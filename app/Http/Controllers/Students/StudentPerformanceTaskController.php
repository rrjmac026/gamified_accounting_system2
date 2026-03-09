<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\PerformanceTask;
use App\Models\PerformanceTaskSubmission;
use App\Models\PerformanceTaskAnswerSheet;
use App\Models\PerformanceTaskExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\Loggable;

class StudentPerformanceTaskController extends Controller
{
	use Loggable;

    /**
     * Show list of performance tasks assigned to the logged-in student
     */
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;

        $performanceTasks = PerformanceTask::whereHas('section.students', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->with(['section', 'subject', 'instructor'])
            ->latest()
            ->get();

        $performanceTasks->each(function ($task) use ($student) {
            $submittedSteps = PerformanceTaskSubmission::where('task_id', $task->id)
                ->where('student_id', $student->id)
                ->distinct('step')
                ->pluck('step')
                ->unique();

            $completedSteps = $submittedSteps->count();

            $task->progress = $completedSteps;
            $task->totalSteps = 10;
            $task->progressPercentage = $completedSteps > 0
                ? round(($completedSteps / 10) * 100, 2)
                : 0;

            $pivotData = DB::table('performance_task_student')
                ->where('performance_task_id', $task->id)
                ->where('student_id', $student->id)
                ->first();

            if ($pivotData) {
                $task->score        = $pivotData->score ?? 0;
                $task->xp_earned    = $pivotData->xp_earned ?? 0;
                $task->status       = $pivotData->status ?? 'in-progress';
                $task->submitted_at = $pivotData->submitted_at ?? null;
            } else {
                $task->score        = 0;
                $task->xp_earned    = 0;
                $task->status       = 'in-progress';
                $task->submitted_at = null;
            }

            $task->max_score = $task->max_score ?? 1000;
            $task->gradePercentage = $task->max_score > 0
                ? round(($task->score / $task->max_score) * 100, 2)
                : 0;

            $task->deadlineStatus = $this->getDeadlineStatus($task);
        });

        return view('students.performance-tasks.index', compact('performanceTasks'));
    }

    /**
     * Show progress page for the most recent active performance task
     */
    public function progress($taskId = null)
    {
        $user = auth()->user();

        $performanceTask = PerformanceTask::whereHas('section.students', function ($query) use ($user) {
                $query->where('student_id', $user->student->id);
            })
            ->when($taskId, function ($query) use ($taskId) {
                $query->where('id', $taskId);
            })
            ->latest()
            ->first();

        if (!$performanceTask) {
            return redirect()->route('students.performance-tasks.index')
                ->with('error', 'Performance task not found or not assigned to you.');
        }

        $deadlineStatus = $this->getDeadlineStatus($performanceTask);
        if ($deadlineStatus['canSubmit'] === false) {
            return redirect()->route('students.performance-tasks.index')
                ->with('error', $deadlineStatus['message']);
        }

        $completedSteps = PerformanceTaskSubmission::where([
            'task_id' => $performanceTask->id,
            'student_id' => $user->student->id,
        ])->pluck('step')->toArray();

		$this->logActivity('viewed performance task progress', [
			'student_id' => $user->student->id ?? null,
			'task_id' => $performanceTask->id ?? null,
		]);

        return view('students.performance-tasks.progress', compact('performanceTask', 'completedSteps', 'deadlineStatus'));
    }

    /**
     * Load the step view (e.g. step-1, step-2, ...)
     */
    public function step($id, $step)
    {
        $user = auth()->user();

        abort_if($step < 1 || $step > 10, 404);

        $performanceTask = PerformanceTask::where('id', $id)
            ->whereHas('section.students', function ($query) use ($user) {
                $query->where('student_id', $user->student->id);
            })
            ->first();

        if (!$performanceTask) {
            return redirect()->route('students.performance-tasks.index')
                ->with('error', 'No active performance task found.');
        }

        $deadlineStatus = $this->getDeadlineStatus($performanceTask);
        if ($deadlineStatus['canSubmit'] === false) {
            return redirect()->route('students.performance-tasks.index')
                ->with('error', $deadlineStatus['message']);
        }

        $submission = PerformanceTaskSubmission::where([
            'task_id'    => $performanceTask->id,
            'student_id' => $user->student->id,
            'step'       => $step,
        ])->first();

        // ── NEW: Load exercises for this step ──────────────────────────────────
        $exercises = PerformanceTaskExercise::where([
            'performance_task_id' => $performanceTask->id,
            'step'                => $step,
        ])->orderBy('order')->get();

        // Backward compatibility — fall back to old answer sheet if no exercises
        $answerSheet = $exercises->isEmpty()
            ? PerformanceTaskAnswerSheet::where([
                'performance_task_id' => $performanceTask->id,
                'step'                => $step,
            ])->first()
            : null;
        // ──────────────────────────────────────────────────────────────────────

        $completedSteps = PerformanceTaskSubmission::where([
            'task_id'    => $performanceTask->id,
            'student_id' => $user->student->id,
        ])->pluck('step')->toArray();

        if ($step > 1 && !in_array($step - 1, $completedSteps)) {
            return redirect()->route('students.performance-tasks.step', [
                'id'   => $performanceTask->id,
                'step' => $step - 1,
            ])->with('error', "You must complete Step " . ($step - 1) . " first.");
        }

        return view("students.performance-tasks.step-$step", [
            'performanceTask' => $performanceTask,
            'submission'      => $submission,
            'answerSheet'     => $answerSheet,
            'exercises'       => $exercises,       // ← NEW
            'completedSteps'  => $completedSteps,
            'deadlineStatus'  => $deadlineStatus,
            'step'            => $step,
        ]);
    }

