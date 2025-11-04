<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\FeedbackRecord;
use App\Models\PerformanceTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class FeedbackController extends Controller
{
    /**
     * Show the student's submitted feedback records.
     */
    public function index()
    {
        try {
            $feedbacks = FeedbackRecord::with(['performanceTask'])
                ->where('student_id', Auth::user()->student->id)
                ->latest()
                ->paginate(10);

            return view('students.feedback.index', compact('feedbacks'));
        } catch (Exception $e) {
            Log::error('Error fetching feedback records: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Unable to load feedback records. Please try again later.');
        }
    }

    public function create()
    {
        try {
            $student = Auth::user()->student;
            
            // Debug: Log student info
            Log::info('Feedback Create - Student ID: ' . $student->id);
            Log::info('Feedback Create - Section ID: ' . $student->section_id);
            
            // Get all submissions for this student
            $allSubmissions = \App\Models\PerformanceTaskSubmission::where('student_id', $student->id)
                ->select('task_id', 'step')
                ->get();
            
            Log::info('Feedback Create - Total submissions: ' . $allSubmissions->count());
            Log::info('Feedback Create - Submissions: ' . $allSubmissions->toJson());
            
            // Group by task_id and count distinct steps
            $taskStepCounts = $allSubmissions->groupBy('task_id')->map(function($submissions) {
                return $submissions->pluck('step')->unique()->count();
            });
            
            Log::info('Feedback Create - Task step counts: ' . $taskStepCounts->toJson());
            
            // Get tasks where student completed all 10 steps
            $completedTaskIds = $taskStepCounts->filter(function($stepCount) {
                return $stepCount >= 10;
            })->keys()->toArray();
            
            Log::info('Feedback Create - Completed task IDs: ' . json_encode($completedTaskIds));

            if (empty($completedTaskIds)) {
                return redirect()->route('students.feedback.index')
                    ->with('info', 'No completed tasks available for feedback. You need to complete all 10 steps of a performance task first.');
            }

            // Get completed tasks (check section_id only if it exists)
            $tasksQuery = PerformanceTask::whereIn('id', $completedTaskIds)
                ->with(['subject', 'instructor']);
            
            // Only filter by section if student has a section assigned
            if ($student->section_id) {
                $tasksQuery->where('section_id', $student->section_id);
            }
            
            $tasks = $tasksQuery->get();
            
            Log::info('Feedback Create - Tasks found: ' . $tasks->count());

            if ($tasks->isEmpty()) {
                return redirect()->route('students.feedback.index')
                    ->with('info', 'No completed tasks available for feedback at this time.');
            }

            // Pass as 'performanceTasks' to match the view variable name
            $performanceTasks = $tasks;
            return view('students.feedback.create', compact('performanceTasks'));
        } catch (Exception $e) {
            Log::error('Error loading feedback creation form: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->route('students.feedback.index')
                ->with('error', 'Unable to load feedback form. Please try again later.');
        }
    }

    /**
     * Store a new feedback record from student.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'performance_task_id' => 'required|exists:performance_tasks,id',
            'feedback_type' => 'required|in:general,improvement,question',
            'feedback_text' => 'required|string|min:10|max:5000',
            'recommendations' => 'nullable|string|max:2000',
            'rating' => 'required|integer|min:1|max:5',
            'is_anonymous' => 'nullable|boolean',
        ]);

        try {
            $student = Auth::user()->student;
            
            // Validate task exists and is accessible to student
            $taskQuery = PerformanceTask::where('id', $request->performance_task_id);
            
            // Only check section if student has one assigned
            if ($student->section_id) {
                $taskQuery->where('section_id', $student->section_id);
            }
            
            $task = $taskQuery->first();

            if (!$task) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid task selected.');
            }

            // Verify student has completed all 10 steps
            $completedSteps = \App\Models\PerformanceTaskSubmission::where([
                'task_id' => $request->performance_task_id,
                'student_id' => $student->id
            ])
            ->distinct()
            ->count('step');

            if ($completedSteps < 10) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You must complete all 10 steps before submitting feedback for this task.');
            }

            // Check for duplicate feedback
            $existingFeedback = FeedbackRecord::where([
                'student_id' => $student->id,
                'performance_task_id' => $request->performance_task_id
            ])->exists();

            if ($existingFeedback) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You have already submitted feedback for this task.');
            }

            // Convert recommendations string to array
            $recommendations = array_filter(
                explode("\n", str_replace("\r", "", $request->recommendations ?? ''))
            );

            FeedbackRecord::create([
                'student_id' => $student->id,
                'performance_task_id' => $request->performance_task_id,
                'feedback_type' => $request->feedback_type,
                'feedback_text' => $request->feedback_text,
                'recommendations' => $recommendations,
                'rating' => $request->rating,
                'generated_at' => now(),
                'is_read' => false,
                'is_anonymous' => $request->boolean('is_anonymous', false)
            ]);

            return redirect()->route('students.feedback.index')
                ->with('success', 'Your feedback has been submitted successfully.');

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

            // Load the relationship
            $feedback->load('performanceTask');

            return view('students.feedback.show', compact('feedback'));

        } catch (Exception $e) {
            Log::error('Error showing feedback: ' . $e->getMessage());
            return redirect()->route('students.feedback.index')
                ->with('error', 'Unable to display feedback. Please try again later.');
        }
    }
}