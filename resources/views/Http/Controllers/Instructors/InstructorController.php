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
        $sections = $instructor->sections()->with('students', 'subjects.performanceTasks')->get();

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

        // 🔹 Recent performance task submissions
        $recentSubmissions = PerformanceTaskSubmission::with(['student', 'task.subject'])
            ->whereHas('task', function ($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id);
            })
            ->whereIn('status', ['submitted', 'late', 'pending'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        // 🔹 Stats
        $stats = [
            'total_subjects' => $instructor->subjects->count(),
            'total_students' => $instructor->sections->flatMap->students->unique('id')->count(),
            'active_tasks' => $instructor->subjects->flatMap->performanceTasks->where('is_active', true)->count(),
            'submissions_pending' => PerformanceTaskSubmission::whereHas('task.subject.instructors', function($q) use ($instructor) {
                $q->where('instructors.id', $instructor->id);
            })
            ->where(function($query) {
                $query->where('status', 'submitted')
                      ->orWhere('status', 'late');
            })
            ->count()
        ];

        // 🔹 Upcoming performance tasks
        $upcomingTasks = $instructor->subjects
            ->flatMap->performanceTasks
            ->where('due_date', '>', now())
            ->where('is_active', true)
            ->sortBy('due_date')
            ->take(5);

        // 🔹 Performance summary per section
        $performanceData = $instructor->sections()
            ->with(['students.performanceTaskSubmissions.task'])
            ->get()
            ->map(function($section) {
                $allSubmissions = $section->students->flatMap->performanceTaskSubmissions;

                $totalTasks = $allSubmissions->count();
                $submittedTasks = $allSubmissions->whereIn('status', ['submitted','late'])->count();
                $avgScore = $allSubmissions->whereNotNull('score')->avg('score');

                return [
                    'section_name' => $section->name,
                    'avg_score' => round($avgScore ?? 0, 1),
                    'submission_rate' => $totalTasks > 0 
                        ? round(($submittedTasks / $totalTasks) * 100, 1) 
                        : 0
                ];
            });

        return view('instructors.dashboard', compact(
            'instructor',
            'stats',
            'recentSubmissions',
            'upcomingTasks',
            'performanceData'
        ));
    }
}