    /**
     * Save or retry a step submission
     */
    public function saveStep(Request $request, $id, $step)
    {
        $user = auth()->user();

        $task = PerformanceTask::where('id', $id)
            ->whereHas('section.students', function ($query) use ($user) {
                $query->where('student_id', $user->student->id);
            })
            ->first();

        if (!$task) {
            return back()->with('error', 'Performance task not found or not assigned to you.');
        }

        $deadlineStatus = $this->getDeadlineStatus($task);
        if ($deadlineStatus['canSubmit'] === false) {
            return back()->with('error', $deadlineStatus['message']);
        }

        try {
            $validated = $request->validate([
                'submission_data' => 'required|string',
                'exercise_id'     => 'nullable|integer',   // ← NEW
            ]);

            $submission = PerformanceTaskSubmission::where([
                'task_id'    => $task->id,
                'student_id' => $user->student->id,
                'step'       => $step,
            ])->first();

            if ($submission && $submission->attempts >= $task->max_attempts) {
                return back()->with('error', "You have reached the maximum of {$task->max_attempts} attempts for this step. Your current score is {$submission->score}.");
            }

            $previousStatus = $submission ? $submission->status : null;
            $previousScore  = $submission ? $submission->score  : 0;

            if (!$submission) {
                $submission = new PerformanceTaskSubmission([
                    'task_id'    => $task->id,
                    'student_id' => $user->student->id,
                    'step'       => $step,
                    'attempts'   => 0,
                ]);
            }

            // ── NEW: Record which exercise the student used ────────────────────
            $exerciseId = $validated['exercise_id'] ?? null;
            if ($exerciseId) {
                $submission->exercise_id = $exerciseId;
            }
            // ─────────────────────────────────────────────────────────────────

            $studentData    = $validated['submission_data'];
            $decodedData    = json_decode($studentData, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->with('error', 'Invalid submission data format.');
            }

            if (isset($decodedData['data']) && isset($decodedData['metadata'])) {
                $studentDataArray = $decodedData['data'];
            } else {
                $studentDataArray = $decodedData;
            }
            $submission->submission_data = $studentData;

            $submission->attempts = $submission->attempts + 1;
            $currentAttempt = $submission->attempts;

            // ── NEW: Resolve the correct answer key ───────────────────────────
            // Priority 1: exercise linked in this submission
            // Priority 2: legacy PerformanceTaskAnswerSheet
            $correctDataRaw = null;

            if ($exerciseId) {
                $exercise = PerformanceTaskExercise::find($exerciseId);
                if ($exercise && $exercise->correct_data) {
                    $correctDataRaw = is_string($exercise->correct_data)
                        ? json_decode($exercise->correct_data, true)
                        : $exercise->correct_data;

                    \Log::info("Step {$step} - Using exercise #{$exerciseId} ({$exercise->title}) as answer key");
                }
            }

            if (!$correctDataRaw) {
                // Fall back to legacy answer sheet
                $answerSheet = PerformanceTaskAnswerSheet::where([
                    'performance_task_id' => $task->id,
                    'step'                => $step,
                ])->first();

                if ($answerSheet && $answerSheet->correct_data) {
                    $correctDataRaw = is_string($answerSheet->correct_data)
                        ? json_decode($answerSheet->correct_data, true)
                        : $answerSheet->correct_data;

                    \Log::info("Step {$step} - Using legacy answer sheet as answer key");
                }
            }
            // ─────────────────────────────────────────────────────────────────

            $awardXp      = false;
            $errorCount   = 0;
            $totalCells   = 0;
            $maxScorePerStep = $task->max_score / 10;

            if ($correctDataRaw) {
                // Extract data array if wrapped in {data, metadata} structure
                if (is_array($correctDataRaw) && isset($correctDataRaw['data'])) {
                    $correctData = $correctDataRaw['data'];
                } else {
                    $correctData = $correctDataRaw;
                }

                \Log::info("Step {$step} - Data sample comparison", [
                    'student_row_0' => $studentDataArray[0] ?? 'missing',
                    'correct_row_0' => $correctData[0]      ?? 'missing',
                ]);

                $errorDetails = $this->checkAnswersWithErrors($studentDataArray, $correctData, $step);
                $errorCount   = $errorDetails['errorCount'];
                $totalCells   = $errorDetails['totalCells'];

                if (isset($errorDetails['cellCountMismatch']) && $errorDetails['cellCountMismatch']) {
                    $submission->status = 'wrong';
                    $submission->score  = 0;

                    $remainingAttempts = $task->max_attempts - $currentAttempt;
                    $attemptInfo = $remainingAttempts > 0
                        ? " You have {$remainingAttempts} attempt(s) remaining."
                        : " No attempts remaining.";

                    $submission->remarks = $deadlineStatus['isLate']
                        ? "Wrong number of entries detected. Your submission has extra or missing data. Score: 0/{$maxScorePerStep}.{$attemptInfo} (Late submission)"
                        : "Wrong number of entries detected. Your submission has extra or missing data. Score: 0/{$maxScorePerStep}.{$attemptInfo}";

                    $awardXp = false;

                } else {
                    $wrongAttemptsBefore = ($currentAttempt > 1) ? ($currentAttempt - 1) : 0;
                    $cumulativePenalty   = $wrongAttemptsBefore * $task->deduction_per_error;
                    $isPerfect           = ($errorCount === 0);
                    $calculatedScore     = max(0, $maxScorePerStep - $cumulativePenalty);
                    $passingScore        = $calculatedScore * 0.7;

                    $submission->score = round($calculatedScore, 2);

                    if ($isPerfect && $calculatedScore > 0) {
                        $submission->status = 'correct';
                        $awardXp = ($previousStatus !== 'correct');
                        $deductionInfo = ($cumulativePenalty > 0) ? " (-{$cumulativePenalty} penalty from previous attempts)" : "";
                        $submission->remarks = $deadlineStatus['isLate']
                            ? "Perfect! {$calculatedScore}/{$maxScorePerStep} points{$deductionInfo} (Late submission)"
                            : "Perfect! {$calculatedScore}/{$maxScorePerStep} points{$deductionInfo}";

                    } elseif ($calculatedScore >= $passingScore && $calculatedScore > 0) {
                        $submission->status = 'passed';
                        $awardXp = ($previousStatus !== 'correct' && $previousStatus !== 'passed');
                        $deductionInfo = ($cumulativePenalty > 0) ? " (-{$cumulativePenalty} penalty from previous attempts)" : "";
                        $submission->remarks = $deadlineStatus['isLate']
                            ? "Good job! {$calculatedScore}/{$maxScorePerStep} points{$deductionInfo}. {$errorCount} error(s) found. (Late submission)"
                            : "Good job! {$calculatedScore}/{$maxScorePerStep} points{$deductionInfo}. {$errorCount} error(s) found.";

                    } else {
                        $submission->status = 'wrong';
                        $awardXp = false;

                        $remainingAttempts = $task->max_attempts - $currentAttempt;
                        $attemptInfo = $remainingAttempts > 0
                            ? " You have {$remainingAttempts} attempt(s) remaining."
                            : " No attempts remaining.";

                        $futureMaxScore   = max(0, $maxScorePerStep - ($currentAttempt * $task->deduction_per_error));
                        $penaltyWarning   = ($remainingAttempts > 0 && $futureMaxScore > 0)
                            ? " Next attempt max score: {$futureMaxScore}/{$maxScorePerStep}."
                            : "";

                        $submission->remarks = $deadlineStatus['isLate']
                            ? "Score: {$calculatedScore}/{$maxScorePerStep}. {$errorCount} error(s) found.{$attemptInfo}{$penaltyWarning} (Late submission)"
                            : "Score: {$calculatedScore}/{$maxScorePerStep}. {$errorCount} error(s) found.{$attemptInfo}{$penaltyWarning}";
                    }
                }

            } else {
                // No answer key found at all
                $submission->status  = 'in-progress';
                $submission->score   = 0;
                $submission->remarks = 'Answer key not found for this step.';
            }

            \Log::info("About to save submission", [
                'step'            => $step,
                'student_id'      => $user->student->id,
                'exercise_id'     => $exerciseId,
                'previous_status' => $previousStatus,
                'new_status'      => $submission->status,
                'score'           => $submission->score,
                'errors'          => $errorCount,
            ]);

            $saved = $submission->save();

            if (!$saved) {
                \Log::error("Failed to save submission for student {$user->student->id}, step {$step}");
                return back()->with('error', 'Failed to save your submission. Please try again.');
            }

            $submission->refresh();

            $this->logActivity('submitted performance task step', [
                'student_id'  => $user->student->id ?? null,
                'task_id'     => $task->id ?? null,
                'step'        => $step,
                'exercise_id' => $exerciseId,
                'status'      => $submission->status ?? null,
                'score'       => $submission->score  ?? null,
                'attempts'    => $submission->attempts ?? null,
            ]);

            $this->recordHistory($user->student->id, $task, $step, $submission, $deadlineStatus['isLate'], $errorCount);
            $this->syncSubmissionToPivot($user->student->id, $task->id, $step, $submission);

            if ($step >= 10) {
                $this->storeFinalGrade($user->student->id, $task);
            }

            $this->logPerformance($user->student, $task, $step, $submission, $deadlineStatus['isLate'], $errorCount);

            $remainingAttempts = $task->max_attempts - $currentAttempt;

            $message = "Step {$step} submitted! (Attempt {$currentAttempt}/{$task->max_attempts}) - Score: {$submission->score}/{$maxScorePerStep}";

            if ($previousScore > 0 && $submission->score < $previousScore) {
                $difference = round($previousScore - $submission->score, 2);
                $message .= " (⬇️ -{$difference} from previous)";
            }

            if ($errorCount > 0) {
                $message .= " | {$errorCount} error(s) detected";
            }

            if ($remainingAttempts > 0 && $submission->status === 'wrong') {
                $message .= " | {$remainingAttempts} attempt(s) remaining";
            } elseif ($remainingAttempts === 0 && $submission->status === 'wrong') {
                $message .= " | ⚠️ No attempts remaining for this step";
            }

            if ($deadlineStatus['isLate']) {
                $message .= " ⚠️ Late submission";
            }

            if ($awardXp && $submission->score > 0) {
                $this->awardXpForStep($user->student, $task, $step, $deadlineStatus['isLate'], $submission->score);
                $xpEarned = $this->calculateStepXp($step, $deadlineStatus['isLate'], $submission->score, $maxScorePerStep, $task->xp_reward ?? 100);
                $message .= " 🎉 You earned {$xpEarned} XP!";
            }

            if ($step >= 10) {
                if ($awardXp && $submission->score > 0) {
                    $this->awardCompletionBonus($user->student, $task);
                }
                $this->logTaskCompletion($user->student, $task);

                return redirect()->route('students.performance-tasks.index')
                    ->with('success', 'You have completed all 10 steps! Final score: ' . $submission->score . '/' . $maxScorePerStep);
            }

            return redirect()->route('students.performance-tasks.step', [
                'id'   => $task->id,
                'step' => $step + 1,
            ])->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Performance Task Submission Error: ' . $e->getMessage(), [
                'student_id' => $user->student->id ?? null,
                'task_id'    => $id,
                'step'       => $step,
                'trace'      => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error saving your submission. Please try again.');
        }
    }

