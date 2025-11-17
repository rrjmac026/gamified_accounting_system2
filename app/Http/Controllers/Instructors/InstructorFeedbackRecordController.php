<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\FeedbackRecord;
use App\Models\PerformanceTask;
use App\Models\PerformanceTaskSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class InstructorFeedbackRecordController extends Controller
{
    // ------------------------------------------
    // STEP TITLES
    // ------------------------------------------
    private $stepTitles = [
        1 => 'Analyze Transactions',
        2 => 'Journalize Transactions',
        3 => 'Post to Ledger Accounts',
        4 => 'Prepare Trial Balance',
        5 => 'Journalize & Post Adjusting Entries',
        6 => 'Prepare Adjusted Trial Balance',
        7 => 'Prepare Financial Statements',
        8 => 'Journalize & Post Closing Entries',
        9 => 'Prepare Post-Closing Trial Balance',
        10 => 'Reverse (Optional Step)',
    ];

    // ------------------------------------------
    // INDEX: Show all tasks + grouped feedback
    // ------------------------------------------
    public function index()
    {
        try {
            $instructorId = Auth::user()->instructor->id;

            // Only tasks belonging to this instructor
            $tasks = PerformanceTask::with(['subject', 'section'])
                ->where('instructor_id', $instructorId)
                ->latest()
                ->get();

            $taskData = [];

            foreach ($tasks as $task) {
                $feedbacks = FeedbackRecord::where('performance_task_id', $task->id)
                    ->with('student.user')
                    ->get()
                    ->groupBy('step');

                $steps = [];

                for ($i = 1; $i <= 10; $i++) {
                    $stepFeedbacks = $feedbacks->get($i, collect());
                    $submissionsCount = PerformanceTaskSubmission::where([
                        'task_id' => $task->id,
                        'step' => $i
                    ])->count();

                    $steps[$i] = [
                        'number' => $i,
                        'title' => $this->stepTitles[$i],
                        'feedback_count' => $stepFeedbacks->count(),
                        'submissions_count' => $submissionsCount,
                        'unread_count' => $stepFeedbacks->where('is_read', false)->count(),
                        'average_rating' => $stepFeedbacks->avg('rating') ?? 0,
                        'feedbacks' => $stepFeedbacks,
                    ];
                }

                $taskData[] = [
                    'task' => $task,
                    'steps' => $steps,
                    'total_feedbacks' => collect($steps)->sum('feedback_count'),
                    'total_unread' => collect($steps)->sum('unread_count'),
                ];
            }

            return view('instructors.feedback.index', [
                'taskData' => $taskData,
                'stepTitles' => $this->stepTitles
            ]);

        } catch (Exception $e) {
            Log::error('Instructor Feedback Index Error: '.$e->getMessage());
            return back()->with('error', 'Unable to load feedback records.');
        }
    }

    // ------------------------------------------
    // SHOW STEP FEEDBACKS
    // ------------------------------------------
    public function showStepFeedbacks(Request $request)
    {
        try {
            $taskId = $request->query('task_id');
            $step = $request->query('step');

            $instructorId = Auth::user()->instructor->id;

            $task = PerformanceTask::where('instructor_id', $instructorId)
                ->with(['subject', 'section'])
                ->findOrFail($taskId);

            $feedbacks = FeedbackRecord::where([
                'performance_task_id' => $taskId,
                'step' => $step
            ])
            ->with('student.user')
            ->latest('generated_at')
            ->paginate(15);

            // Mark feedback as read automatically
            FeedbackRecord::where([
                'performance_task_id' => $taskId,
                'step' => $step,
                'is_read' => false
            ])->update(['is_read' => true]);

            return view('instructor.feedback.step-feedbacks', [
                'task' => $task,
                'step' => $step,
                'feedbacks' => $feedbacks,
                'stepTitles' => $this->stepTitles
            ]);

        } catch (Exception $e) {
            Log::error('Instructor Step Feedback Error: '.$e->getMessage());
            return redirect()->route('instructors.feedback.index')
                ->with('error', 'Unable to load feedback.');
        }
    }

    // ------------------------------------------
    // SHOW SINGLE FEEDBACK RECORD
    // ------------------------------------------
    public function show(FeedbackRecord $feedbackRecord)
    {
        try {
            $instructorId = Auth::user()->instructor->id;

            // Protect access
            if ($feedbackRecord->performanceTask->instructor_id !== $instructorId) {
                abort(403);
            }

            $feedbackRecord->load(['student.user', 'performanceTask']);

            if (!$feedbackRecord->is_read) {
                $feedbackRecord->update(['is_read' => true]);
            }

            // Retrieve submission for context
            $submission = PerformanceTaskSubmission::where([
                'task_id' => $feedbackRecord->performance_task_id,
                'student_id' => $feedbackRecord->student_id,
                'step' => $feedbackRecord->step
            ])->first();

            return view('instructors.feedback.show', [
                'feedbackRecord' => $feedbackRecord,
                'submission' => $submission,
                'stepTitles' => $this->stepTitles
            ]);

        } catch (Exception $e) {
            Log::error('Instructor Show Feedback Error: '.$e->getMessage());
            return back()->with('error', 'Unable to display feedback.');
        }
    }

    // ------------------------------------------
    // ANALYTICS FOR INSTRUCTOR
    // ------------------------------------------
    public function analytics()
    {
        try {
            $instructorId = Auth::user()->instructor->id;

            $feedbackQuery = FeedbackRecord::whereHas('performanceTask', function ($q) use ($instructorId) {
                $q->where('instructor_id', $instructorId);
            });

            return view('instructor.feedback.analytics', [
                'totalFeedbacks' => $feedbackQuery->count(),
                'unreadFeedbacks' => $feedbackQuery->where('is_read', false)->count(),
                'averageRating' => $feedbackQuery->avg('rating') ?? 0,
                'feedbacksByType' => $feedbackQuery->selectRaw('feedback_type, COUNT(*) as count')
                    ->groupBy('feedback_type')
                    ->pluck('count', 'feedback_type'),
                'feedbacksByStep' => $feedbackQuery->selectRaw('step, COUNT(*) as count, AVG(rating) as avg_rating')
                    ->groupBy('step')
                    ->orderBy('step')
                    ->get(),
                'recentFeedbacks' => $feedbackQuery->with(['student.user', 'performanceTask'])
                    ->latest('generated_at')
                    ->limit(10)
                    ->get(),
                'stepTitles' => $this->stepTitles
            ]);

        } catch (Exception $e) {
            Log::error('Instructor Analytics Error: '.$e->getMessage());
            return back()->with('error', 'Unable to load analytics.');
        }
    }

    // ------------------------------------------
    // DELETE FEEDBACK
    // ------------------------------------------
    public function destroy(FeedbackRecord $feedbackRecord)
    {
        try {
            $instructorId = Auth::user()->instructor->id;

            if ($feedbackRecord->performanceTask->instructor_id !== $instructorId) {
                abort(403);
            }

            $step = $feedbackRecord->step;
            $taskId = $feedbackRecord->performance_task_id;

            $feedbackRecord->delete();

            return redirect()->route('instructor.feedback.step-feedbacks', [
                'task_id' => $taskId,
                'step' => $step
            ])->with('success', 'Feedback deleted successfully.');

        } catch (Exception $e) {
            Log::error('Instructor Delete Feedback Error: '.$e->getMessage());
            return back()->with('error', 'Unable to delete feedback.');
        }
    }

    // ------------------------------------------
    // TOGGLE READ STATUS
    // ------------------------------------------
    public function toggleRead(FeedbackRecord $feedbackRecord)
    {
        try {
            $instructorId = Auth::user()->instructor->id;

            if ($feedbackRecord->performanceTask->instructor_id !== $instructorId) {
                abort(403);
            }

            $feedbackRecord->update(['is_read' => !$feedbackRecord->is_read]);

            $status = $feedbackRecord->is_read ? 'read' : 'unread';

            return back()->with('success', "Feedback marked as {$status}.");

        } catch (Exception $e) {
            Log::error('Instructor Toggle Read Error: '.$e->getMessage());
            return back()->with('error', 'Unable to update feedback status.');
        }
    }

    // ------------------------------------------
    // EXPORT FEEDBACKS
    // ------------------------------------------
    public function export(Request $request)
    {
        try {
            $taskId = $request->query('task_id');
            $instructorId = Auth::user()->instructor->id;

            $task = PerformanceTask::where('instructor_id', $instructorId)
                ->findOrFail($taskId);

            $feedbacks = FeedbackRecord::where('performance_task_id', $taskId)
                ->with('student.user')
                ->orderBy('step')
                ->orderBy('generated_at')
                ->get();

            return view('instructor.feedback.export', [
                'task' => $task,
                'feedbacks' => $feedbacks,
                'stepTitles' => $this->stepTitles
            ]);

        } catch (Exception $e) {
            Log::error('Instructor Export Feedback Error: '.$e->getMessage());
            return redirect()->route('instructor.feedback.index')
                ->with('error', 'Unable to export feedbacks.');
        }
    }
}
