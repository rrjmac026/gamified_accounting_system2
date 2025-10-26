<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    public function index($status = 'assigned')
    {
        $student = Auth::user()->student;

        // Load student tasks with pivot + subject + submissions
        $tasks = $student->tasks()
            ->with(['subject', 'submissions' => function ($query) use ($student) {
                $query->where('student_id', $student->id)
                    ->latest('submitted_at');
            }])
            ->get();

        // 🔹 Auto-mark overdue "assigned" tasks as "missing"
        foreach ($tasks as $task) {
            $submission = $task->submissions->first();

            if (
                !$submission &&                               // no submission yet
                $task->due_date && $task->due_date < now() && // past deadline
                $task->pivot->status === 'assigned'           // still assigned
            ) {
                $student->tasks()->updateExistingPivot($task->id, [
                    'status' => 'missing',
                ]);

                // Also update the in-memory pivot so filtering works immediately
                $task->pivot->status = 'missing';
            }
        }

        // Filter tasks by status
        $filteredTasks = $tasks->filter(function ($task) use ($status) {
            $submission = $task->submissions->first();

            if ($status === 'missing') {
                return $task->pivot->status === 'missing';
            }

            if ($status === 'graded') {
                return $submission && $submission->score !== null;
            }

            if ($status === 'submitted') {
                return $task->pivot->status === 'submitted' || 
                    ($submission && $submission->score !== null);
            }

            if ($status === 'late') {
                return $task->pivot->status === 'late' ||
                    ($submission && $submission->submitted_at > $task->due_date);
            }

            return $task->pivot->status === $status;
        });

        return view('students.todo.index', [
            'status' => $status,
            'tasks'  => $filteredTasks,
        ]);
    }

}
