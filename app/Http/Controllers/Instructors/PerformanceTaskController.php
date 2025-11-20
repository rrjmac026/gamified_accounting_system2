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
            'xp_reward'           => 'required|integer|min:0',
            'max_attempts'        => 'required|integer|min:1',
            'subject_id'          => 'required|exists:subjects,id',
            'section_id'          => 'required|exists:sections,id',
            'due_date'            => 'required|date|after:now',
            'late_until'          => 'nullable|date|after:due_date',
            'max_score'           => 'required|integer|min:1',
            'deduction_per_error' => 'required|integer|min:0',
        ]);

        $instructor = Auth::user()->instructor;

        // Create the performance task
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
        ]);

        // Load the section with students
        $task->load('section.students.user', 'subject', 'section');

        // ✅ Log performance task creation
        $this->logActivity('created performance task', $task->id, [
            'title' => $task->title,
            'subject' => $task->subject->subject_name ?? 'N/A',
            'section' => $task->section->section_name ?? 'N/A',
            'xp_reward' => $task->xp_reward,
            'max_score' => $task->max_score,
            'due_date' => $task->due_date,
            'students_notified' => $task->section->students->count(),
        ]);

        // Notify all students in the section
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

        return redirect()->route('instructors.performance-tasks.index')
            ->with('success', 'Performance task created successfully.');
    }

    /**
     * Display a specific performance task
     */
    public function show(PerformanceTask $task)
    {
        // Authorize: ensure the instructor owns this task
        if ($task->instructor_id !== Auth::user()->instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        $task->load([
            'subject',
            'section.students',
            'instructor'
        ]);

        // ✅ Log viewing performance task
        $this->logActivity('viewed performance task', $task->id, [
            'title' => $task->title,
        ]);

        return view('instructors.performance-tasks.show', compact('task'));
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
        // Authorize: ensure the instructor owns this task
        if ($task->instructor_id !== Auth::user()->instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'xp_reward'           => 'required|integer|min:0',
            'max_attempts'        => 'required|integer|min:1',
            'subject_id'          => 'required|exists:subjects,id',
            'section_id'          => 'required|exists:sections,id',
            'due_date'            => 'required|date|after:now',
            'late_until'          => 'nullable|date|after:due_date',
            'max_score'           => 'required|integer|min:1',
            'deduction_per_error' => 'required|integer|min:0',
        ]);

        // ✅ Capture changes before update
        $changes = [];
        foreach ($validated as $key => $value) {
            if ($task->{$key} != $value) {
                $changes[$key] = [
                    'old' => $task->{$key},
                    'new' => $value,
                ];
            }
        }

        $task->update($validated);

        // Load the section with students
        $task->load('section.students.user', 'subject', 'section');

        // ✅ Log performance task update
        $this->logActivity('updated performance task', $task->id, [
            'title' => $task->title,
            'subject' => $task->subject->subject_name ?? 'N/A',
            'section' => $task->section->section_name ?? 'N/A',
            'changes' => $changes,
            'students_notified' => $task->section->students->count(),
        ]);

        // Notify students about updates
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