    /**
     * Show correct answers for a step (after max attempts reached)
     */
    public function showAnswers($id, $step)
    {
        $user = auth()->user();
        abort_if($step < 1 || $step > 10, 404);

        $performanceTask = PerformanceTask::where('id', $id)
            ->whereHas('section.students', function ($query) use ($user) {
                $query->where('student_id', $user->student->id);
            })
            ->first();

        if (!$performanceTask) {
            return redirect()->route('students.performance-tasks.index')
                ->with('error', 'Performance task not found.');
        }

        $submission = PerformanceTaskSubmission::where([
            'task_id'    => $performanceTask->id,
            'student_id' => $user->student->id,
            'step'       => $step,
        ])->first();

        if (!$submission || $submission->attempts < $performanceTask->max_attempts) {
            return redirect()->route('students.performance-tasks.step', [
                'id'   => $performanceTask->id,
                'step' => $step,
            ])->with('error', 'You must complete all attempts before viewing answers.');
        }

        // ── NEW: resolve answer key from exercise first, fall back to sheet ──
        $exercise    = null;
        $answerSheet = null;

        if ($submission->exercise_id) {
            $exercise = PerformanceTaskExercise::find($submission->exercise_id);
        }

        if (!$exercise) {
            $answerSheet = PerformanceTaskAnswerSheet::where([
                'performance_task_id' => $performanceTask->id,
                'step'                => $step,
            ])->first();
        }

        if (!$exercise && !$answerSheet) {
            return back()->with('error', 'Answer sheet not available for this step.');
        }
        // ─────────────────────────────────────────────────────────────────────

		$this->logActivity('viewed answer sheet', [
			'student_id'  => $user->student->id ?? null,
			'task_id'     => $performanceTask->id ?? null,
			'step'        => $step,
            'exercise_id' => $submission->exercise_id ?? null,
		]);

        return view("students.performance-tasks.answers.view", [
            'performanceTask' => $performanceTask,
            'answerSheet'     => $answerSheet,   // may be null
            'exercise'        => $exercise,      // ← NEW, may be null
            'submission'      => $submission,
            'step'            => $step,
        ]);
    }

