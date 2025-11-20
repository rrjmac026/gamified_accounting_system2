<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\PerformanceTask;
use App\Models\PerformanceTaskSubmission;
use App\Models\User;
use App\Models\SystemNotification;
use App\Models\PerformanceTaskAnswerSheet;
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

            // Create a lookup array for submissions by step number (for Blade use)
            $submissionsByStep = [];
            foreach ($submissions as $submission) {
                $submissionsByStep[$submission->step] = $submission;
            }

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

            // Statistics calculation
            $statistics = [
                'total_score' => $submissions->sum('score'),
                'total_attempts' => $submissions->sum('attempts'),
                // Completed: ANY step that has been submitted (regardless of correct/wrong)
                'completed_steps' => $submissions->count(),
                // Wrong: Steps with wrong answers
                'wrong_steps' => $submissions->where('status', 'wrong')->count(),
                // In Progress: Steps NOT yet submitted (10 total steps - submitted steps)
                'in_progress_steps' => $hasStarted ? (10 - $submissions->count()) : 0,
            ];

            return view('instructors.performance-tasks.submissions.show-student', compact(
                'task',
                'student',
                'submissionDetails',
                'stepTitles',
                'statistics',
                'submissionsByStep'
            ));

        } catch (Exception $e) {
            Log::error('Error in PerformanceTaskSubmissionController@showStudent: ' . $e->getMessage());
            return back()->with('error', 'Unable to load student submission. Please try again.');
        }
    }

    public function feedbackForm(PerformanceTask $task, User $student, $step)
    {
        try {
            $instructor = auth()->user()->instructor;
            
            if ($task->instructor_id !== $instructor->id) {
                throw new Exception('Unauthorized access to task');
            }

            $studentRecord = $student->student;
            
            $submission = PerformanceTaskSubmission::where([
                'task_id' => $task->id,
                'student_id' => $studentRecord->id,
                'step' => $step
            ])->firstOrFail();

            // Get answer sheet for reference
            $answerSheet = PerformanceTaskAnswerSheet::where([
                'performance_task_id' => $task->id,
                'step' => $step
            ])->first();

            return view('instructors.performance-tasks.submissions.feedback-form', compact(
                'task',
                'student',
                'submission',
                'answerSheet',
                'step'
            ));

        } catch (Exception $e) {
            Log::error('Error loading feedback form: ' . $e->getMessage());
            return back()->with('error', 'Unable to load feedback form.');
        }
    }

    /**
     * Store instructor feedback for a step submission
     * Enhanced with student notification
     */
    public function storeFeedback(Request $request, PerformanceTask $task, User $student, $step)
    {
        try {
            $instructor = auth()->user()->instructor;
            
            if ($task->instructor_id !== $instructor->id) {
                throw new Exception('Unauthorized access to task');
            }

            $validated = $request->validate([
                'instructor_feedback' => 'required|string|min:10|max:1000'
            ]);

            $studentRecord = $student->student;
            
            $submission = PerformanceTaskSubmission::where([
                'task_id' => $task->id,
                'student_id' => $studentRecord->id,
                'step' => $step
            ])->firstOrFail();

            // Update submission with feedback
            $submission->update([
                'instructor_feedback' => $validated['instructor_feedback'],
                'feedback_given_at' => now(),
                'needs_feedback' => false
            ]);

            // Create notification for the student
            $this->notifyStudentAboutFeedback($student, $task, $step, $instructor);

            // Log the feedback
            Log::info("Instructor {$instructor->id} provided feedback for student {$studentRecord->id}, task {$task->id}, step {$step}");

            return redirect()
                ->route('instructors.performance-tasks.submissions.show-student', [
                    'task' => $task->id,
                    'student' => $student->id
                ])
                ->with('success', 'Feedback saved and student notified successfully!');

        } catch (Exception $e) {
            Log::error('Error saving feedback: ' . $e->getMessage());
            return back()->with('error', 'Unable to save feedback. Please try again.');
        }
    }

    /**
     * Notify student when instructor provides feedback
     * 
     * @param User $student The student to notify
     * @param PerformanceTask $task The performance task
     * @param int $step The step number that received feedback
     * @param mixed $instructor The instructor who provided feedback
     * @return void
     */
    protected function notifyStudentAboutFeedback(User $student, PerformanceTask $task, int $step, $instructor)
    {
        try {
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

            $stepTitle = $stepTitles[$step] ?? "Step {$step}";
            $instructorName = $instructor->user->name ?? 'Your instructor';

            // Create the notification with proper route
            $notificationLink = $this->getStudentTaskLink($task->id);
            
            SystemNotification::create([
                'user_id' => $student->id,
                'title' => 'New Feedback Received',
                'message' => "{$instructorName} provided feedback on your submission for {$task->title} - {$stepTitle}",
                'type' => 'info',
                'link' => $notificationLink,
                'is_read' => false,
                'expires_at' => now()->addDays(30), // Notification expires in 30 days
            ]);

            Log::info("Notification sent to student {$student->id} for feedback on task {$task->id}, step {$step}");

        } catch (Exception $e) {
            // Log error but don't throw - notification failure shouldn't block feedback
            Log::error("Failed to notify student about feedback: " . $e->getMessage());
        }
    }

    /**
     * Get the correct student task link
     * This method tries multiple route patterns to ensure compatibility
     * 
     * @param int $taskId
     * @return string
     */
    protected function getStudentTaskLink(int $taskId): string
    {
        // Try common route patterns in order of likelihood
        $possibleRoutes = [
            ['students.performance-tasks.show', ['task' => $taskId]],
            ['students.performance-tasks.show', ['id' => $taskId]],
            ['students.performance-tasks.show', $taskId],
        ];

        foreach ($possibleRoutes as $routeConfig) {
            try {
                if (is_array($routeConfig[1])) {
                    return route($routeConfig[0], $routeConfig[1]);
                } else {
                    return route($routeConfig[0], $routeConfig[1]);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Fallback: construct URL manually
        return url("/students/performance-tasks/{$taskId}");
    }

    /**
     * Optional: Bulk notify students when grading multiple submissions
     * 
     * @param array $feedbackData Array of feedback items with student_id, task_id, step
     * @return int Number of students notified
     */
    protected function bulkNotifyStudents(array $feedbackData)
    {
        $notifiedCount = 0;

        foreach ($feedbackData as $data) {
            try {
                $student = User::find($data['student_id']);
                $task = PerformanceTask::find($data['task_id']);
                $step = $data['step'];
                $instructor = auth()->user()->instructor;

                if ($student && $task) {
                    $this->notifyStudentAboutFeedback($student, $task, $step, $instructor);
                    $notifiedCount++;
                }
            } catch (Exception $e) {
                Log::error("Bulk notification failed for student {$data['student_id']}: " . $e->getMessage());
            }
        }

        return $notifiedCount;
    }

    /**
     * Optional: Send reminder notification for pending feedback
     * This can be called via a scheduled command
     * 
     * @param int $daysOld Number of days since submission
     * @return void
     */
    public function notifyPendingFeedback(int $daysOld = 7)
    {
        $instructor = auth()->user()->instructor;

        // Find submissions awaiting feedback
        $pendingSubmissions = PerformanceTaskSubmission::where('needs_feedback', true)
            ->whereHas('task', function($query) use ($instructor) {
                $query->where('instructor_id', $instructor->id);
            })
            ->where('created_at', '<=', now()->subDays($daysOld))
            ->with(['student.user', 'task'])
            ->get();

        foreach ($pendingSubmissions as $submission) {
            try {
                SystemNotification::create([
                    'user_id' => $submission->student->user->id,
                    'title' => 'Feedback Pending',
                    'message' => "Your submission for {$submission->task->title} - Step {$submission->step} is still being reviewed",
                    'type' => 'warning',
                    'link' => $this->getStudentTaskLink($submission->task->id),
                    'is_read' => false,
                    'expires_at' => now()->addDays(14),
                ]);
            } catch (Exception $e) {
                Log::error("Failed to send pending feedback notification: " . $e->getMessage());
            }
        }
    }
}