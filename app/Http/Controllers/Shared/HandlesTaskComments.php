<?php

namespace App\Http\Controllers\Shared;

use App\Models\PerformanceTask;
use App\Models\PerformanceTaskComment;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Reusable comment logic shared between student and instructor controllers.
 */
trait HandlesTaskComments
{
    /**
     * Return all top-level comments (with replies) for a task,
     * optionally filtered by step.
     */
    protected function getComments(PerformanceTask $task, ?int $step = null)
    {
        return PerformanceTaskComment::with(['sender', 'replies.sender'])
            ->where('performance_task_id', $task->id)
            ->topLevel()
            ->when($step, fn($q) => $q->where('step', $step))
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Persist a new comment and fire a notification to the other party.
     */
    protected function storeComment(
        Request $request,
        PerformanceTask $task,
        string $senderRole,      // 'student' | 'instructor'
        ?int $step = null
    ): PerformanceTaskComment {
        $request->validate([
            'body'      => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:performance_task_comments,id',
        ]);

        $comment = PerformanceTaskComment::create([
            'performance_task_id' => $task->id,
            'user_id'             => auth()->id(),
            'parent_id'           => $request->input('parent_id'),
            'body'                => $request->input('body'),
            'sender_role'         => $senderRole,
            'step'                => $step,
            'is_read'             => false,
        ]);

        // Fire notification to the other party
        $this->notifyCommentRecipient($task, $comment, $senderRole, $step);

        return $comment;
    }

    /**
     * Mark all comments sent to the viewer as read.
     */
    protected function markCommentsRead(PerformanceTask $task, string $viewerRole, ?int $step = null): void
    {
        PerformanceTaskComment::where('performance_task_id', $task->id)
            ->where('sender_role', '!=', $viewerRole)
            ->where('is_read', false)
            ->when($step, fn($q) => $q->where('step', $step))
            ->update(['is_read' => true]);
    }

    /**
     * Count unread messages for the given viewer role on a task.
     */
    protected function unreadCount(PerformanceTask $task, string $viewerRole, ?int $step = null): int
    {
        return PerformanceTaskComment::where('performance_task_id', $task->id)
            ->where('sender_role', '!=', $viewerRole)
            ->where('is_read', false)
            ->when($step, fn($q) => $q->where('step', $step))
            ->count();
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function notifyCommentRecipient(
        PerformanceTask $task,
        PerformanceTaskComment $comment,
        string $senderRole,
        ?int $step
    ): void {
        try {
            $stepTitles = [
                1 => 'Analyze Transactions',          2 => 'Journalize Transactions',
                3 => 'Post to Ledger Accounts',       4 => 'Prepare Trial Balance',
                5 => 'Journalize & Post Adjusting Entries', 6 => 'Prepare Adjusted Trial Balance',
                7 => 'Prepare Financial Statements',  8 => 'Journalize & Post Closing Entries',
                9 => 'Prepare Post-Closing Trial Balance', 10 => 'Reverse (Optional Step)',
            ];

            $stepInfo  = $step ? " (Step {$step}: " . ($stepTitles[$step] ?? "Step {$step}") . ")" : '';
            $senderName = auth()->user()->name ?? 'Someone';

            if ($senderRole === 'student') {
                // Notify the instructor
                $instructorUser = $task->instructor->user ?? null;
                if ($instructorUser) {
                    SystemNotification::create([
                        'user_id' => $instructorUser->id,
                        'title'   => 'New Student Comment',
                        'message' => "{$senderName} commented on \"{$task->title}\"{$stepInfo}.",
                        'type'    => 'info',
                        'is_read' => false,
                        'link'    => route('instructors.performance-tasks.comments.show', [
                            'task' => $task->id,
                        ]),
                    ]);
                }
            } else {
                // Notify all students in the section — or just the thread participant
                // Here we target the student who owns the thread (if reply) or all students
                $task->load('section.students.user');
                foreach ($task->section->students ?? [] as $student) {
                    SystemNotification::create([
                        'user_id' => $student->user->id,
                        'title'   => 'Instructor Replied',
                        'message' => "Your instructor commented on \"{$task->title}\"{$stepInfo}.",
                        'type'    => 'info',
                        'is_read' => false,
                        'link'    => route('students.performance-tasks.comments.show', [
                            'task' => $task->id,
                        ]),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Comment notification failed: ' . $e->getMessage());
        }
    }
}