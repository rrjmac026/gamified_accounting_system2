<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\HandlesTaskComments;
use App\Models\PerformanceTask;
use App\Models\PerformanceTaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PerformanceTaskCommentController extends Controller
{
    use HandlesTaskComments;

    /**
     * Show all conversations for all of the instructor's tasks (inbox view).
     */
    public function index()
    {
        $instructor = auth()->user()->instructor;

        $tasks = PerformanceTask::where('instructor_id', $instructor->id)
            ->with(['section', 'subject'])
            ->withCount([
                'comments as unread_count' => function ($q) {
                    $q->where('sender_role', 'student')->where('is_read', false);
                },
                'comments',
            ])
            ->latest()
            ->get();

        return view('instructors.performance-tasks.comments.index', compact('tasks'));
    }

    /**
     * Show the full conversation thread for a single task.
     */
    public function show(PerformanceTask $task, Request $request)
    {
        $instructor = auth()->user()->instructor;

        if ($task->instructor_id !== $instructor->id) {
            abort(403);
        }

        $step = $request->integer('step') ?: null;

        // Mark student messages as read
        $this->markCommentsRead($task, 'instructor', $step);

        $comments = $this->getComments($task, $step);

        $stepTitles = [
            1 => 'Analyze Transactions',          2 => 'Journalize Transactions',
            3 => 'Post to Ledger Accounts',       4 => 'Prepare Trial Balance',
            5 => 'Journalize & Post Adjusting Entries', 6 => 'Prepare Adjusted Trial Balance',
            7 => 'Prepare Financial Statements',  8 => 'Journalize & Post Closing Entries',
            9 => 'Prepare Post-Closing Trial Balance', 10 => 'Reverse (Optional Step)',
        ];

        return view('instructors.performance-tasks.comments.show', compact(
            'task', 'comments', 'step', 'stepTitles'
        ));
    }

    /**
     * Post a new comment (or reply) as instructor.
     */
    public function store(Request $request, PerformanceTask $task)
    {
        $instructor = auth()->user()->instructor;

        if ($task->instructor_id !== $instructor->id) {
            abort(403);
        }

        $step = $request->integer('step') ?: null;

        try {
            $this->storeComment($request, $task, 'instructor', $step);

            return redirect()
                ->route('instructors.performance-tasks.comments.show', [
                    'task' => $task->id,
                    'step' => $step,
                ])
                ->with('success', 'Reply sent!');
        } catch (\Exception $e) {
            Log::error('Instructor comment store error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Could not send message. Please try again.');
        }
    }

    /**
     * Delete a comment (soft-delete, instructor can delete own comments only).
     */
    public function destroy(PerformanceTask $task, PerformanceTaskComment $comment)
    {
        $instructor = auth()->user()->instructor;

        if ($task->instructor_id !== $instructor->id || $comment->user_id !== auth()->id()) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}