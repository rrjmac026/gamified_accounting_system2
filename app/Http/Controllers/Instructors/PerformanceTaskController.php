<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PerformanceTask;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Student;
use App\Models\SystemNotification;
use App\Models\ActivityLog;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PerformanceTaskController extends Controller
{
    /**
     * Show list of performance tasks created by this instructor
     */
    public function index()
    {
        $instructorId = Auth::user()->instructor->id;

        $tasks = PerformanceTask::with(['section', 'instructor', 'subject'])
            ->where('instructor_id', $instructorId)
            ->latest('created_at')
            ->get();

        return view('instructors.performance-tasks.index', compact('tasks'));
    }

    /**
     * Show form to create a new performance task
     */
    public function create()
    {
        $instructor = Auth::user()->instructor;
        $subjects = $instructor->subjects()->with('sections')->get();
        $sections = $instructor->sections;

        return view('instructors.performance-tasks.create', compact('subjects', 'sections'));
    }

    /**
     * Store a newly created performance task
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'subject_id'          => 'required|exists:subjects,id',
            'section_id'          => 'required|exists:sections,id',
            'max_score'           => 'required|numeric|min:1',
            'deduction_per_error' => 'required|numeric|min:0',
            'due_date'            => 'required|date',
            'late_until'          => 'nullable|date|after:due_date',
            'xp_reward'           => 'required|numeric|min:0',
            'max_attempts'        => 'required|integer|min:1',
            'enabled_steps'       => 'required|array|min:1',
            'enabled_steps.*'     => 'integer|between:1,10',
        ]);

        $instructor = Auth::user()->instructor;

        // ✅ Build enabled_steps BEFORE create()
        $enabledSteps = collect($validated['enabled_steps'])
            ->map(fn($s) => (int) $s)
            ->filter(fn($s) => $s >= 1 && $s <= 10)
            ->sort()
            ->values()
            ->toArray();

        $task = PerformanceTask::create([
            'title'               => $validated['title'],
            'description'         => $validated['description'] ?? null,
            'xp_reward'           => $validated['xp_reward'],
            'max_attempts'        => $validated['max_attempts'],
            'subject_id'          => $validated['subject_id'],
            'section_id'          => $validated['section_id'],
            'instructor_id'       => $instructor->id,
            'due_date'            => $validated['due_date'],
            'late_until'          => $validated['late_until'] ?? null,
            'max_score'           => $validated['max_score'],
            'deduction_per_error' => $validated['deduction_per_error'],
            'enabled_steps'       => $enabledSteps,   // ✅ no longer undefined
        ]);

        $task->load('section.students.user', 'subject', 'section');

        $this->logActivity('created performance task', $task->id, [
            'title'              => $task->title,
            'subject'            => $task->subject->subject_name ?? 'N/A',
            'section'            => $task->section->section_name ?? 'N/A',
            'xp_reward'          => $task->xp_reward,
            'max_score'          => $task->max_score,
            'due_date'           => $task->due_date,
            'enabled_steps'      => $enabledSteps,
            'students_notified'  => $task->section->students->count(),
        ]);

        foreach ($task->section->students as $student) {
            SystemNotification::create([
                'user_id' => $student->user->id,
                'title'   => 'New Performance Task Available',
                'message' => "Your instructor has assigned a new performance task: '{$task->title}'.",
                'type'    => 'info',
                'is_read' => false,
                'link'    => route('students.performance-tasks.progress', ['taskId' => $task->id]),
            ]);
        }

        return redirect()->route('instructors.performance-tasks.answer-sheets.index', $task)
            ->with('success', 'Performance task created! Now add exercises to your steps.');
    }

    /**
     * Display a specific performance task
     */
    public function show(PerformanceTask $task)
    {
        if ($task->instructor_id !== Auth::user()->instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        $task->load([
            'subject',
            'section.students.user',
            'instructor'
        ]);

        // ✅ Load submissions keyed by student_id
        $submissions = \App\Models\PerformanceTaskSubmission::where('task_id', $task->id)
            ->get()
            ->groupBy('student_id');

        $pivotData = \Illuminate\Support\Facades\DB::table('performance_task_student')
            ->where('performance_task_id', $task->id)
            ->get()
            ->keyBy('student_id');

        $enabledStepsCount = count($task->enabled_steps_list);

        $studentStats = $task->section->students->mapWithKeys(function ($student) use ($submissions, $pivotData, $enabledStepsCount) {
            $studentSubmissions = $submissions->get($student->id, collect());
            $pivot = $pivotData->get($student->id);

            $completedSteps = $studentSubmissions->pluck('step')->unique()->count();
            $totalScore     = $studentSubmissions->sum('score');
            $status         = $pivot->status ?? ($completedSteps === 0 ? 'not_started' : 'in_progress');

            return [$student->id => [
                'completed_steps' => $completedSteps,
                'total_steps'     => $enabledStepsCount,
                'score'           => round($totalScore, 2),
                'status'          => $status,
            ]];
        });

        // ✅ Quick stats
        $totalStudents    = $task->section->students->count();
        $submittedCount   = $studentStats->filter(fn($s) => in_array($s['status'], ['submitted', 'graded', 'in_progress']) || $s['completed_steps'] > 0)->count();
        $avgScore         = $studentStats->avg('score');
        $completionRate   = $totalStudents > 0 ? round(($studentStats->filter(fn($s) => $s['completed_steps'] >= $s['total_steps'])->count() / $totalStudents) * 100) : 0;

        $this->logActivity('viewed performance task', $task->id, ['title' => $task->title]);

        return view('instructors.performance-tasks.show', compact('task', 'studentStats', 'submittedCount', 'avgScore', 'completionRate'));
    }

    /**
     * Show form to edit a performance task
     */
    public function edit(PerformanceTask $task)
    {
        // Authorize: ensure the instructor owns this task
        if ($task->instructor_id !== Auth::user()->instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        $instructor = Auth::user()->instructor;
        $subjects = $instructor->subjects;
        $sections = $instructor->sections;

        // Load relationships for dropdowns
        $task->load('section');

        return view('instructors.performance-tasks.edit', compact('task', 'subjects', 'sections'));
    }

    /**
     * Update a performance task
     */
    public function update(Request $request, PerformanceTask $task)
    {
        if ($task->instructor_id !== Auth::user()->instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'subject_id'          => 'required|exists:subjects,id',
            'section_id'          => 'required|exists:sections,id',
            'max_score'           => 'required|numeric|min:1',
            'deduction_per_error' => 'required|numeric|min:0',
            'due_date'            => 'required|date',
            'late_until'          => 'nullable|date|after:due_date',
            'xp_reward'           => 'required|numeric|min:0',
            'max_attempts'        => 'required|integer|min:1',
            'enabled_steps'       => 'required|array|min:1',
            'enabled_steps.*'     => 'integer|between:1,10',
        ]);

        // ✅ Build enabled_steps BEFORE update() so the diff check captures it too
        $enabledSteps = collect($validated['enabled_steps'])
            ->map(fn($s) => (int) $s)
            ->filter(fn($s) => $s >= 1 && $s <= 10)
            ->sort()
            ->values()
            ->toArray();

        // Replace the raw array in $validated with the sanitized version
        $validated['enabled_steps'] = $enabledSteps;

        // ✅ Diff check — compare old vs new for all validated fields
        $changes = [];
        foreach ($validated as $key => $value) {
            $old = $task->{$key};

            // For array fields (like enabled_steps), compare as sorted arrays
            $isDifferent = is_array($value)
                ? $old !== $value   // model casts already return array
                : $old != $value;

            if ($isDifferent) {
                $changes[$key] = [
                    'old' => $old,
                    'new' => $value,
                ];
            }
        }

        $task->update($validated);

        $task->load('section.students.user', 'subject', 'section');

        $this->logActivity('updated performance task', $task->id, [
            'title'             => $task->title,
            'subject'           => $task->subject->subject_name ?? 'N/A',
            'section'           => $task->section->section_name ?? 'N/A',
            'changes'           => $changes,
            'students_notified' => $task->section->students->count(),
        ]);

        foreach ($task->section->students as $student) {
            SystemNotification::create([
                'user_id' => $student->user->id,
                'title'   => 'Performance Task Updated',
                'message' => "The performance task '{$task->title}' has been updated.",
                'type'    => 'warning',
                'is_read' => false,
                'link'    => route('students.performance-tasks.progress', ['taskId' => $task->id]),
            ]);
        }

        return redirect()->route('instructors.performance-tasks.index')
            ->with('success', 'Performance task updated successfully.');
    }

    /**
     * Remove a performance task
     */
    public function destroy(PerformanceTask $task)
    {
        if ($task->instructor_id !== Auth::user()->instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        $task->load('section.students.user', 'subject', 'section');
        $taskTitle = $task->title;
        $taskId = $task->id;
        $students = $task->section->students ?? collect();

        // ✅ Capture task data before deletion
        $taskData = [
            'title' => $taskTitle,
            'subject' => $task->subject->subject_name ?? 'N/A',
            'section' => $task->section->section_name ?? 'N/A',
            'xp_reward' => $task->xp_reward,
            'max_score' => $task->max_score,
            'students_affected' => $students->count(),
        ];

        $task->delete();

        // ✅ Log performance task deletion
        $this->logActivity('deleted performance task', $taskId, $taskData);

        if ($students->isNotEmpty()) {
            foreach ($students as $student) {
                SystemNotification::create([
                    'user_id' => $student->user->id,
                    'title'   => 'Performance Task Removed',
                    'message' => "The performance task '{$taskTitle}' has been removed.",
                    'type'    => 'info',
                    'is_read' => false,
                    'link'    => null,
                ]);
            }
        }

        return redirect()->route('instructors.performance-tasks.index')
            ->with('success', 'Performance task deleted successfully.');
    }

    /**
     * Simple logging helper for performance task actions
     */
    protected function logActivity(string $action, ?int $taskId, array $details = []): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => 'PerformanceTask',
            'model_id' => $taskId,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'performed_at' => now(),
        ]);
    }
}