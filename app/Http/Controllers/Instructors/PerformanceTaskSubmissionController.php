<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\PerformanceTask;
use App\Models\PerformanceTaskSubmission;
use App\Models\User;
use App\Models\SystemNotification;
use App\Traits\Loggable;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class PerformanceTaskSubmissionController extends Controller
{
    use Loggable;

    /**
     * Show all performance tasks grouped by section.
     */
    public function index()
    {
        try {
            $instructor = auth()->user()->instructor;

            if (!$instructor) {
                throw new Exception('Not authorized as an instructor');
            }

            // Load tasks with section so the blade can group them
            $tasks = PerformanceTask::where('instructor_id', $instructor->id)
                ->with(['section', 'subject'])
                ->withCount('submissions')
                ->latest()
                ->get();

            $allSubmissions = PerformanceTaskSubmission::with(['student.user', 'task'])
                ->whereHas('task', function ($query) use ($instructor) {
                    $query->where('instructor_id', $instructor->id);
                })
                ->orderBy('task_id')
                ->orderBy('student_id')
                ->orderBy('step')
                ->get();

            // Per-task stats — each task is already scoped to one section
            $taskStats = [];
            foreach ($tasks as $task) {
                $taskSubmissions = $allSubmissions->where('task_id', $task->id);
                $uniqueStudents  = $taskSubmissions->unique('student_id')->count();
                $answeredSteps   = $taskSubmissions->count();
                $totalPossible   = $uniqueStudents * 10;

                $taskStats[$task->id] = [
                    'total_submissions'    => $answeredSteps,
                    'unique_students'      => $uniqueStudents,
                    'answered_steps'       => $answeredSteps,
                    'correct_steps'        => $taskSubmissions->where('status', 'correct')->count(),
                    'passed_steps'         => $taskSubmissions->where('status', 'passed')->count(),
                    'wrong_steps'          => $taskSubmissions->where('status', 'wrong')->count(),
                    'completed_steps'      => $taskSubmissions->whereIn('status', ['correct', 'passed'])->count(),
                    'total_possible_steps' => $totalPossible,
                    'progress_percent'     => $totalPossible > 0 ? ($answeredSteps / $totalPossible) * 100 : 0,
                    'section_name'         => $task->section->name ?? 'No Section',
                    'section_id'           => $task->section_id,
                ];
            }

            // Group tasks by section for the blade
            $tasksBySection = $tasks->groupBy('section_id');

            return view('instructors.performance-tasks.submissions.index', compact(
                'tasks',
                'taskStats',
                'tasksBySection'
            ));

        } catch (Exception $e) {
            Log::error('Error in PerformanceTaskSubmissionController@index: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while loading submissions. Please try again.');
        }
    }

    /**
     * Show all student submissions for a specific task.
     * Students are scoped to the task's own section — no cross-section mixing.
     */
    public function show(PerformanceTask $task)
    {
        try {
            $instructor = auth()->user()->instructor;

            if ($task->instructor_id !== $instructor->id) {
                throw new Exception('Unauthorized access to task');
            }

            $task->load(['section.students.user', 'subject']);

            $sectionStudentIds = $task->section
                ? $task->section->students->pluck('id')->toArray()
                : [];

            $submissions = PerformanceTaskSubmission::with('student.user')
                ->where('task_id', $task->id)
                ->whereIn('student_id', $sectionStudentIds)
                ->orderBy('student_id')
                ->orderBy('step')
                ->get();

            $studentSubmissions = $submissions->groupBy('student_id');
            $enabledCount       = count($task->enabled_steps_list); // ✅

            $studentStats = [];
            foreach ($studentSubmissions as $studentId => $studentSubs) {
                $student       = $studentSubs->first()->student;
                $answeredSteps = $studentSubs->count();

                $studentStats[$studentId] = [
                    'student'           => $student,
                    'user'              => $student->user,
                    'total_submissions' => $answeredSteps,
                    'answered_steps'    => $answeredSteps,
                    'correct_steps'     => $studentSubs->where('status', 'correct')->count(),
                    'passed_steps'      => $studentSubs->where('status', 'passed')->count(),
                    'wrong_steps'       => $studentSubs->where('status', 'wrong')->count(),
                    'completed_steps'   => $studentSubs->whereIn('status', ['correct', 'passed'])->count(),
                    'total_score'       => $studentSubs->sum('score'),
                    'total_attempts'    => $studentSubs->sum('attempts'),
                    'progress_percent'  => ($answeredSteps / $enabledCount) * 100, // ✅ was / 10
                    'has_submitted'     => true,
                ];
            }

            if ($task->section) {
                foreach ($task->section->students as $enrolledStudent) {
                    if (!isset($studentStats[$enrolledStudent->id])) {
                        $studentStats[$enrolledStudent->id] = [
                            'student'           => $enrolledStudent,
                            'user'              => $enrolledStudent->user,
                            'total_submissions' => 0,
                            'answered_steps'    => 0,
                            'correct_steps'     => 0,
                            'passed_steps'      => 0,
                            'wrong_steps'       => 0,
                            'completed_steps'   => 0,
                            'total_score'       => 0,
                            'total_attempts'    => 0,
                            'progress_percent'  => 0,
                            'has_submitted'     => false,
                        ];
                    }
                }
            }

            uasort($studentStats, fn($a, $b) =>
                $b['has_submitted'] <=> $a['has_submitted']
                ?: $b['answered_steps'] <=> $a['answered_steps']
            );

            $taskStats = [
                'total_submissions'    => $submissions->count(),
                'enrolled_students'    => $task->section ? $task->section->students->count() : 0,
                'submitted_students'   => $studentSubmissions->count(),
                'unique_students'      => count($studentStats),
                'answered_steps'       => $submissions->count(),
                'correct_steps'        => $submissions->where('status', 'correct')->count(),
                'passed_steps'         => $submissions->where('status', 'passed')->count(),
                'wrong_steps'          => $submissions->where('status', 'wrong')->count(),
                'completed_steps'      => $submissions->whereIn('status', ['correct', 'passed'])->count(),
                'average_progress'     => count($studentStats) > 0
                    ? collect($studentStats)->avg('progress_percent')
                    : 0,
                'section_name'         => $task->section->name ?? 'No Section',
                'enabled_steps'        => $enabledCount, // ✅ new — useful in blade
                'total_possible_steps' => count($studentStats) * $enabledCount, // ✅ was * 10
            ];

            return view('instructors.performance-tasks.submissions.show', compact(
                'task', 'studentStats', 'taskStats'
            ));

        } catch (Exception $e) {
            Log::error('Error in PerformanceTaskSubmissionController@show: ' . $e->getMessage());
            return back()->with('error', 'Unable to load task submissions. Please try again.');
        }
    }

    // In PerformanceTaskSubmissionController.php
    // Replace the viewAnswerSheet() method with this:

    public function viewAnswerSheet(PerformanceTask $task, User $student, $step)
    {
        try {
            $instructor = auth()->user()->instructor;

            if ($task->instructor_id !== $instructor->id) {
                throw new Exception('Unauthorized access to task');
            }

            if ($step < 1 || $step > 10) {
                throw new Exception('Invalid step number');
            }

            $stepTitle     = $this->stepTitles()[$step] ?? "Step {$step}";
            $studentRecord = $student->student;

            // ── Get the student's submission for this step ──────────────────────
            $submission = PerformanceTaskSubmission::where([
                'task_id'    => $task->id,
                'student_id' => $studentRecord->id,
                'step'       => $step,
            ])->first();

            // ── Resolve the correct answer source ───────────────────────────────
            // Priority 1: submission has an exercise_id → use that exercise
            // Priority 2: fall back to legacy PerformanceTaskAnswerSheet
            $exercise    = null;
            $answerSheet = null;

            if ($submission && $submission->exercise_id) {
                $exercise = \App\Models\PerformanceTaskExercise::find($submission->exercise_id);
            }

            if (!$exercise) {
                // No exercise linked — try legacy answer sheet
                $answerSheet = \App\Models\PerformanceTaskAnswerSheet::where([
                    'performance_task_id' => $task->id,
                    'step'                => $step,
                ])->first();
            }

            // If neither exists, show a friendly message in the view
            $hasAnswerKey = $exercise || $answerSheet;

            $this->logActivity('viewed answer sheet comparison', [
                'instructor_id'  => $instructor->id,
                'task_id'        => $task->id,
                'student_id'     => $studentRecord->id,
                'step'           => $step,
                'has_submission' => (bool) $submission,
                'source'         => $exercise ? 'exercise' : ($answerSheet ? 'answer_sheet' : 'none'),
            ]);

            return view('instructors.performance-tasks.submissions.answer-sheets.view', compact(
                'task', 'student', 'submission', 'step', 'stepTitle',
                'exercise', 'answerSheet', 'hasAnswerKey'
            ));

        } catch (Exception $e) {
            Log::error('Error viewing answer sheet: ' . $e->getMessage());
            return back()->with('error', 'Unable to load answer sheet. Please try again.');
        }
    }

    /**
     * View details of a single student's submission for a specific task.
     */
    public function showStudent(PerformanceTask $task, User $student)
    {
        try {
            $instructor = auth()->user()->instructor;

            if ($task->instructor_id !== $instructor->id) {
                throw new Exception('Unauthorized access to task');
            }

            $studentRecord = $student->student;

            if (!$studentRecord) {
                throw new Exception('Student record not found');
            }

            $submissions = PerformanceTaskSubmission::where('task_id', $task->id)
                ->where('student_id', $studentRecord->id)
                ->orderBy('step')
                ->get();

            $stepTitles = $this->stepTitles();
            $hasStarted = $submissions->count() > 0;

            $submissionsByStep = [];
            foreach ($submissions as $submission) {
                $submissionsByStep[$submission->step] = $submission;
            }

            $submissionDetails = [];
            foreach ($submissions as $submission) {
                $submissionDetails[$submission->step] = [
                    'step_title'     => $stepTitles[$submission->step] ?? "Step {$submission->step}",
                    'status'         => $submission->status,
                    'score'          => $submission->score,
                    'attempts'       => $submission->attempts,
                    'submitted_data' => $submission->submitted_data ?? null,
                    'feedback'       => $submission->remarks,
                    'submitted_at'   => $submission->created_at,
                    'updated_at'     => $submission->updated_at,
                ];
            }

            $exercisesByStep = \App\Models\PerformanceTaskExercise::where('performance_task_id', $task->id)
                ->get()->keyBy('id');

            // ✅ Only fill not-started slots for ENABLED steps (was looping 1–10)
            foreach ($task->enabled_steps_list as $step) {
                if (!isset($submissionDetails[$step])) {
                    $submissionDetails[$step] = [
                        'step_title'     => $stepTitles[$step] ?? "Step {$step}",
                        'status'         => 'not-started',
                        'score'          => 0,
                        'attempts'       => 0,
                        'submitted_data' => null,
                        'feedback'       => null,
                        'submitted_at'   => null,
                        'updated_at'     => null,
                    ];
                }
            }

            ksort($submissionDetails);

            $answeredSteps = $submissions->count();
            $enabledCount  = count($task->enabled_steps_list); // ✅

            $statistics = [
                'total_score'       => $submissions->sum('score'),
                'total_attempts'    => $submissions->sum('attempts'),
                'answered_steps'    => $answeredSteps,
                'correct_steps'     => $submissions->where('status', 'correct')->count(),
                'passed_steps'      => $submissions->where('status', 'passed')->count(),
                'wrong_steps'       => $submissions->where('status', 'wrong')->count(),
                'completed_steps'   => $submissions->whereIn('status', ['correct', 'passed'])->count(),
                'in_progress_steps' => $enabledCount - $answeredSteps,   // ✅ was 10 - $answeredSteps
                'progress_percent'  => ($answeredSteps / $enabledCount) * 100, // ✅ was / 10
                'section_name'      => $task->section->name ?? 'No Section',
            ];

            return view('instructors.performance-tasks.submissions.show-student', compact(
                'task', 'student', 'submissionDetails', 'stepTitles', 'statistics', 'submissionsByStep', 'exercisesByStep'
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
                'task_id'    => $task->id,
                'student_id' => $studentRecord->id,
                'step'       => $step,
            ])->firstOrFail();

            // ✅ Load exercise instead of PerformanceTaskAnswerSheet
            $exercise = \App\Models\PerformanceTaskExercise::where([
                'performance_task_id' => $task->id,
                'step'                => $step,
                'order'               => 1,
            ])->first();

            return view('instructors.performance-tasks.submissions.feedback-form', compact(
                'task', 'student', 'submission', 'exercise', 'step'
            ));

        } catch (Exception $e) {
            Log::error('Error loading feedback form: ' . $e->getMessage());
            return back()->with('error', 'Unable to load feedback form.');
        }
    }

    public function storeFeedback(Request $request, PerformanceTask $task, User $student, $step)
    {
        try {
            $instructor = auth()->user()->instructor;

            if ($task->instructor_id !== $instructor->id) {
                throw new Exception('Unauthorized access to task');
            }

            $validated = $request->validate([
                'instructor_feedback' => 'required|string|min:10|max:1000',
            ]);

            $studentRecord = $student->student;

            $submission = PerformanceTaskSubmission::where([
                'task_id'    => $task->id,
                'student_id' => $studentRecord->id,
                'step'       => $step,
            ])->firstOrFail();

            $submission->update([
                'instructor_feedback' => $validated['instructor_feedback'],
                'feedback_given_at'   => now(),
                'needs_feedback'      => false,
            ]);

            $this->notifyStudentAboutFeedback($student, $task, $step, $instructor);

            $this->logActivity('provided feedback on performance task submission', [
                'task_id'         => $task->id,
                'task_title'      => $task->title,
                'student_id'      => $studentRecord->id,
                'step'            => $step,
                'feedback_length' => strlen($validated['instructor_feedback']),
            ]);

            return redirect()
                ->route('instructors.performance-tasks.submissions.show-student', [
                    'task'    => $task->id,
                    'student' => $student->id,
                ])
                ->with('success', 'Feedback saved and student notified successfully!');

        } catch (Exception $e) {
            Log::error('Error saving feedback: ' . $e->getMessage());
            return back()->with('error', 'Unable to save feedback. Please try again.');
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function stepTitles(): array
    {
        return [
            1  => 'Analyze Transactions',
            2  => 'Journalize Transactions',
            3  => 'Post to Ledger Accounts',
            4  => 'Prepare Trial Balance',
            5  => 'Journalize & Post Adjusting Entries',
            6  => 'Prepare Adjusted Trial Balance',
            7  => 'Prepare Financial Statements',
            8  => 'Journalize & Post Closing Entries',
            9  => 'Prepare Post-Closing Trial Balance',
            10 => 'Reverse (Optional Step)',
        ];
    }

    protected function notifyStudentAboutFeedback(User $student, PerformanceTask $task, int $step, $instructor)
    {
        try {
            $stepTitle      = $this->stepTitles()[$step] ?? "Step {$step}";
            $instructorName = $instructor->user->name ?? 'Your instructor';

            SystemNotification::create([
                'user_id'    => $student->id,
                'title'      => 'New Feedback Received',
                'message'    => "{$instructorName} provided feedback on your submission for {$task->title} - {$stepTitle}",
                'type'       => 'info',
                'link'       => $this->getStudentTaskLink($task->id),
                'is_read'    => false,
                'expires_at' => now()->addDays(30),
            ]);
        } catch (Exception $e) {
            Log::error("Failed to notify student about feedback: " . $e->getMessage());
        }
    }

    protected function getStudentTaskLink(int $taskId): string
    {
        $routes = [
            ['students.performance-tasks.show', ['task' => $taskId]],
            ['students.performance-tasks.show', ['id'   => $taskId]],
            ['students.performance-tasks.show', $taskId],
        ];

        foreach ($routes as [$name, $params]) {
            try {
                return route($name, $params);
            } catch (\Exception $e) {
                continue;
            }
        }

        return url("/students/performance-tasks/{$taskId}");
    }

    protected function bulkNotifyStudents(array $feedbackData): int
    {
        $count = 0;
        foreach ($feedbackData as $data) {
            try {
                $student    = User::find($data['student_id']);
                $task       = PerformanceTask::find($data['task_id']);
                $instructor = auth()->user()->instructor;
                if ($student && $task) {
                    $this->notifyStudentAboutFeedback($student, $task, $data['step'], $instructor);
                    $count++;
                }
            } catch (Exception $e) {
                Log::error("Bulk notification failed for student {$data['student_id']}: " . $e->getMessage());
            }
        }
        return $count;
    }

    public function notifyPendingFeedback(int $daysOld = 7): void
    {
        $instructor = auth()->user()->instructor;

        PerformanceTaskSubmission::where('needs_feedback', true)
            ->whereHas('task', fn($q) => $q->where('instructor_id', $instructor->id))
            ->where('created_at', '<=', now()->subDays($daysOld))
            ->with(['student.user', 'task'])
            ->each(function ($submission) {
                try {
                    SystemNotification::create([
                        'user_id'    => $submission->student->user->id,
                        'title'      => 'Feedback Pending',
                        'message'    => "Your submission for {$submission->task->title} - Step {$submission->step} is still being reviewed",
                        'type'       => 'warning',
                        'link'       => $this->getStudentTaskLink($submission->task->id),
                        'is_read'    => false,
                        'expires_at' => now()->addDays(14),
                    ]);
                } catch (Exception $e) {
                    Log::error("Failed to send pending feedback notification: " . $e->getMessage());
                }
            });
    }
}