    /**
     * Show attempt history list for a step
     */
    public function stepHistory(int $id, int $step)
    {
        $user = auth()->user();

        abort_if($step < 1 || $step > 10, 404);

        $task = \App\Models\PerformanceTask::where('id', $id)
            ->whereHas('section.students', function ($q) use ($user) {
                $q->where('student_id', $user->student->id);
            })
            ->firstOrFail();

        $histories = \App\Models\PerformanceTaskSubmissionHistory::where([
                'task_id'    => $task->id,
                'student_id' => $user->student->id,
                'step'       => $step,
            ])
            ->orderBy('attempt_number')
            ->get();

        $stepTitles = $this->stepTitlesArray();

        return view('students.performance-tasks.step-history', [
            'performanceTask' => $task,
            'histories'       => $histories,
            'step'            => $step,
            'stepTitles'      => $stepTitles,
        ]);
    }

    /**
     * Show the spreadsheet data of a specific history attempt
     */
    public function historyDetail(int $id, int $step, int $attempt)
    {
        $user = auth()->user();

        abort_if($step < 1 || $step > 10, 404);

        $task = \App\Models\PerformanceTask::where('id', $id)
            ->whereHas('section.students', function ($q) use ($user) {
                $q->where('student_id', $user->student->id);
            })
            ->firstOrFail();

        $history = \App\Models\PerformanceTaskSubmissionHistory::where([
                'task_id'        => $task->id,
                'student_id'     => $user->student->id,
                'step'           => $step,
                'attempt_number' => $attempt,
            ])
            ->firstOrFail();

        // ── NEW: resolve answer key from exercise first, fall back to sheet ──
        $exercise    = null;
        $answerSheet = null;

        // Get the current (most recent) submission to check exercise_id
        $submission = PerformanceTaskSubmission::where([
            'task_id'    => $task->id,
            'student_id' => $user->student->id,
            'step'       => $step,
        ])->first();

        if ($submission && $submission->exercise_id) {
            $exercise = PerformanceTaskExercise::find($submission->exercise_id);
        }

        if (!$exercise) {
            $answerSheet = PerformanceTaskAnswerSheet::where([
                'performance_task_id' => $task->id,
                'step'                => $step,
            ])->first();
        }
        // ─────────────────────────────────────────────────────────────────────

        $stepTitles = $this->stepTitlesArray();

        return view('students.performance-tasks.history-detail', compact(
            'task', 'history', 'answerSheet', 'exercise', 'step', 'attempt', 'stepTitles'
        ));
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function stepTitlesArray(): array
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

    /**
     * Log performance metrics for a step submission
     */
    private function logPerformance($student, $task, $step, $submission, $isLate, $errorCount = 0)
    {
        try {
            \App\Models\PerformanceLog::create([
                'student_id'         => $student->id,
                'subject_id'         => $task->subject_id,
                'task_id'            => $task->id,
                'performance_metric' => "step_{$step}_score",
                'value'              => $submission->score,
                'recorded_at'        => now(),
            ]);

            \App\Models\PerformanceLog::create([
                'student_id'         => $student->id,
                'subject_id'         => $task->subject_id,
                'task_id'            => $task->id,
                'performance_metric' => "step_{$step}_errors",
                'value'              => $errorCount,
                'recorded_at'        => now(),
            ]);

            \App\Models\PerformanceLog::create([
                'student_id'         => $student->id,
                'subject_id'         => $task->subject_id,
                'task_id'            => $task->id,
                'performance_metric' => "step_{$step}_attempts",
                'value'              => $submission->attempts,
                'recorded_at'        => now(),
            ]);

            \App\Models\PerformanceLog::create([
                'student_id'         => $student->id,
                'subject_id'         => $task->subject_id,
                'task_id'            => $task->id,
                'performance_metric' => "step_{$step}_late_submission",
                'value'              => $isLate ? 1 : 0,
                'recorded_at'        => now(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Performance logging error: ' . $e->getMessage());
        }
    }

    private function checkAnswersWithErrors($studentData, $correctData, $step = null)
    {
        $errorCount = 0;
        $totalCells = 0;

        if (!is_array($studentData) || !is_array($correctData)) {
            \Log::error('Invalid data format in checkAnswersWithErrors', [
                'student_is_array' => is_array($studentData),
                'correct_is_array' => is_array($correctData)
            ]);
            return ['errorCount' => 999, 'totalCells' => 1];
        }

        $studentCellCount = 0;
        $correctCellCount = 0;

        foreach ($studentData as $row) {
            foreach ($row as $cell) {
                if ($this->normalizeValue($cell) !== '') $studentCellCount++;
            }
        }

        foreach ($correctData as $row) {
            foreach ($row as $cell) {
                if ($this->normalizeValue($cell) !== '') $correctCellCount++;
            }
        }

        \Log::info("Cell count comparison", [
            'step'           => $step,
            'student_cells'  => $studentCellCount,
            'correct_cells'  => $correctCellCount,
        ]);

        if ($studentCellCount !== $correctCellCount) {
            \Log::warning("Cell count mismatch - automatic fail", [
                'step'       => $step,
                'expected'   => $correctCellCount,
                'got'        => $studentCellCount,
                'difference' => abs($studentCellCount - $correctCellCount),
            ]);

            return [
                'errorCount'        => 999999,
                'totalCells'        => max($correctCellCount, 1),
                'cellCountMismatch' => true,
            ];
        }

        $maxRows = max(count($studentData), count($correctData));

        for ($rowIndex = 0; $rowIndex < $maxRows; $rowIndex++) {
            if ($step === 4 && $rowIndex < 4) continue;

            $correctRow = $correctData[$rowIndex] ?? [];
            $studentRow = $studentData[$rowIndex] ?? [];
            $maxCols    = max(count($studentRow), count($correctRow));

            for ($colIndex = 0; $colIndex < $maxCols; $colIndex++) {
                $correctValue     = $correctRow[$colIndex] ?? null;
                $studentValue     = $studentRow[$colIndex] ?? null;
                $normalizedCorrect = $this->normalizeValue($correctValue);
                $normalizedStudent = $this->normalizeValue($studentValue);

                if ($normalizedCorrect !== '') {
                    $totalCells++;
                    if ($normalizedStudent !== $normalizedCorrect) {
                        $errorCount++;
                        if ($errorCount <= 5) {
                            \Log::info("Mismatch at Row {$rowIndex}, Col {$colIndex}", [
                                'student_normalized' => $normalizedStudent,
                                'correct_normalized' => $normalizedCorrect,
                            ]);
                        }
                    }
                }
            }
        }

        return [
            'errorCount' => $errorCount,
            'totalCells' => $totalCells,
        ];
    }

    /**
     * Log overall task completion metrics
     */
    private function logTaskCompletion($student, $task)
    {
        try {
            $submissions     = PerformanceTaskSubmission::where('task_id', $task->id)
                ->where('student_id', $student->id)
                ->get();
            $correctCount    = $submissions->where('status', 'correct')->count();
            $totalSteps      = $submissions->count();
            $overallAccuracy = $totalSteps > 0 ? ($correctCount / $totalSteps) * 100 : 0;
            $totalAttempts   = $submissions->sum('attempts');
            $avgAttempts     = $totalSteps > 0 ? $totalAttempts / $totalSteps : 0;

            foreach ([
                ['task_overall_accuracy',       round($overallAccuracy, 2)],
                ['task_total_attempts',          $totalAttempts],
                ['task_avg_attempts_per_step',   round($avgAttempts, 2)],
                ['task_completed',               1],
            ] as [$metric, $value]) {
                \App\Models\PerformanceLog::create([
                    'student_id'         => $student->id,
                    'subject_id'         => $task->subject_id,
                    'task_id'            => $task->id,
                    'performance_metric' => $metric,
                    'value'              => $value,
                    'recorded_at'        => now(),
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Task completion logging error: ' . $e->getMessage());
        }
    }

    private function awardXpForStep($student, $task, $step, $isLate = false, $score = 0)
    {
        $xpAmount = $this->calculateStepXp($step, $isLate, $score, $task->max_score, $task->xp_reward ?? 100);

        if ($xpAmount > 0) {
            $student->xpTransactions()->create([
                'amount'       => $xpAmount,
                'type'         => 'earned',
                'source'       => 'performance_task',
                'source_id'    => $task->id,
                'description'  => "Step {$step}: {$score}/{$task->max_score} points - {$task->title}",
                'processed_at' => now(),
            ]);
        }
    }

    private function calculateStepXp($step, $isLate, $score, $maxScore, $taskXpReward = 100)
    {
        $xpPerStep  = ($taskXpReward * 0.9) / 10;
        $percentage = $maxScore > 0 ? ($score / $maxScore) : 0;
        $xp         = round($xpPerStep * $percentage, 2);
        if ($isLate) $xp = round($xp * 0.5, 2);
        return max(0, $xp);
    }

    private function awardCompletionBonus($student, $task)
    {
        $bonusXp = round(($task->xp_reward ?? 100) * 0.1, 2);
        if ($bonusXp > 0) {
            $student->xpTransactions()->create([
                'amount'       => $bonusXp,
                'type'         => 'earned',
                'source'       => 'performance_task',
                'source_id'    => $task->id,
                'description'  => "Completion Bonus: {$task->title}",
                'processed_at' => now(),
            ]);
        }
    }

    private function getDeadlineStatus($task)
    {
        $now = now();

        if (!$task->due_date) {
            return ['canSubmit' => true, 'isLate' => false, 'status' => 'open', 'message' => 'No deadline set for this task.'];
        }

        if ($task->late_until && $now->greaterThan($task->late_until)) {
            return ['canSubmit' => false, 'isLate' => true, 'status' => 'closed', 'message' => 'This task is no longer accepting submissions. The deadline has passed.'];
        }

        if ($task->late_until && $now->greaterThan($task->due_date) && $now->lessThanOrEqualTo($task->late_until)) {
            $hoursRemaining = $now->diffInHours($task->late_until);
            return ['canSubmit' => true, 'isLate' => true, 'status' => 'late', 'message' => "⚠️ Late submission period. Final deadline: {$task->late_until->format('M d, Y h:i A')} ({$hoursRemaining} hours remaining)"];
        }

        if (!$task->late_until && $now->greaterThan($task->due_date)) {
            return ['canSubmit' => false, 'isLate' => true, 'status' => 'closed', 'message' => 'This task is past the due date and no longer accepting submissions.'];
        }

        $hoursRemaining = $now->diffInHours($task->due_date);
        return ['canSubmit' => true, 'isLate' => false, 'status' => 'on-time', 'message' => "Due: {$task->due_date->format('M d, Y h:i A')} ({$hoursRemaining} hours remaining)"];
    }

    private function compareAnswers($studentData, $correctData)
    {
        if (!is_array($studentData) || !is_array($correctData)) return false;

        foreach ($correctData as $rowIndex => $correctRow) {
            if (!isset($studentData[$rowIndex])) return false;
            $studentRow = $studentData[$rowIndex];
            foreach ($correctRow as $colIndex => $correctValue) {
                if ($correctValue === null || $correctValue === '' || $correctValue === 0) continue;
                $studentValue = $studentRow[$colIndex] ?? null;
                if (!$this->valuesMatch($studentValue, $correctValue)) return false;
            }
        }

        return true;
    }

    private function valuesMatch($value1, $value2)
    {
        return $this->normalizeValue($value1) === $this->normalizeValue($value2);
    }

    private function normalizeValue($value)
    {
        if ($value === null || $value === '' || $value === 0) return '';
        $stringValue = (string)$value;
        $cleaned = preg_replace('/[,₱\s]/', '', $stringValue);
        if (is_numeric($cleaned)) return number_format((float)$cleaned, 2, '.', '');
        return strtolower(trim($stringValue));
    }

    public function submit()
    {
        return back()->with('success', 'Performance task submitted!');
    }

    public function show($id)
    {
        $user = auth()->user();
        $task = PerformanceTask::where('id', $id)
            ->whereHas('section.students', function ($query) use ($user) {
                $query->where('student_id', $user->student->id);
            })
            ->firstOrFail();

        return redirect()->route('students.performance-tasks.progress', ['taskId' => $id]);
    }

    private function syncSubmissionToPivot($studentId, $taskId, $step, $submission)
    {
        try {
            $exists = DB::table('performance_task_student')
                ->where('performance_task_id', $taskId)
                ->where('student_id', $studentId)
                ->exists();

            if ($exists) {
                DB::table('performance_task_student')
                    ->where('performance_task_id', $taskId)
                    ->where('student_id', $studentId)
                    ->update([
                        'status'       => 'in_progress',
                        'attempts'     => DB::raw('attempts + 1'),
                        'submitted_at' => now(),
                        'updated_at'   => now()
                    ]);
            } else {
                DB::table('performance_task_student')->insert([
                    'performance_task_id' => $taskId,
                    'student_id'          => $studentId,
                    'status'              => 'in_progress',
                    'attempts'            => 1,
                    'submitted_at'        => now(),
                    'created_at'          => now(),
                    'updated_at'          => now()
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to sync to pivot table: " . $e->getMessage());
        }
    }

    private function storeFinalGrade($studentId, $task)
    {
        try {
            $submissions  = PerformanceTaskSubmission::where('task_id', $task->id)
                ->where('student_id', $studentId)
                ->get();

            if ($submissions->isEmpty()) return;

            $sumOfScores = $submissions->sum('score');
            $finalScore  = min($sumOfScores, $task->max_score);
            $percentage  = $task->max_score > 0 ? round(($finalScore / $task->max_score) * 100, 2) : 0;

            $totalEarnedXp = DB::table('xp_transactions')
                ->where('student_id', $studentId)
                ->where('source', 'performance_task')
                ->where('source_id', $task->id)
                ->sum('amount');

            $cappedXp = min($totalEarnedXp, $task->xp_reward ?? 1000);

            DB::table('performance_task_student')
                ->where('performance_task_id', $task->id)
                ->where('student_id', $studentId)
                ->update([
                    'status'     => 'graded',
                    'score'      => round($finalScore, 2),
                    'xp_earned'  => round($cappedXp, 2),
                    'graded_at'  => now(),
                    'feedback'   => sprintf(
                        "Task completed! Final score: %.2f / %d (%.2f%%) | XP Earned: %d",
                        $finalScore, $task->max_score, $percentage, $cappedXp
                    ),
                    'updated_at' => now(),
                ]);

            $this->logActivity('stored final grade', [
                'student_id'  => $studentId,
                'task_id'     => $task->id,
                'final_score' => round($finalScore, 2),
                'percentage'  => $percentage,
                'xp_earned'   => round($cappedXp, 2),
            ]);

        } catch (\Exception $e) {
            \Log::error("Failed to store final grade: " . $e->getMessage());
        }
    }

    public function myProgress($id)
    {
        $user = auth()->user();

        $task = PerformanceTask::where('id', $id)
            ->whereHas('section.students', function ($query) use ($user) {
                $query->where('student_id', $user->student->id);
            })
            ->firstOrFail();

        $submissions = PerformanceTaskSubmission::where('task_id', $task->id)
            ->where('student_id', $user->student->id)
            ->orderBy('step')
            ->get();

        $stepTitles = $this->stepTitlesArray();

        $submissionDetails = [];
        foreach ($submissions as $submission) {
            $submissionDetails[$submission->step] = [
                'step_title'          => $stepTitles[$submission->step] ?? "Step {$submission->step}",
                'status'              => $submission->status,
                'score'               => $submission->score,
                'attempts'            => $submission->attempts,
                'remarks'             => $submission->remarks,
                'instructor_feedback' => $submission->instructor_feedback,
                'feedback_given_at'   => $submission->feedback_given_at,
                'submitted_at'        => $submission->created_at,
                'updated_at'          => $submission->updated_at,
            ];
        }

        $statistics = [
            'total_score'       => $submissions->sum('score'),
            'total_attempts'    => $submissions->sum('attempts'),
            'answered_steps'    => $submissions->count(),
            'correct_steps'     => $submissions->where('status', 'correct')->count(),
            'passed_steps'      => $submissions->where('status', 'passed')->count(),
            'wrong_steps'       => $submissions->where('status', 'wrong')->count(),
            'completed_steps'   => $submissions->whereIn('status', ['correct', 'passed'])->count(),
            'in_progress_steps' => 10 - $submissions->count(),
            'feedback_count'    => $submissions->whereNotNull('instructor_feedback')->count(),
        ];

        return view('students.performance-tasks.my-progress', compact(
            'task', 'submissionDetails', 'stepTitles', 'statistics'
        ));
    }

    public function viewFeedback($id, $step)
    {
        $user = auth()->user();

        $task = PerformanceTask::where('id', $id)
            ->whereHas('section.students', function ($query) use ($user) {
                $query->where('student_id', $user->student->id);
            })
            ->firstOrFail();

        $submission = PerformanceTaskSubmission::where([
            'task_id'    => $task->id,
            'student_id' => $user->student->id,
            'step'       => $step,
        ])->firstOrFail();

        if (!$submission->instructor_feedback) {
            return redirect()->route('students.performance-tasks.my-progress', $task->id)
                ->with('info', 'No instructor feedback available for this step yet.');
        }

        $stepTitles = $this->stepTitlesArray();

        return view('students.performance-tasks.view-feedback', compact(
            'task', 'submission', 'step', 'stepTitles'
        ));
    }

    private function recordHistory(
        int $studentId,
        \App\Models\PerformanceTask $task,
        int $step,
        \App\Models\PerformanceTaskSubmission $submission,
        bool $isLate,
        int $errorCount
    ): void {
        try {
            \App\Models\PerformanceTaskSubmissionHistory::create([
                'submission_id'   => $submission->id,
                'task_id'         => $task->id,
                'student_id'      => $studentId,
                'step'            => $step,
                'attempt_number'  => $submission->attempts,
                'submission_data' => $submission->submission_data
                    ? (is_string($submission->submission_data) ? $submission->submission_data : json_encode($submission->submission_data))
                    : null,
                'status'          => $submission->status,
                'score'           => $submission->score,
                'remarks'         => $submission->remarks,
                'error_count'     => min($errorCount, 32767),
                'is_late'         => $isLate,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to record submission history: ' . $e->getMessage());
        }
    }
}