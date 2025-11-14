<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\PerformanceTask;
use App\Models\PerformanceTaskSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class PerformanceTaskSubmissionController extends Controller
{
    /**
     * Show all submissions across all performance tasks for this instructor
     */
    public function index()
    {
        try {
            $instructor = auth()->user()->instructor;
            
            if (!$instructor) {
                throw new Exception('Not authorized as an instructor');
            }

            // Get all performance tasks for this instructor with submission counts
            $tasks = PerformanceTask::where('instructor_id', $instructor->id)
                ->withCount('submissions')
                ->latest()
                ->get();

            // Get all submissions for the instructor's tasks with proper eager loading
            $allSubmissions = PerformanceTaskSubmission::with(['student.user', 'task'])
                ->whereHas('task', function($query) use ($instructor) {
                    $query->where('instructor_id', $instructor->id);
                })
                ->orderBy('task_id')
                ->orderBy('student_id')
                ->orderBy('step')
                ->get();

            // Pre-calculate task statistics
            $taskStats = [];
            foreach ($tasks as $task) {
                $taskSubmissions = $allSubmissions->where('task_id', $task->id);
                
                $taskStats[$task->id] = [
                    'total_submissions' => $taskSubmissions->count(),
                    'unique_students' => $taskSubmissions->unique('student_id')->count(),
                    'completed_steps' => $taskSubmissions->where('status', 'correct')->count(),
                    'total_possible_steps' => $taskSubmissions->unique('student_id')->count() * 10,
                ];
                
                // Calculate progress percentage
                if ($taskStats[$task->id]['total_possible_steps'] > 0) {
                    $taskStats[$task->id]['progress_percent'] = 
                        ($taskStats[$task->id]['completed_steps'] / $taskStats[$task->id]['total_possible_steps']) * 100;
                } else {
                    $taskStats[$task->id]['progress_percent'] = 0;
                }
            }

            // Pre-calculate student statistics
            $studentStats = [];
            $submissionsByStudent = $allSubmissions->groupBy('student_id');
            
            foreach ($submissionsByStudent as $studentId => $studentSubmissions) {
                $student = $studentSubmissions->first()->student;
                
                $studentStats[$studentId] = [
                    'student' => $student,
                    'user' => $student->user, // Add user data
                    'tasks_count' => $studentSubmissions->unique('task_id')->count(),
                    'completed_steps' => $studentSubmissions->where('status', 'correct')->count(),
                    'total_score' => $studentSubmissions->sum('score'),
                    'total_attempts' => $studentSubmissions->sum('attempts'),
                ];
            }

            return view('instructors.performance-tasks.submissions.index', compact(
                'tasks',
                'taskStats',
                'studentStats'
            ));

        } catch (Exception $e) {
            Log::error('Error in PerformanceTaskSubmissionController@index: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while loading submissions. Please try again.');
        }
    }


    /**
     * Show all student submissions for a specific performance task
     */
    public function show(PerformanceTask $task)
    {
        try {
            $instructor = auth()->user()->instructor;
            
            // Verify ownership
            if ($task->instructor_id !== $instructor->id) {
                throw new Exception('Unauthorized access to task');
            }

            // Get all submissions for this task with proper eager loading
            $submissions = PerformanceTaskSubmission::with('student.user')
                ->where('task_id', $task->id)
                ->orderBy('student_id')
                ->orderBy('step')
                ->get();

            // Group submissions by student
            $studentSubmissions = $submissions->groupBy('student_id');

            // Calculate statistics for each student
            $studentStats = [];
            foreach ($studentSubmissions as $studentId => $studentSubs) {
                $student = $studentSubs->first()->student;
                $user = $student->user; // Get the related user
                
                $studentStats[$studentId] = [
                    'student' => $student,
                    'user' => $user, // Pass user separately
                    'total_submissions' => $studentSubs->count(),
                    'completed_steps' => $studentSubs->where('status', 'correct')->count(),
                    'wrong_steps' => $studentSubs->where('status', 'wrong')->count(),
                    'in_progress_steps' => $studentSubs->where('status', 'in-progress')->count(),
                    'total_score' => $studentSubs->sum('score'),
                    'total_attempts' => $studentSubs->sum('attempts'),
                    'progress_percent' => ($studentSubs->where('status', 'correct')->count() / 10) * 100,
                ];
            }

            // Overall task statistics
            $taskStats = [
                'total_submissions' => $submissions->count(),
                'unique_students' => $studentSubmissions->count(),
                'completed_steps' => $submissions->where('status', 'correct')->count(),
                'average_progress' => $studentSubmissions->count() > 0 
                    ? collect($studentStats)->avg('progress_percent') 
                    : 0,
            ];

            return view('instructors.performance-tasks.submissions.show', compact(
                'task',
                'studentStats',
                'taskStats'
            ));

        } catch (Exception $e) {
            Log::error('Error in PerformanceTaskSubmissionController@show: ' . $e->getMessage());
            return back()->with('error', 'Unable to load task submissions. Please try again.');
        }
    }

    /**
     * View details of a single student's submission for a specific task
     */
    public function showStudent(PerformanceTask $task, User $student)
    {
        try {
            $instructor = auth()->user()->instructor;
            
            // Verify ownership
            if ($task->instructor_id !== $instructor->id) {
                throw new Exception('Unauthorized access to task');
            }

            // Get the student record
            $studentRecord = $student->student;
            
            if (!$studentRecord) {
                throw new Exception('Student record not found');
            }

            // Get all submissions for this student and task
            $submissions = PerformanceTaskSubmission::where('task_id', $task->id)
                ->where('student_id', $studentRecord->id)
                ->orderBy('step')
                ->get();

            // Step titles
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

            // Check if student has started the task (has at least one submission)
            $hasStarted = $submissions->count() > 0;

            // Pre-calculate submission details
            $submissionDetails = [];
            foreach ($submissions as $submission) {
                $submissionDetails[$submission->step] = [
                    'step_title' => $stepTitles[$submission->step] ?? "Step {$submission->step}",
                    'status' => $submission->status,
                    'score' => $submission->score,
                    'attempts' => $submission->attempts,
                    'submitted_data' => $submission->submitted_data,
                    'feedback' => $submission->remarks,
                    'submitted_at' => $submission->created_at,
                    'updated_at' => $submission->updated_at,
                ];
            }

            // Add "in-progress" status for unsubmitted steps if student has started
            if ($hasStarted) {
                for ($step = 1; $step <= 10; $step++) {
                    if (!isset($submissionDetails[$step])) {
                        $submissionDetails[$step] = [
                            'step_title' => $stepTitles[$step] ?? "Step {$step}",
                            'status' => 'in-progress',
                            'score' => 0,
                            'attempts' => 0,
                            'submitted_data' => null,
                            'feedback' => null,
                            'submitted_at' => null,
                            'updated_at' => null,
                        ];
                    }
                }
            }

            // Overall statistics
            $statistics = [
                'total_score' => $submissions->sum('score'),
                'total_attempts' => $submissions->sum('attempts'),
                'completed_steps' => $submissions->where('status', 'correct')->count(),
                'wrong_steps' => $submissions->where('status', 'wrong')->count(),
                'in_progress_steps' => $hasStarted ? (10 - $submissions->whereIn('status', ['correct', 'wrong'])->count()) : 0,
            ];

            return view('instructors.performance-tasks.submissions.show-student', compact(
                'task',
                'student',
                'submissionDetails',
                'stepTitles',
                'statistics'
            ));

        } catch (Exception $e) {
            Log::error('Error in PerformanceTaskSubmissionController@showStudent: ' . $e->getMessage());
            return back()->with('error', 'Unable to load student submission. Please try again.');
        }
    }
}