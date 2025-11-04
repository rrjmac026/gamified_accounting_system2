<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\PerformanceTask;
use Carbon\Carbon;

class StudentController extends Controller
{
    /**
     * Get the authenticated student or fail
     */
    protected function getAuthenticatedStudent()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            abort(403, 'Unauthorized access.');
        }

        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            abort(404, 'Student profile not found.');
        }

        return $student;
    }

    /**
     * Display the student dashboard
     */
    public function dashboard()
    {
        $student = $this->getAuthenticatedStudent();
        
        // Eager load relationships
        $student->load(['sections.course', 'xpTransactions']);

        // Get student's section IDs
        $sectionIds = $student->sections->pluck('id');

        // Calculate all stats
        $stats = $this->calculateStudentStats($student);
        
        // Get dashboard data
        $upcomingDeadlines = $this->getUpcomingDeadlines($student, $sectionIds);
        $recentGrades = $this->getRecentGrades($student);
        $levelData = $this->calculateLevelData($stats['total_xp']);

        return view('students.dashboard', compact(
            'student',
            'stats',
            'upcomingDeadlines', 
            'recentGrades',
            'levelData',
        ))->with('upcomingTasks', $upcomingDeadlines);
    }

    /**
     * Calculate all student statistics
     */
    private function calculateStudentStats(Student $student): array
    {
        $totalXP = $student->xpTransactions()->sum('amount');
        
        // Check both pivot table and submissions table
        $pivotStats = DB::table('performance_task_student')
            ->where('student_id', $student->id)
            ->selectRaw('
                COUNT(CASE WHEN status IN ("submitted", "graded") THEN 1 END) as submitted_count,
                AVG(CASE WHEN status = "graded" AND score IS NOT NULL THEN score END) as avg_score
            ')
            ->first();

        // Fallback to submissions table if pivot is empty
        if (($pivotStats->submitted_count ?? 0) == 0) {
            $submissionStats = DB::table('performance_task_submissions')
                ->where('student_id', $student->id)
                ->selectRaw('
                    COUNT(DISTINCT task_id) as submitted_count,
                    AVG(CASE WHEN status = "graded" AND score IS NOT NULL THEN score END) as avg_score
                ')
                ->first();
            
            return [
                'total_xp' => $totalXP,
                'submitted_tasks' => $submissionStats->submitted_count ?? 0,
                'average_score' => round($submissionStats->avg_score ?? 0, 2),
                'rank' => $this->calculateStudentRank($student, $totalXP)
            ];
        }

        return [
            'total_xp' => $totalXP,
            'submitted_tasks' => $pivotStats->submitted_count ?? 0,
            'average_score' => round($pivotStats->avg_score ?? 0, 2),
            'rank' => $this->calculateStudentRank($student, $totalXP)
        ];
    }

    /**
     * Get upcoming deadlines for the student
     */
    private function getUpcomingDeadlines(Student $student, $sectionIds, int $limit = 5)
    {
        if ($sectionIds->isEmpty()) {
            return collect([]);
        }

        // Get tasks that are either:
        // 1. Not yet submitted in pivot table
        // 2. Not yet submitted in submissions table
        $submittedTaskIds = DB::table('performance_task_student')
            ->where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->pluck('performance_task_id');

        // Also check submissions table
        $submittedViaSubmissions = DB::table('performance_task_submissions')
            ->where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->distinct()
            ->pluck('task_id');

        $allSubmittedIds = $submittedTaskIds->merge($submittedViaSubmissions)->unique();

        $tasks = PerformanceTask::with(['subject', 'instructor'])
            ->whereIn('section_id', $sectionIds)
            ->where('due_date', '>', now())
            ->whereNotIn('id', $allSubmittedIds)
            ->orderBy('due_date', 'asc')
            ->take($limit)
            ->get();

        return $tasks;
    }

    /**
     * Get recent graded tasks for the student
     * Checks both pivot table and submissions table
     */
    private function getRecentGrades(Student $student, int $limit = 5)
    {
        // Try to get from pivot table first
        $pivotGrades = DB::table('performance_task_student')
            ->where('student_id', $student->id)
            ->where('status', 'graded')
            ->whereNotNull('score')
            ->orderBy('graded_at', 'desc')
            ->select('performance_task_id as task_id', 'score', 'xp_earned', 'graded_at', 'feedback')
            ->limit($limit)
            ->get();

        // If no pivot grades, check submissions table
        if ($pivotGrades->isEmpty()) {
            $submissionGrades = DB::table('performance_task_submissions')
                ->where('student_id', $student->id)
                ->where('status', 'graded')
                ->whereNotNull('score')
                ->orderBy('updated_at', 'desc')
                ->select('task_id', 'score', DB::raw('NULL as xp_earned'), 'updated_at as graded_at', 'remarks as feedback')
                ->limit($limit)
                ->get();
            
            $pivotGrades = $submissionGrades;
        }

        if ($pivotGrades->isEmpty()) {
            return collect([]);
        }

        // Get task IDs
        $taskIds = $pivotGrades->pluck('task_id')->unique();

        // Load the full task models
        $tasks = PerformanceTask::with(['subject', 'instructor'])
            ->whereIn('id', $taskIds)
            ->get()
            ->keyBy('id');

        // Attach pivot data to each task
        return $pivotGrades->map(function($grade) use ($tasks) {
            $task = $tasks->get($grade->task_id);
            if ($task) {
                $task->pivot = (object)[
                    'score' => $grade->score,
                    'xp_earned' => $grade->xp_earned,
                    'graded_at' => $grade->graded_at,
                    'feedback' => $grade->feedback
                ];
                return $task;
            }
            return null;
        })->filter()->values();
    }

    /**
     * Calculate student's rank within their sections
     */
    private function calculateStudentRank(Student $student, int $studentTotalXp): int
    {
        $sectionIds = $student->sections->pluck('id');
        
        if ($sectionIds->isEmpty()) {
            return 1;
        }
        
        // More efficient rank calculation using subquery
        $studentsAbove = Student::whereHas('sections', function($q) use ($sectionIds) {
                $q->whereIn('sections.id', $sectionIds);
            })
            ->where('id', '!=', $student->id)
            ->whereHas('xpTransactions')
            ->get()
            ->filter(function($s) use ($studentTotalXp) {
                return $s->xpTransactions()->sum('amount') > $studentTotalXp;
            })
            ->count();
        
        return $studentsAbove + 1;
    }

    /**
     * Calculate level progression data dynamically
     */
    private function calculateLevelData(int $xp): array
    {
        $xpPerLevel = 1000;
        $currentLevel = floor($xp / $xpPerLevel) + 1;
        $xpInCurrentLevel = $xp % $xpPerLevel;
        $progressPercentage = ($xpInCurrentLevel / $xpPerLevel) * 100;

        return [
            'current_level' => $currentLevel,
            'xp_in_current_level' => $xpInCurrentLevel,
            'progress_percentage' => round($progressPercentage, 1),
            'xp_to_next_level' => $xpPerLevel - $xpInCurrentLevel
        ];
    }
}