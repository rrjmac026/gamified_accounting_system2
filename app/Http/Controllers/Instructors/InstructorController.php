<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\Subject;
use App\Models\PerformanceTask;
use App\Models\PerformanceTaskSubmission;
use App\Models\Student;
use App\Models\Section;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InstructorController extends Controller
{
    /**
     * Display a listing of instructors with filtering and pagination.
     */
    public function index(Request $request)
    {
        $query = Instructor::with('user');
        $instructors = $query->paginate(15);

        return view('instructors.index', compact('instructors'));
    }

    public function mySections()
    {
        $instructor = Auth::user()->instructor;
        $sections = $instructor->sections()->with('course', 'students')->get();

        return view('instructors.sections.index', compact('sections'));
    }

    public function dashboard()
    {
        $instructor = auth()->user()->instructor;
        
        // Load relationships
        $instructor->load([
            'sections.students',
            'subjects.performanceTasks',
            'subjects.sections'
        ]);

        // 🔹 Recent performance task submissions (FIXED)
        // Get recent completed step submissions from students
        $recentSubmissions = PerformanceTaskSubmission::with(['student.user', 'task.subject'])
            ->whereHas('task', function ($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id);
            })
            ->where('status', 'correct') // Only show completed (correct) steps
            ->latest('updated_at')
            ->take(20) // Get more to ensure we have enough after grouping
            ->get()
            ->groupBy(function($submission) {
                // Group by student + task to get latest completed step per student per task
                return $submission->student_id . '-' . $submission->task_id;
            })
            ->map(function($group) {
                // Get the most recent completed submission for each student-task combination
                return $group->sortByDesc('updated_at')->first();
            })
            ->sortByDesc('updated_at')
            ->take(5) // Take only 5 for display
            ->values();

        // 🔹 Stats (FIXED)
        $stats = [
            'total_subjects' => $instructor->subjects->count(),
            'total_students' => $instructor->sections->flatMap->students->unique('id')->count(),
            'active_tasks' => PerformanceTask::where('instructor_id', $instructor->id)->count(),
            'submissions_pending' => PerformanceTaskSubmission::whereHas('task', function($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id);
            })
            ->where(function($query) {
                $query->where('status', 'wrong') // Steps marked as wrong need review
                      ->orWhere('needs_feedback', true); // Steps that explicitly need feedback
            })
            ->count()
        ];

        // 🔹 Upcoming performance tasks
        $upcomingTasks = PerformanceTask::where('instructor_id', $instructor->id)
            ->where('due_date', '>', now())
            ->with('subject')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        // 🔹 Performance summary per section (FIXED)
        $performanceData = $instructor->sections()
            ->with(['students.user'])
            ->get()
            ->map(function($section) use ($instructor) {
                // Get all performance tasks for this instructor
                $instructorTasks = PerformanceTask::where('instructor_id', $instructor->id)
                    ->pluck('id');

                // Get student IDs in this section
                $studentIds = $section->students->pluck('id');

                if ($studentIds->isEmpty() || $instructorTasks->isEmpty()) {
                    return [
                        'section_name' => $section->name,
                        'avg_score' => 0,
                        'submission_rate' => 0,
                        'total_students' => 0,
                        'active_students' => 0
                    ];
                }

                // Get all submissions for students in this section
                $submissions = PerformanceTaskSubmission::whereIn('student_id', $studentIds)
                    ->whereIn('task_id', $instructorTasks)
                    ->get();

                // Calculate average score from correct steps
                $correctSteps = $submissions->where('status', 'correct');
                $avgScore = $correctSteps->isNotEmpty() 
                    ? $correctSteps->avg('score') 
                    : 0;

                // Calculate submission rate
                // Total possible steps = students × tasks × 10 steps per task
                $totalPossibleSteps = $studentIds->count() * $instructorTasks->count() * 10;
                
                // Completed steps = steps with 'correct' status
                $completedSteps = $correctSteps->count();
                
                $submissionRate = $totalPossibleSteps > 0 
                    ? ($completedSteps / $totalPossibleSteps) * 100 
                    : 0;

                // Count active students (students who have submitted at least one step)
                $activeStudents = $submissions->unique('student_id')->count();

                return [
                    'section_name' => $section->name,
                    'avg_score' => round($avgScore, 1),
                    'submission_rate' => round($submissionRate, 1),
                    'total_students' => $studentIds->count(),
                    'active_students' => $activeStudents
                ];
            })
            ->sortByDesc('submission_rate')
            ->values();

        return view('instructors.dashboard', compact(
            'instructor',
            'stats',
            'recentSubmissions',
            'upcomingTasks',
            'performanceData'
        ));
    }
}