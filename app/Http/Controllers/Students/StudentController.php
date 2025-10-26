<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Course;
use Carbon\Carbon;

class StudentController extends Controller
{
    // ✅ Only declare this once!
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

        $student->load('sections.course', 'tasks.subject', 'xpTransactions');

        // Calculate total XP from transactions
        $totalXP = $student->xpTransactions()->sum('amount');

        // Calculate statistics
        $stats = $this->calculateStudentStats($student);

        // Calculate level data based on total XP
        $levelData = $this->calculateLevelData($totalXP);

        // Upcoming deadlines and recent grades...
        $upcomingDeadlines = $student->tasks()
            ->with('subject')
            ->where('student_tasks.status', 'assigned')
            ->whereNotNull('student_tasks.due_date')
            ->orderBy('student_tasks.due_date')
            ->take(5)
            ->get();

        $recentGrades = $student->tasks()
            ->with('subject')
            ->where('student_tasks.status', 'graded')
            ->whereNotNull('student_tasks.score')
            ->orderByDesc('student_tasks.graded_at')
            ->take(3)
            ->get();

        $upcomingTasks = $student->tasks()
            ->with('subject')
            ->where('student_tasks.status', 'assigned') // only tasks that are active
            ->whereNotNull('student_tasks.due_date')
            ->where('student_tasks.due_date', '>', now())
            ->orderBy('student_tasks.due_date')
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
        // Total XP
        $totalXP = $student->xpTransactions()->sum('amount') ?? 0;

        // Submitted tasks
        $submittedTasks = $student->tasks->filter(function ($task) {
            $submission = $task->submissions->first();
            return $task->pivot->status === 'submitted' ||
                ($submission && $submission->score !== null);
        })->count();

        // Average score
        $averageScore = $student->tasks()
            ->where('student_tasks.status', 'graded')
            ->whereNotNull('student_tasks.score')
            ->avg('student_tasks.score') ?? 0;

        // Rank calculation: within section
        $sectionIds = $student->sections->pluck('id');
        $studentsInSection = Student::whereHas('sections', fn($q) => $q->whereIn('sections.id', $sectionIds))
                                    ->with('xpTransactions')
                                    ->get();

        $rankedStudents = $studentsInSection->sortByDesc(fn($s) => $s->xpTransactions->sum('amount'))
                                            ->values();

        $rank = $rankedStudents->search(fn($s) => $s->id === $student->id) + 1;

        return [
            'total_xp' => $totalXP,
            'submitted_tasks' => $submittedTasks,
            'average_score' => round($averageScore, 1),
            'rank' => $rank,
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


    
    /**
     * Get section information safely
     */
    private function getSectionInfo(Student $student)
    {
        // Get the first section assigned to the student through the pivot table
        $section = $student->sections()->with('course')->first();
        
        if ($section) {
            return [
                'course_name' => $section->course->name ?? 'No Course',
                'section_name' => $section->name
            ];
        }
        
        return [
            'course_name' => null,
            'section_name' => null
        ];
    }
}