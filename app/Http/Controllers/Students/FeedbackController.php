<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\FeedbackRecord;
use App\Models\PerformanceTask;
use App\Models\PerformanceTaskSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\Loggable; // << added
use Exception;

class FeedbackController extends Controller
{
    use Loggable; // << added

    /**
     * Show the student's performance tasks with completed steps for feedback
     */
    public function index()
    {
        try {
            $student = Auth::user()->student;
            
            // Get all tasks assigned to student's section
            $tasksQuery = PerformanceTask::whereHas('section.students', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })->with(['subject', 'instructor']);
            
            if ($student->section_id) {
                $tasksQuery->where('section_id', $student->section_id);
            }
            
            $tasks = $tasksQuery->latest()->get();
            
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
            
            // Prepare task data with step completion status
            $taskData = [];
            foreach ($tasks as $task) {
                // Get all submissions for this task
                $submissions = PerformanceTaskSubmission::where([
                    'task_id' => $task->id,
                    'student_id' => $student->id
                ])->get()->keyBy('step');
                
                // Get existing feedbacks for this task
                $existingFeedbacks = FeedbackRecord::where([
                    'student_id' => $student->id,
                    'performance_task_id' => $task->id
                ])->get()->keyBy('step');
                
                $steps = [];
                for ($i = 1; $i <= 10; $i++) {
                    $submission = $submissions->get($i);
                    $feedback = $existingFeedbacks->get($i);
                    
                    $steps[$i] = [
                        'number' => $i,
                        'title' => $stepTitles[$i],
                        'is_completed' => $submission !== null,
                        'status' => $submission ? $submission->status : null,
                        'score' => $submission ? $submission->score : 0,
                        'has_feedback' => $feedback !== null,
                        'feedback' => $feedback,
                        'can_submit_feedback' => $submission !== null && $feedback === null,
                    ];
                }
                
                $completedStepsCount = collect($steps)->filter(fn($s) => $s['is_completed'])->count();
                
                $taskData[] = [
                    'task' => $task,
                    'steps' => $steps,
                    'completed_steps' => $completedStepsCount,
                    'total_steps' => 10,
                    'progress_percentage' => ($completedStepsCount / 10) * 100,
                ];
            }

            // Activity log for viewing feedback index
            $this->logActivity('viewed feedback index', [
                'student_id' => $student->id ?? null,
                'tasks_count' => count($tasks),
            ]);

            return view('students.feedback.index', compact('taskData', 'stepTitles'));
            
        } catch (Exception $e) {
            Log::error('Error fetching feedback records: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Unable to load feedback records. Please try again later.');
        }
    }

    /**
     * Show form to create feedback for a specific step
     */
    public function create(Request $request)
    {
        try {
            $student = Auth::user()->student;
            $taskId = $request->query('task_id');
            $step = $request->query('step');
            
            if (!$taskId || !$step) {
                return redirect()->route('students.feedback.index')
                    ->with('error', 'Invalid request. Please select a step from the feedback page.');
            }
            
            // Verify task exists and is accessible
            $taskQuery = PerformanceTask::where('id', $taskId);
            if ($student->section_id) {
                $taskQuery->where('section_id', $student->section_id);
            }
            $task = $taskQuery->first();
            
            if (!$task) {
                return redirect()->route('students.feedback.index')
                    ->with('error', 'Performance task not found.');
            }
            
            // Verify step is completed
            $submission = PerformanceTaskSubmission::where([
                'task_id' => $taskId,
                'student_id' => $student->id,
                'step' => $step
            ])->first();
            
            if (!$submission) {
                return redirect()->route('students.feedback.index')
                    ->with('error', 'You must complete this step before submitting feedback.');
            }
            
            // Check if feedback already exists
            $existingFeedback = FeedbackRecord::where([
                'student_id' => $student->id,
                'performance_task_id' => $taskId,
                'step' => $step
            ])->exists();
            
            if ($existingFeedback) {
                return redirect()->route('students.feedback.index')
                    ->with('error', 'You have already submitted feedback for this step.');
            }
            
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

            // Activity log for creating feedback form
            $this->logActivity('opened feedback creation form', [
                'student_id' => $student->id ?? null,
                'task_id' => $taskId,
                'step' => $step,
            ]);

            return view('students.feedback.create', compact('task', 'step', 'stepTitles', 'submission'));
            
        } catch (Exception $e) {
            Log::error('Error loading feedback creation form: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->route('students.feedback.index')
                ->with('error', 'Unable to load feedback form. Please try again later.');
        }
    }

    /**
     * Store a new feedback record for a specific step
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'performance_task_id' => 'required|exists:performance_tasks,id',
            'step' => 'required|integer|min:1|max:10',
            'feedback_type' => 'required|in:general,improvement,question',
            'feedback_text' => 'required|string|min:10|max:5000',
            'recommendations' => 'nullable|string|max:2000',
            'rating' => 'required|integer|min:1|max:5',
            'is_anonymous' => 'nullable|boolean',
        ]);

        try {
            $student = Auth::user()->student;
            
            // Verify step is completed
            $submission = PerformanceTaskSubmission::where([
                'task_id' => $request->performance_task_id,
                'student_id' => $student->id,
                'step' => $request->step
            ])->first();

            if (!$submission) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You must complete this step before submitting feedback.');
            }

            // Check for duplicate feedback
            $existingFeedback = FeedbackRecord::where([
                'student_id' => $student->id,
                'performance_task_id' => $request->performance_task_id,
                'step' => $request->step
            ])->exists();

            if ($existingFeedback) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You have already submitted feedback for this step.');
            }

            // Convert recommendations to array
            $recommendations = array_filter(
                explode("\n", str_replace("\r", "", $request->recommendations ?? ''))
            );

            FeedbackRecord::create([
                'student_id' => $student->id,
                'performance_task_id' => $request->performance_task_id,
                'step' => $request->step,
                'feedback_type' => $request->feedback_type,
                'feedback_text' => $request->feedback_text,
                'recommendations' => $recommendations,
                'rating' => $request->rating,
                'generated_at' => now(),
                'is_read' => false,
                'is_anonymous' => $request->boolean('is_anonymous', false)
            ]);

            // Activity log for submitting feedback
            $this->logActivity('submitted feedback', [
                'student_id' => $student->id,
                'task_id' => $request->performance_task_id,
                'step' => $request->step,
                'feedback_type' => $request->feedback_type,
                'rating' => $request->rating,
                'is_anonymous' => $request->boolean('is_anonymous', false),
            ]);

            return redirect()->route('students.feedback.index')
                ->with('success', 'Your feedback for Step ' . $request->step . ' has been submitted successfully.');

        } catch (Exception $e) {
            Log::error('Error storing feedback: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Unable to submit feedback. Please try again later.');
        }
    }

    public function show(FeedbackRecord $feedback)
    {
        try {
            // Ensure student can only view their own feedback
            if ($feedback->student_id !== Auth::user()->student->id) {
                return redirect()->route('students.feedback.index')
                    ->with('error', 'You are not authorized to view this feedback.');
            }

            $feedback->load('performanceTask');
            
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

            // Activity log for viewing feedback
            $this->logActivity('viewed feedback', [
                'student_id' => $feedback->student_id,
                'task_id' => $feedback->performance_task_id,
                'step' => $feedback->step,
                'feedback_type' => $feedback->feedback_type,
            ]);

            return view('students.feedback.show', compact('feedback', 'stepTitles'));

        } catch (Exception $e) {
            Log::error('Error showing feedback: ' . $e->getMessage());
            return redirect()->route('students.feedback.index')
                ->with('error', 'Unable to display feedback. Please try again later.');
        }
    }
}