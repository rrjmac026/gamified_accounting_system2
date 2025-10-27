<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Badge;
use App\Models\TaskSubmission;
use App\Models\PerformanceLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\SystemNotification;


class TaskController extends Controller
{
    /**
     * List all tasks assigned to the logged-in student.
     */
    public function index()
    {
        $student = auth()->user()->student;

        // Load tasks assigned to this student
        $tasks = $student->tasks()->with('subject')->get();

        // Update statuses for overdue tasks
        foreach ($tasks as $task) {
            $pivot = $task->students()->where('student_id', $student->id)->first()->pivot;

            if (in_array($pivot->status, ['assigned', 'in_progress']) && $task->due_date < now()) {
                $task->students()->updateExistingPivot($student->id, [
                    'status' => 'missing',
                ]);
            }
        }
        

        return view('students.tasks.index', compact('tasks'));
    }


    /**
     * Show details of a single assigned task.
     */
    public function show(Task $task)
    {
        $student = Auth::user()->student;

        // Get the student-task pivot (status, score, etc.)
        $studentTask = $student->tasks()
        ->where('tasks.id', $task->id)
        ->withPivot(['score', 'xp_earned', 'status', 'was_late', 'submitted_at', 'graded_at', 'penalty'])
        ->firstOrFail();

        // Load relations for displaying task details
        $task->load(['subject', 'instructor.user', 'questions']);

        // Get the student’s latest submission
        $submission = $task->submissions()
            ->where('student_id', $student->id)
            ->latest('submitted_at')
            ->first();

        return view('students.tasks.show', compact('task', 'studentTask', 'submission'));
    }


    /**
     * Store student submission (file upload or answers).
     */
    /**
 * Store student submission (file upload or answers).
 */
    public function submit(Request $request, Task $task)
    {
        $student = Auth::user()->student;

        if (!$student->tasks()->where('tasks.id', $task->id)->exists()) {
            abort(403, 'This task is not assigned to you.');
        }

        $validated = $request->validate([
            'file' => 'nullable|file|max:2048',
            'answers' => 'nullable|array',
        ]);

        $filePath = $request->hasFile('file')
            ? $request->file('file')->store('submissions', 'public')
            : null;

        // === Late handling ===
        $isLate = $task->due_date && now()->gt($task->due_date);

        // If submission is late
        if ($isLate) {
            // If no late_until set OR already past late_until → reject
            if (!$task->late_until || now()->gt($task->late_until)) {
                return back()->withErrors(['error' => 'Late submission is not allowed for this task.']);
            }
        }

        // If late submission is allowed, apply penalty if set
        $penaltyApplied = $isLate && $task->late_penalty > 0;

        // Get next attempt number
        $lastAttempt = TaskSubmission::where('task_id', $task->id)
            ->where('student_id', $student->id)
            ->max('attempt_number');
        $attemptNumber = $lastAttempt ? $lastAttempt + 1 : 1;

        // Create or update the task submission
        $submission = TaskSubmission::create([
            'task_id' => $task->id,
            'student_id' => $student->id,
            'submission_data' => $validated['answers'] ?? [],
            'file_path' => $filePath,
            'status' => $isLate ? 'late' : 'submitted',
            'submitted_at' => now(),
            'attempt_number' => $attemptNumber,
        ]);

        // Update the pivot record in student_tasks
        $student->tasks()->updateExistingPivot($task->id, [
            'status' => 'submitted',
            'submitted_at' => now(),
            'was_late' => $isLate,
            'penalty' => $penaltyApplied ? $task->late_penalty : null
        ]);

        PerformanceLog::create([
            'student_id' => $student->id,
            'subject_id' => $task->subject_id,
            'task_id' => $task->id,
            'performance_metric' => 'score', // or 'completion' if not graded
            'value' => $submission->score ?? 0, // or whatever metric you calculate
            'recorded_at' => now(),
        ]);

        SystemNotification::create([
            'user_id' => $task->instructor->user->id, // notify the instructor
            'title'   => 'Task Submitted',
            'message' => "{$student->name} submitted {$task->title}.",
            'type'    => 'success',
            'is_read' => false,
        ]);

        
        
        $this->checkAndAssignBadges($student);
        // FIXED: Add proper redirect with success message
        $message = $isLate 
            ? 'Late submission completed successfully' . ($penaltyApplied ? ' (penalty applied)' : '') 
            : 'Task submitted successfully!';
        
        return redirect()->route('students.tasks.show', $task)
            ->with('success', $message);
    }

    protected function checkAndAssignBadges($student)
    {
        // Example: total XP earned so far
        $totalXp = $student->xpTransactions()->sum('amount');

        // Find all active badges that match criteria
        $eligibleBadges = Badge::where('is_active', true)
            ->where('xp_threshold', '<=', $totalXp)
            ->get();

        foreach ($eligibleBadges as $badge) {
            // Attach if not already earned
            $student->badges()->syncWithoutDetaching([
                $badge->id => ['earned_at' => now()]
            ]);
        }
    }

}
