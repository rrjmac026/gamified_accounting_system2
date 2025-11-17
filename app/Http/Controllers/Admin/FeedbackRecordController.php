<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackRecord;
use App\Models\Student;
use App\Models\PerformanceTask;
use App\Models\PerformanceTaskSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class FeedbackRecordController extends Controller
{
    /**
     * Display all feedback records grouped by performance tasks and steps
     */
    public function index()
    {
        try {
            // Get all performance tasks with their feedbacks
            $tasks = PerformanceTask::with(['subject', 'instructor', 'section'])
                ->latest()
                ->get();
            
            // Step titles mapping
            $stepTitles = [
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
            
            // Prepare task data with feedback statistics
            $taskData = [];
            foreach ($tasks as $task) {
                // Get all feedbacks for this task
                $feedbacks = FeedbackRecord::where('performance_task_id', $task->id)
                    ->with('student.user')
                    ->get()
                    ->groupBy('step');
                
                // Get submission statistics per step
                $steps = [];
                for ($i = 1; $i <= 10; $i++) {
                    $stepFeedbacks = $feedbacks->get($i, collect());
                    $submissionsCount = PerformanceTaskSubmission::where([
                        'task_id' => $task->id,
                        'step' => $i
                    ])->count();
                    
                    $steps[$i] = [
                        'number' => $i,
                        'title' => $stepTitles[$i],
                        'feedback_count' => $stepFeedbacks->count(),
                        'submissions_count' => $submissionsCount,
                        'unread_count' => $stepFeedbacks->where('is_read', false)->count(),
                        'average_rating' => $stepFeedbacks->avg('rating') ?? 0,
                        'feedbacks' => $stepFeedbacks,
                    ];
                }
                
                $totalFeedbacks = collect($steps)->sum('feedback_count');
                $totalUnread = collect($steps)->sum('unread_count');
                
                $taskData[] = [
                    'task' => $task,
                    'steps' => $steps,
                    'total_feedbacks' => $totalFeedbacks,
                    'total_unread' => $totalUnread,
                ];
            }

            return view('admin.feedback-records.index', compact('taskData', 'stepTitles'));
            
        } catch (Exception $e) {
            Log::error('Error fetching feedback records: ' . $e->getMessage());
            return back()->with('error', 'Unable to load feedback records.');
        }
    }

    /**
     * Show feedbacks for a specific task and step
     */
    public function showStepFeedbacks(Request $request)
    {
        try {
            $taskId = $request->query('task_id');
            $step = $request->query('step');
            
            if (!$taskId || !$step) {
                return redirect()->route('admin.feedback-records.index')
                    ->with('error', 'Invalid request. Please select a task and step.');
            }
            
            $task = PerformanceTask::with(['subject', 'instructor', 'section'])
                ->findOrFail($taskId);
            
            $feedbacks = FeedbackRecord::where([
                'performance_task_id' => $taskId,
                'step' => $step
            ])
            ->with('student.user')
            ->latest('generated_at')
            ->paginate(15);
            
            // Mark all as read when admin views
            FeedbackRecord::where([
                'performance_task_id' => $taskId,
                'step' => $step,
                'is_read' => false
            ])->update(['is_read' => true]);
            
            $stepTitles = [
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

            return view('admin.feedback-records.step-feedbacks', compact('task', 'step', 'feedbacks', 'stepTitles'));
            
        } catch (Exception $e) {
            Log::error('Error fetching step feedbacks: ' . $e->getMessage());
            return redirect()->route('admin.feedback-records.index')
                ->with('error', 'Unable to load feedback records.');
        }
    }

    /**
     * Show a single feedback record
     */
    public function show(FeedbackRecord $feedbackRecord)
    {
        try {
            $feedbackRecord->load(['student.user', 'performanceTask.subject', 'performanceTask.instructor']);
            
            // Mark as read when admin views
            if (!$feedbackRecord->is_read) {
                $feedbackRecord->update(['is_read' => true]);
            }
            
            // Get the submission for this feedback
            $submission = PerformanceTaskSubmission::where([
                'task_id' => $feedbackRecord->performance_task_id,
                'student_id' => $feedbackRecord->student_id,
                'step' => $feedbackRecord->step
            ])->first();
            
            $stepTitles = [
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
            
            return view('admin.feedback-records.show', compact('feedbackRecord', 'submission', 'stepTitles'));
            
        } catch (Exception $e) {
            Log::error('Error showing feedback: ' . $e->getMessage());
            return back()->with('error', 'Unable to display feedback.');
        }
    }

    /**
     * Show analytics/statistics for feedbacks
     */
    public function analytics()
    {
        try {
            $totalFeedbacks = FeedbackRecord::count();
            $unreadFeedbacks = FeedbackRecord::unread()->count();
            $averageRating = FeedbackRecord::avg('rating') ?? 0;
            
            // Feedback by type
            $feedbacksByType = FeedbackRecord::selectRaw('feedback_type, COUNT(*) as count')
                ->groupBy('feedback_type')
                ->pluck('count', 'feedback_type');
            
            // Feedback by step
            $feedbacksByStep = FeedbackRecord::selectRaw('step, COUNT(*) as count, AVG(rating) as avg_rating')
                ->groupBy('step')
                ->orderBy('step')
                ->get();
            
            // Recent feedbacks
            $recentFeedbacks = FeedbackRecord::with(['student.user', 'performanceTask'])
                ->latest('generated_at')
                ->limit(10)
                ->get();
            
            // Top rated steps
            $topRatedSteps = FeedbackRecord::selectRaw('step, AVG(rating) as avg_rating, COUNT(*) as count')
                ->groupBy('step')
                ->having('count', '>=', 3)
                ->orderByDesc('avg_rating')
                ->limit(5)
                ->get();
            
            // Lowest rated steps
            $lowestRatedSteps = FeedbackRecord::selectRaw('step, AVG(rating) as avg_rating, COUNT(*) as count')
                ->groupBy('step')
                ->having('count', '>=', 3)
                ->orderBy('avg_rating')
                ->limit(5)
                ->get();
            
            $stepTitles = [
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

            return view('admin.feedback-records.analytics', compact(
                'totalFeedbacks',
                'unreadFeedbacks',
                'averageRating',
                'feedbacksByType',
                'feedbacksByStep',
                'recentFeedbacks',
                'topRatedSteps',
                'lowestRatedSteps',
                'stepTitles'
            ));
            
        } catch (Exception $e) {
            Log::error('Error loading feedback analytics: ' . $e->getMessage());
            return back()->with('error', 'Unable to load analytics.');
        }
    }

    /**
     * Delete a feedback record
     */
    public function destroy(FeedbackRecord $feedbackRecord)
    {
        try {
            $step = $feedbackRecord->step;
            $taskId = $feedbackRecord->performance_task_id;
            
            $feedbackRecord->delete();
            
            return redirect()->route('admin.feedback-records.step-feedbacks', [
                'task_id' => $taskId,
                'step' => $step
            ])->with('success', 'Feedback deleted successfully.');
            
        } catch (Exception $e) {
            Log::error('Error deleting feedback: ' . $e->getMessage());
            return back()->with('error', 'Unable to delete feedback.');
        }
    }

    /**
     * Mark feedback as read/unread
     */
    public function toggleRead(FeedbackRecord $feedbackRecord)
    {
        try {
            $feedbackRecord->update(['is_read' => !$feedbackRecord->is_read]);
            
            $status = $feedbackRecord->is_read ? 'read' : 'unread';
            return back()->with('success', "Feedback marked as {$status}.");
            
        } catch (Exception $e) {
            Log::error('Error toggling feedback read status: ' . $e->getMessage());
            return back()->with('error', 'Unable to update feedback status.');
        }
    }

    /**
     * Export feedbacks for a specific task
     */
    public function export(Request $request)
    {
        try {
            $taskId = $request->query('task_id');
            
            if (!$taskId) {
                return redirect()->route('admin.feedback-records.index')
                    ->with('error', 'Please select a task to export.');
            }
            
            $task = PerformanceTask::findOrFail($taskId);
            
            $feedbacks = FeedbackRecord::where('performance_task_id', $taskId)
                ->with('student.user')
                ->orderBy('step')
                ->orderBy('generated_at')
                ->get();
            
            // This would typically generate a CSV or Excel file
            // For now, return a view that can be printed/saved
            $stepTitles = [
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
            
            return view('admin.feedback-records.export', compact('task', 'feedbacks', 'stepTitles'));
            
        } catch (Exception $e) {
            Log::error('Error exporting feedbacks: ' . $e->getMessage());
            return redirect()->route('admin.feedback-records.index')
                ->with('error', 'Unable to export feedbacks.');
        }
    }
}