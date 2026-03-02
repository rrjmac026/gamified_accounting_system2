<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\HandlesTaskComments;
use App\Models\PerformanceTask;
use App\Models\PerformanceTaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentPerformanceTaskCommentController extends Controller
{
    use HandlesTaskComments;

    /**
     * Show the conversation thread for a task (student view).
     */
    public function show(PerformanceTask $task, Request $request)
    {
        $student = auth()->user()->student;

        // Confirm task is assigned to this student's section
        abort_unless(
            $task->section->students()->where('student_id', $student->id)->exists(),
            403
        );

        $step = $request->integer('step') ?: null;

        // Mark instructor messages as read
        $this->markCommentsRead($task, 'student', $step);

        $comments = $this->getComments($task, $step);

        $stepTitles = [
            1 => 'Analyze Transactions',          2 => 'Journalize Transactions',
            3 => 'Post to Ledger Accounts',       4 => 'Prepare Trial Balance',
            5 => 'Journalize & Post Adjusting Entries', 6 => 'Prepare Adjusted Trial Balance',
            7 => 'Prepare Financial Statements',  8 => 'Journalize & Post Closing Entries',
            9 => 'Prepare Post-Closing Trial Balance', 10 => 'Reverse (Optional Step)',
        ];

        return view('students.performance-tasks.comments.show', compact(
            'task', 'comments', 'step', 'stepTitles'
        ));
    }

    /**
     * Post a new comment (or reply) as student.
     */
    public function store(Request $request, PerformanceTask $task)
    {
        $student = auth()->user()->student;

        abort_unless(
            $task->section->students()->where('student_id', $student->id)->exists(),
            403
        );

        $step = $request->integer('step') ?: null;

        try {
            $this->storeComment($request, $task, 'student', $step);

            return redirect()
                ->route('students.performance-tasks.comments.show', [
                    'task' => $task->id,
                    'step' => $step,
                ])
                ->with('success', 'Message sent!');
        } catch (\Exception $e) {
            Log::error('Student comment store error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Could not send message. Please try again.');
        }
    }

    /**
     * Delete own comment (soft-delete).
     */
    public function destroy(PerformanceTask $task, PerformanceTaskComment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}