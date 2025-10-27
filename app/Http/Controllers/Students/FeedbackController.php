<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\FeedbackRecord;
use App\Http\Requests\FeedbackRecordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\PerformanceTask; // Changed from Task
use Exception;

class FeedbackController extends Controller
{
    /**
     * Show the student's submitted feedback records.
     */
    public function index()
    {
        try {
            $feedbacks = FeedbackRecord::with(['performanceTask']) // Changed from 'task'
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
            // Get tasks assigned to the student's section
            $tasks = PerformanceTask::where('section_id', Auth::user()->student->section_id)
                ->get();

            if ($tasks->isEmpty()) {
                return redirect()->route('students.feedback.index')
                    ->with('info', 'No tasks available for feedback at this time.');
            }

            return view('students.feedback.create', compact('tasks'));
        } catch (Exception $e) {
            Log::error('Error loading feedback creation form: ' . $e->getMessage());
            return redirect()->route('students.feedback.index')
                ->with('error', 'Unable to load feedback form. Please try again later.');
        }
    }

    /**
     * Store a new feedback record from student.
     */
    public function store(FeedbackRecordRequest $request)
    {
        try {
            // Validate task belongs to student's section
            $task = PerformanceTask::where('section_id', Auth::user()->student->section_id)
                ->find($request->performance_task_id); // Changed from task_id

            if (!$task) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid task selected.');
            }

            // Check for duplicate feedback
            $existingFeedback = FeedbackRecord::where([
                'student_id' => Auth::user()->student->id,
                'performance_task_id' => $request->performance_task_id // Changed from task_id
            ])->exists();

            if ($existingFeedback) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You have already submitted feedback for this task.');
            }

            // Convert recommendations string to array
            $recommendations = array_filter(
                explode("\n", str_replace("\r", "", $request->recommendations))
            );

            FeedbackRecord::create([
                'student_id' => Auth::user()->student->id,
                'performance_task_id' => $request->performance_task_id, // Changed from task_id
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

            return view('students.feedback.show', compact('feedback'));

        } catch (Exception $e) {
            Log::error('Error showing feedback: ' . $e->getMessage());
            return redirect()->route('students.feedback.index')
                ->with('error', 'Unable to display feedback. Please try again later.');
        }
    }
}