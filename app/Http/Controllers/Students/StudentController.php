<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\PerformanceTask;
use Carbon\Carbon;

class StudentController extends Controller
{
    protected function getAuthenticatedStudent()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            abort(403, 'Unauthorized.');
        }

        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            abort(404, 'Student profile not found.');
        }

        return $student;
    }

    public function dashboard()
    {
        $student = $this->getAuthenticatedStudent();

        // Eager load all necessary relationships to prevent N+1 queries
        $student->load([
            'sections.course',
            'xpTransactions'
        ]);

        // Calculate total XP from transactions
        $totalXP = $student->xpTransactions()->sum('amount');

        // Calculate statistics
        $stats = $this->calculateStudentStats($student);

        // Calculate level data based on total XP
        $levelData = $this->calculateLevelData($totalXP);

        // Get upcoming performance tasks with eager loading
        $upcomingDeadlines = PerformanceTask::whereHas('students', function ($query) use ($student) {
                $query->where('students.id', $student->id)
                      ->where('performance_task_student.status', 'assigned');
            })
            ->with('subject') // Eager load subject to prevent N+1
            ->whereNotNull('due_date')
            ->where('due_date', '>', now())
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // Get recent graded performance tasks
        $recentGrades = PerformanceTask::whereHas('students', function ($query) use ($student) {
                $query->where('students.id', $student->id)
                      ->where('performance_task_student.status', 'graded')
                      ->whereNotNull('performance_task_student.score');
            })
            ->with([
                'subject', // Eager load subject
                'students' => function ($query) use ($student) {
                    $query->where('students.id', $student->id); // Only load current student's pivot
                }
            ])
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        // Get upcoming performance tasks (different from deadlines - shows all upcoming)
        $upcomingTasks = PerformanceTask::whereHas('students', function ($query) use ($student) {
                $query->where('students.id', $student->id)
                      ->whereIn('performance_task_student.status', ['assigned', 'in_progress']);
            })
            ->with('subject') // Eager load subject
            ->whereNotNull('due_date')
            ->where('due_date', '>', now())
            ->orderBy('due_date')
            ->take(5)
            ->get();

        return view('students.dashboard', compact(
            'student',
            'stats',
            'upcomingDeadlines',
            'recentGrades',
            'levelData',
            'upcomingTasks'
        ));
    }

    /**
     * Calculate student statistics
     */
    private function calculateStudentStats(Student $student)
    {
        // Total XP (already calculated in dashboard)
        $totalXP = $student->xpTransactions()->sum('amount') ?? 0;

        // Submitted performance tasks
        $submittedTasks = PerformanceTask::whereHas('students', function ($query) use ($student) {
            $query->where('students.id', $student->id)
                  ->whereIn('performance_task_student.status', ['submitted', 'graded']);
        })->count();

        // Average score from performance tasks
        $averageScore = \DB::table('performance_task_student')
            ->where('student_id', $student->id)
            ->where('status', 'graded')
            ->whereNotNull('score')
            ->avg('score') ?? 0;

        // Rank calculation: within section
        $sectionIds = $student->sections->pluck('id');
        
        $studentsInSection = Student::whereHas('sections', function ($q) use ($sectionIds) {
                $q->whereIn('sections.id', $sectionIds);
            })
            ->withSum('xpTransactions', 'amount')
            ->orderByDesc('xp_transactions_sum_amount')
            ->get();

        $rank = $studentsInSection->search(function ($s) use ($student) {
            return $s->id === $student->id;
        }) + 1;

        return [
            'total_xp' => $totalXP,
            'submitted_tasks' => $submittedTasks,
            'average_score' => round($averageScore, 1),
            'rank' => $rank ?: 1,
        ];
    }

    /**
     * Calculate level progression data dynamically
     */
    private function calculateLevelData(int $xp)
    {
        $currentLevel = floor($xp / 1000) + 1;
        $xpInCurrentLevel = $xp % 1000;
        $progressPercentage = $xpInCurrentLevel / 10; // percent

        return [
            'current_level' => $currentLevel,
            'xp_in_current_level' => $xpInCurrentLevel,
            'progress_percentage' => $progressPercentage,
            'xp_to_next_level' => 1000 - $xpInCurrentLevel
        ];
    }
}