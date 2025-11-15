<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\PerformanceTask;
use App\Models\PerformanceTaskSubmission;
use App\Models\PerformanceTaskAnswerSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentPerformanceTaskController extends Controller
{
    /**
     * Show list of performance tasks assigned to the logged-in student
     */
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;

        // Fetch all tasks assigned to the student's section
        $performanceTasks = PerformanceTask::whereHas('section.students', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->with(['section', 'subject', 'instructor'])
            ->latest()
            ->get();

        $performanceTasks->each(function ($task) use ($student) {
            // ✅ 1. Calculate progress
            $completedSteps = PerformanceTaskSubmission::where('task_id', $task->id)
                ->where('student_id', $student->id)
                ->distinct()
                ->pluck('step')
                ->unique()
                ->count();

            $task->progress = $completedSteps;
            $task->totalSteps = 10;
            $task->progressPercentage = $completedSteps > 0
                ? round(($completedSteps / 10) * 100, 2)
                : 0;

            // ✅ 2. Fetch final grade from pivot table (if stored there)
            $pivotData = DB::table('performance_task_student')
                ->where('performance_task_id', $task->id)
                ->where('student_id', $student->id)
                ->first();

            if ($pivotData) {
                $task->score = $pivotData->score ?? 0;
                $task->xp_earned = $pivotData->xp_earned ?? 0;
                $task->status = $pivotData->status ?? 'in-progress';
            } else {
                // fallback if no pivot data yet
                $task->score = 0;
                $task->xp_earned = 0;
                $task->status = 'in-progress';
            }

            // ✅ 3. Add other computed attributes
            $task->max_score = $task->max_score ?? 1000;
            $task->gradePercentage = $task->max_score > 0
                ? round(($task->score / $task->max_score) * 100, 2)
                : 0;

            // ✅ 4. Add deadline badge
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

        // Check if submissions are still allowed
        $deadlineStatus = $this->getDeadlineStatus($performanceTask);
        if ($deadlineStatus['canSubmit'] === false) {
            return redirect()->route('students.performance-tasks.index')
                ->with('error', $deadlineStatus['message']);
        }

        $completedSteps = PerformanceTaskSubmission::where([
            'task_id' => $performanceTask->id,
            'student_id' => $user->student->id,
        ])->pluck('step')->toArray();

        return view('students.performance-tasks.progress', compact('performanceTask', 'completedSteps', 'deadlineStatus'));
    }

    /**
     * Load the step view (e.g. step-1, step-2, ...)
     */
    public function step($id, $step)
    {
        $user = auth()->user();

        abort_if($step < 1 || $step > 10, 404);

        // Find the performance task and ensure it belongs to the student's section
        $performanceTask = PerformanceTask::where('id', $id)
            ->whereHas('section.students', function ($query) use ($user) {
                $query->where('student_id', $user->student->id);
            })
            ->first();

        if (!$performanceTask) {
            return redirect()->route('students.performance-tasks.index')
                ->with('error', 'No active performance task found.');
        }

        // Check deadline restrictions before allowing step access
        $deadlineStatus = $this->getDeadlineStatus($performanceTask);
        if ($deadlineStatus['canSubmit'] === false) {
            return redirect()->route('students.performance-tasks.index')
                ->with('error', $deadlineStatus['message']);
        }

        // Check if the student has a submission for this step
        $submission = PerformanceTaskSubmission::where([
            'task_id' => $performanceTask->id,
            'student_id' => $user->student->id,
            'step' => $step,
        ])->first();

        // Get the answer sheet template for this step
        $answerSheet = PerformanceTaskAnswerSheet::where([
            'performance_task_id' => $performanceTask->id,
            'step' => $step,
        ])->first();

        // Get all completed steps by the student
        $completedSteps = PerformanceTaskSubmission::where([
            'task_id' => $performanceTask->id,
            'student_id' => $user->student->id,
        ])->pluck('step')->toArray();

        // Prevent skipping steps
        if ($step > 1 && !in_array($step - 1, $completedSteps)) {
            return redirect()->route('students.performance-tasks.step', [
                'id' => $performanceTask->id,
                'step' => $step - 1,
            ])->with('error', "You must complete Step " . ($step - 1) . " first.");
        }

        return view("students.performance-tasks.step-$step", [
            'performanceTask' => $performanceTask,
            'submission' => $submission,
            'answerSheet' => $answerSheet,
            'completedSteps' => $completedSteps,
            'deadlineStatus' => $deadlineStatus,
            'step' => $step,
        ]);
    }

    /**
     * Save or retry a step submission - FIXED METHOD SIGNATURE
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
                'submission_data' => 'required|string'
            ]);

            // Find existing submission
            $submission = PerformanceTaskSubmission::where([
                'task_id' => $task->id,
                'student_id' => $user->student->id,
                'step' => $step,
            ])->first();

            // Check attempts BEFORE incrementing
            if ($submission && $submission->attempts >= $task->max_attempts) {
                return back()->with('error', "You have reached the maximum of {$task->max_attempts} attempts for this step. Your current score is {$submission->score}.");
            }

            // Store previous status for XP logic
            $previousStatus = $submission ? $submission->status : null;
            $previousScore = $submission ? $submission->score : 0;

            // Create new submission if doesn't exist
            if (!$submission) {
                $submission = new PerformanceTaskSubmission([
                    'task_id' => $task->id,
                    'student_id' => $user->student->id,
                    'step' => $step,
                    'attempts' => 0,
                ]);
            }

            // Parse submission data
            $studentData = $validated['submission_data'];
            $decodedData = json_decode($studentData, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->with('error', 'Invalid submission data format.');
            }

            if (isset($decodedData['data']) && isset($decodedData['metadata'])) {
                // New format with metadata
                $studentDataArray = $decodedData['data'];
                $submissionMetadata = $decodedData['metadata'];
                $submission->submission_data = $studentData;
            } else {
                // Old format: just array of sheet data
                $studentDataArray = $decodedData;
                $submission->submission_data = $studentData;
            }
            
            // INCREMENT ATTEMPTS
            $submission->attempts = $submission->attempts + 1;
            $currentAttempt = $submission->attempts;

            // Get answer sheet
            $answerSheet = PerformanceTaskAnswerSheet::where([
                'performance_task_id' => $task->id,
                'step' => $step
            ])->first();

            $awardXp = false;
            $errorCount = 0;
            $totalCells = 0;

            if ($answerSheet && $answerSheet->correct_data) {
                // 🔥 FIX 1: Parse correct data properly
                $correctDataRaw = is_string($answerSheet->correct_data)
                    ? json_decode($answerSheet->correct_data, true)
                    : $answerSheet->correct_data;

                // 🔥 FIX 2: Extract data array if wrapped in {data, metadata} structure
                if (is_array($correctDataRaw) && isset($correctDataRaw['data'])) {
                    $correctData = $correctDataRaw['data'];
                    \Log::info("Step {$step} - Extracted correct data from wrapper", [
                        'has_metadata' => isset($correctDataRaw['metadata'])
                    ]);
                } else {
                    $correctData = $correctDataRaw;
                    \Log::info("Step {$step} - Using correct data as-is (no wrapper)");
                }

                // 🔍 Debug: Log sample data
                \Log::info("Step {$step} - Data sample comparison", [
                    'student_row_4' => $studentDataArray[4] ?? 'missing',
                    'correct_row_4' => $correctData[4] ?? 'missing',
                    'student_row_5_col_0' => $studentDataArray[5][0] ?? 'missing',
                    'correct_row_5_col_0' => $correctData[5][0] ?? 'missing',
                ]);

                // Count errors
                $errorDetails = $this->checkAnswersWithErrors($studentDataArray, $correctData, $step);
                $errorCount = $errorDetails['errorCount'];
                $totalCells = $errorDetails['totalCells'];

                // Calculate score per step
                $maxScorePerStep = $task->max_score / 10;
                $deductionPerStep = $task->deduction_per_error;
                
                $calculatedScore = max(0, $maxScorePerStep - ($errorCount * $deductionPerStep));
                
                // Determine thresholds
                $passingScore = $maxScorePerStep * 0.7;
                $isPerfect = ($errorCount === 0);
                $isPassing = ($calculatedScore >= $passingScore);

                // Update score
                $submission->score = round($calculatedScore, 2);

                if ($isPerfect) {
                    $awardXp = ($previousStatus !== 'correct');
                    $submission->status = 'correct';
                    $submission->remarks = $deadlineStatus['isLate']
                        ? "Perfect! {$calculatedScore}/{$maxScorePerStep} points (Late submission)"
                        : "Perfect! {$calculatedScore}/{$maxScorePerStep} points";
                        
                } elseif ($isPassing) {
                    $awardXp = ($previousStatus !== 'correct' && $previousStatus !== 'passed');
                    $submission->status = 'passed';
                    $submission->remarks = $deadlineStatus['isLate']
                        ? "Good job! {$calculatedScore}/{$maxScorePerStep} points. {$errorCount} error(s) found. (Late submission)"
                        : "Good job! {$calculatedScore}/{$maxScorePerStep} points. {$errorCount} error(s) found.";
                        
                } else {
                    $awardXp = false;
                    $submission->status = 'wrong';
                    
                    $remainingAttempts = $task->max_attempts - $currentAttempt;
                    $attemptInfo = $remainingAttempts > 0 
                        ? " You have {$remainingAttempts} attempt(s) remaining."
                        : " No attempts remaining.";
                    
                    $submission->remarks = $deadlineStatus['isLate']
                        ? "Score: {$calculatedScore}/{$maxScorePerStep}. {$errorCount} error(s) found.{$attemptInfo} (Late submission)"
                        : "Score: {$calculatedScore}/{$maxScorePerStep}. {$errorCount} error(s) found.{$attemptInfo}";
                }
                
            } else {
                $submission->status = 'in-progress';
                $submission->score = 0;
                $submission->remarks = 'Answer sheet not found for this step.';
            }

            // Save
            $saved = $submission->save();
            
            if (!$saved) {
                \Log::error("Failed to save submission for student {$user->student->id}, step {$step}");
                return back()->with('error', 'Failed to save your submission. Please try again.');
            }

            $submission->refresh();

            // Sync to pivot table
            $this->syncSubmissionToPivot($user->student->id, $task->id, $step, $submission);

            // Final step handling
            if ($step >= 10) {
                $this->storeFinalGrade($user->student->id, $task);
            }

            // Log performance
            $this->logPerformance($user->student, $task, $step, $submission, $deadlineStatus['isLate'], $errorCount);

            // Build success message
            $maxScorePerStep = $task->max_score / 10;
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

            // Award XP
            if ($awardXp && $submission->score > 0) {
                $this->awardXpForStep($user->student, $task, $step, $deadlineStatus['isLate'], $submission->score);
                $xpEarned = $this->calculateStepXp($step, $deadlineStatus['isLate'], $submission->score, $maxScorePerStep, $task->xp_reward ?? 100);
                $message .= " 🎉 You earned {$xpEarned} XP!";
            }

            // Check if final step
            if ($step >= 10) {
                if ($awardXp && $submission->score > 0) {
                    $this->awardCompletionBonus($user->student, $task);
                }
                $this->logTaskCompletion($user->student, $task);

                return redirect()->route('students.performance-tasks.index')
                    ->with('success', 'You have completed all 10 steps! Final score: ' . $submission->score . '/' . $maxScorePerStep);
            }

            // Proceed to next step
            return redirect()->route('students.performance-tasks.step', [
                'id' => $task->id,
                'step' => $step + 1,
            ])->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Performance Task Submission Error: ' . $e->getMessage(), [
                'student_id' => $user->student->id ?? null,
                'task_id' => $id,
                'step' => $step,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error saving your submission. Please try again.');
        }
    }

    /**
     * 📊 Log performance metrics for a step submission
     */
    private function logPerformance($student, $task, $step, $submission, $isLate, $errorCount = 0)
    {
        try {
            // Log score (out of max_score)
            \App\Models\PerformanceLog::create([
                'student_id' => $student->id,
                'subject_id' => $task->subject_id,
                'task_id' => $task->id,
                'performance_metric' => "step_{$step}_score",
                'value' => $submission->score,
                'recorded_at' => now(),
            ]);

            // Log error count
            \App\Models\PerformanceLog::create([
                'student_id' => $student->id,
                'subject_id' => $task->subject_id,
                'task_id' => $task->id,
                'performance_metric' => "step_{$step}_errors",
                'value' => $errorCount,
                'recorded_at' => now(),
            ]);

            // Log attempt number
            \App\Models\PerformanceLog::create([
                'student_id' => $student->id,
                'subject_id' => $task->subject_id,
                'task_id' => $task->id,
                'performance_metric' => "step_{$step}_attempts",
                'value' => $submission->attempts,
                'recorded_at' => now(),
            ]);

            // Log lateness
            \App\Models\PerformanceLog::create([
                'student_id' => $student->id,
                'subject_id' => $task->subject_id,
                'task_id' => $task->id,
                'performance_metric' => "step_{$step}_late_submission",
                'value' => $isLate ? 1 : 0,
                'recorded_at' => now(),
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
            return ['errorCount' => 999, 'totalCells' => 0];
        }

        foreach ($correctData as $rowIndex => $correctRow) {
            // 🔥 FIX: Skip header rows for Step 4 (rows 0-3 are headers)
            if ($step === 4 && $rowIndex < 4) {
                continue;
            }
            
            if (!isset($studentData[$rowIndex])) {
                // Missing entire row counts as errors for all cells in that row
                $errorCount += count(array_filter($correctRow, fn($val) => $val !== null && $val !== '' && $val !== 0));
                $totalCells += count($correctRow);
                continue;
            }

            $studentRow = $studentData[$rowIndex];
            foreach ($correctRow as $colIndex => $correctValue) {
                // Skip empty/null cells in answer key
                if ($correctValue === null || $correctValue === '' || $correctValue === 0) {
                    continue;
                }

                $totalCells++;
                $studentValue = $studentRow[$colIndex] ?? null;

                if (!$this->valuesMatch($studentValue, $correctValue)) {
                    $errorCount++;
                    
                    // 🔍 Debug: Log first few mismatches
                    if ($errorCount <= 5) {
                        \Log::info("Mismatch at Row {$rowIndex}, Col {$colIndex}", [
                            'student_value' => $studentValue,
                            'correct_value' => $correctValue,
                            'student_normalized' => $this->normalizeValue($studentValue),
                            'correct_normalized' => $this->normalizeValue($correctValue),
                        ]);
                    }
                }
            }
        }

        \Log::info("Grading completed for step {$step}", [
            'total_errors' => $errorCount,
            'total_cells_checked' => $totalCells,
        ]);

        return [
            'errorCount' => $errorCount,
            'totalCells' => $totalCells
        ];
    }

    /**
     * 📊 Log overall task completion metrics
     */
    private function logTaskCompletion($student, $task)
    {
        try {
            // Get all submissions for this task
            $submissions = PerformanceTaskSubmission::where('task_id', $task->id)
                ->where('student_id', $student->id)
                ->get();

            // Calculate overall accuracy
            $correctCount = $submissions->where('status', 'correct')->count();
            $totalSteps = $submissions->count();
            $overallAccuracy = $totalSteps > 0 ? ($correctCount / $totalSteps) * 100 : 0;

            // Calculate total attempts
            $totalAttempts = $submissions->sum('attempts');

            // Calculate average attempts per step
            $avgAttempts = $totalSteps > 0 ? $totalAttempts / $totalSteps : 0;

            // Log overall task accuracy
            \App\Models\PerformanceLog::create([
                'student_id' => $student->id,
                'subject_id' => $task->subject_id,
                'task_id' => $task->id,
                'performance_metric' => 'task_overall_accuracy',
                'value' => round($overallAccuracy, 2),
                'recorded_at' => now(),
            ]);

            // Log total attempts
            \App\Models\PerformanceLog::create([
                'student_id' => $student->id,
                'subject_id' => $task->subject_id,
                'task_id' => $task->id,
                'performance_metric' => 'task_total_attempts',
                'value' => $totalAttempts,
                'recorded_at' => now(),
            ]);

            // Log average attempts per step
            \App\Models\PerformanceLog::create([
                'student_id' => $student->id,
                'subject_id' => $task->subject_id,
                'task_id' => $task->id,
                'performance_metric' => 'task_avg_attempts_per_step',
                'value' => round($avgAttempts, 2),
                'recorded_at' => now(),
            ]);

            // Log completion status
            \App\Models\PerformanceLog::create([
                'student_id' => $student->id,
                'subject_id' => $task->subject_id,
                'task_id' => $task->id,
                'performance_metric' => 'task_completed',
                'value' => 1,
                'recorded_at' => now(),
            ]);

            \Log::info("Task completion logged for student {$student->id}, task {$task->id}");

        } catch (\Exception $e) {
            \Log::error('Task completion logging error: ' . $e->getMessage());
        }
    }

    /**
     * Award XP for completing a step
     */
    private function awardXpForStep($student, $task, $step, $isLate = false, $score = 0)
    {
        $xpAmount = $this->calculateStepXp($step, $isLate, $score, $task->max_score, $task->xp_reward ?? 100);

        if ($xpAmount > 0) {
            $student->xpTransactions()->create([
                'amount' => $xpAmount,
                'type' => 'earned',
                'source' => 'performance_task',
                'source_id' => $task->id,
                'description' => "Step {$step}: {$score}/{$task->max_score} points - {$task->title}",
                'processed_at' => now(),
            ]);

            \Log::info("Awarded {$xpAmount} XP to student {$student->id} for PT step {$step}");
        }
    }

    /**
     * Calculate XP amount based on step and timing
     */
    private function calculateStepXp($step, $isLate, $score, $maxScore, $taskXpReward = 100)
    {
        // Calculate XP per step as a fraction of total task XP (90% for steps, 10% for bonus)
        $xpPerStep = ($taskXpReward * 0.9) / 10; // 90% divided across 10 steps
        
        // Calculate percentage score for this step
        $percentage = $maxScore > 0 ? ($score / $maxScore) : 0;
        
        // XP proportional to score for this step
        $xp = round($xpPerStep * $percentage, 2);
        
        // Apply late penalty (50% reduction)
        if ($isLate) {
            $xp = round($xp * 0.5, 2);
        }
        
        return max(0, $xp);
    }

    /**
     * Award completion bonus for finishing all 10 steps
     */
    private function awardCompletionBonus($student, $task)
    {
        // Award 10% bonus for completing all steps
        $bonusXp = round(($task->xp_reward ?? 100) * 0.1, 2);
        
        if ($bonusXp > 0) {
            $student->xpTransactions()->create([
                'amount' => $bonusXp,
                'type' => 'earned',
                'source' => 'performance_task',
                'source_id' => $task->id,
                'description' => "Completion Bonus: {$task->title}",
                'processed_at' => now(),
            ]);
            
            \Log::info("Awarded {$bonusXp} XP completion bonus to student {$student->id}");
        }
    }

    /**
     * Get deadline status for a task
     * Returns: canSubmit (bool), isLate (bool), message (string), status (string)
     */
    private function getDeadlineStatus($task)
    {
        $now = now();
        
        // If no due dates are set, allow submissions
        if (!$task->due_date) {
            return [
                'canSubmit' => true,
                'isLate' => false,
                'status' => 'open',
                'message' => 'No deadline set for this task.',
            ];
        }

        // Check if past the late deadline (hard cutoff)
        if ($task->late_until && $now->greaterThan($task->late_until)) {
            return [
                'canSubmit' => false,
                'isLate' => true,
                'status' => 'closed',
                'message' => 'This task is no longer accepting submissions. The deadline has passed.',
            ];
        }

        // Check if between due_date and late_until (late but allowed)
        if ($task->late_until && $now->greaterThan($task->due_date) && $now->lessThanOrEqualTo($task->late_until)) {
            $hoursRemaining = $now->diffInHours($task->late_until);
            return [
                'canSubmit' => true,
                'isLate' => true,
                'status' => 'late',
                'message' => "⚠️ Late submission period. Final deadline: {$task->late_until->format('M d, Y h:i A')} ({$hoursRemaining} hours remaining)",
            ];
        }

        // Check if past due_date but no late_until is set (hard deadline)
        if (!$task->late_until && $now->greaterThan($task->due_date)) {
            return [
                'canSubmit' => false,
                'isLate' => true,
                'status' => 'closed',
                'message' => 'This task is past the due date and no longer accepting submissions.',
            ];
        }

        // Before due date (on time)
        $hoursRemaining = $now->diffInHours($task->due_date);
        return [
            'canSubmit' => true,
            'isLate' => false,
            'status' => 'on-time',
            'message' => "Due: {$task->due_date->format('M d, Y h:i A')} ({$hoursRemaining} hours remaining)",
        ];
    }

    /** 
     * Compare cell-by-cell answers
     */
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
        $norm1 = $this->normalizeValue($value1);
        $norm2 = $this->normalizeValue($value2);
        
        // Temporary debug logging
        if ($norm1 !== $norm2) {
            \Log::info("Value mismatch: '{$value1}' (normalized: '{$norm1}') vs '{$value2}' (normalized: '{$norm2}')");
        }
        
        return $norm1 === $norm2;
    }

    private function normalizeValue($value)
    {
        // Handle null, empty, or zero
        if ($value === null || $value === '' || $value === 0) {
            return '';
        }
        
        // Convert to string for processing
        $stringValue = (string)$value;
        
        // Try to parse as number (handles both numeric strings and actual numbers)
        // Remove peso signs, commas, and spaces first
        $cleaned = preg_replace('/[,₱\s]/', '', $stringValue);
        
        if (is_numeric($cleaned)) {
            // It's a number - normalize to 2 decimal places
            return number_format((float)$cleaned, 2, '.', '');
        }
        
        // It's text - normalize case and whitespace
        return strtolower(trim($stringValue));
    }

    public function submit()
    {
        return back()->with('success', 'Performance task submitted!');
    }

    public function show($id)
    {
        $user = auth()->user();
        
        // Verify the task belongs to the student
        $task = PerformanceTask::where('id', $id)
            ->whereHas('section.students', function ($query) use ($user) {
                $query->where('student_id', $user->student->id);
            })
            ->firstOrFail();

        // Redirect to progress page with the task ID
        return redirect()->route('students.performance-tasks.progress', ['taskId' => $id]);
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
            'task_id' => $performanceTask->id,
            'student_id' => $user->student->id,
            'step' => $step,
        ])->first();

        if (!$submission || $submission->attempts < $performanceTask->max_attempts) {
            return redirect()->route('students.performance-tasks.step', [
                'id' => $performanceTask->id,
                'step' => $step,
            ])->with('error', 'You must complete all attempts before viewing answers.');
        }

        $answerSheet = PerformanceTaskAnswerSheet::where([
            'performance_task_id' => $performanceTask->id,
            'step' => $step,
        ])->first();

        if (!$answerSheet) {
            return back()->with('error', 'Answer sheet not available for this step.');
        }

        // ✅ Use ONE view for all steps
        return view("students.performance-tasks.answers.view", [
            'performanceTask' => $performanceTask,
            'answerSheet' => $answerSheet,
            'submission' => $submission,
            'step' => $step,
        ]);
    }

    private function syncSubmissionToPivot($studentId, $taskId, $step, $submission)
    {
        try {
            // Check if pivot record exists
            $exists = DB::table('performance_task_student')
                ->where('performance_task_id', $taskId)
                ->where('student_id', $studentId)
                ->exists();

            if ($exists) {
                // Update existing record
                DB::table('performance_task_student')
                    ->where('performance_task_id', $taskId)
                    ->where('student_id', $studentId)
                    ->update([
                        'status' => 'in_progress',
                        'attempts' => DB::raw('attempts + 1'),
                        'submitted_at' => now(),
                        'updated_at' => now()
                    ]);
            } else {
                // Create new record
                DB::table('performance_task_student')->insert([
                    'performance_task_id' => $taskId,
                    'student_id' => $studentId,
                    'status' => 'in_progress',
                    'attempts' => 1,
                    'submitted_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            \Log::info("Synced submission to pivot table for student {$studentId}, task {$taskId}, step {$step}");
        } catch (\Exception $e) {
            \Log::error("Failed to sync to pivot table: " . $e->getMessage());
        }
    }

    /**
     * Store final grade when all steps are completed
     */
    private function storeFinalGrade($studentId, $task)
    {
        try {
            // Get all step submissions for this student and task
            $submissions = PerformanceTaskSubmission::where('task_id', $task->id)
                ->where('student_id', $studentId)
                ->get();

            if ($submissions->isEmpty()) {
                \Log::warning("No submissions found for student {$studentId} in task {$task->id}");
                return;
            }

            // --- SCORE CALCULATION ---
            $totalSteps = $submissions->count();
            $sumOfScores = $submissions->sum('score');

            // Each step is out of 100, but the total task score is 1000 (10 steps × 100)
            // So total score = sum of all step scores (capped by task max_score)
            $finalScore = min($sumOfScores, $task->max_score);

            // Compute percentage based on total task max score
            $percentage = $task->max_score > 0
                ? round(($finalScore / $task->max_score) * 100, 2)
                : 0;

            // --- XP CALCULATION ---
            // Sum of all XP already earned for this task (no double-award)
            $totalEarnedXp = DB::table('xp_transactions')
                ->where('student_id', $studentId)
                ->where('source', 'performance_task')
                ->where('source_id', $task->id)
                ->sum('amount');

            // Cap XP at the task’s defined XP reward
            $cappedXp = min($totalEarnedXp, $task->xp_reward ?? 1000);

            // --- SAVE FINAL GRADE ---
            DB::table('performance_task_student')
                ->where('performance_task_id', $task->id)
                ->where('student_id', $studentId)
                ->update([
                    'status' => 'graded',
                    'score' => round($finalScore, 2),
                    'xp_earned' => round($cappedXp, 2),
                    'graded_at' => now(),
                    'feedback' => sprintf(
                        "Task completed! Final score: %.2f / %d (%.2f%%) | XP Earned: %d",
                        $finalScore,
                        $task->max_score,
                        $percentage,
                        $cappedXp
                    ),
                    'updated_at' => now(),
                ]);

            // Log success
            \Log::info("Stored final grade for student {$studentId}, task {$task->id}: {$finalScore}/{$task->max_score} ({$percentage}%), XP: {$cappedXp}");

        } catch (\Exception $e) {
            \Log::error("Failed to store final grade for student {$studentId}, task {$task->id}: " . $e->getMessage());
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

        // Get all submissions for this student
        $submissions = PerformanceTaskSubmission::where('task_id', $task->id)
            ->where('student_id', $user->student->id)
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

        // Prepare submission details with feedback
        $submissionDetails = [];
        foreach ($submissions as $submission) {
            $submissionDetails[$submission->step] = [
                'step_title' => $stepTitles[$submission->step] ?? "Step {$submission->step}",
                'status' => $submission->status,
                'score' => $submission->score,
                'attempts' => $submission->attempts,
                'remarks' => $submission->remarks,
                'instructor_feedback' => $submission->instructor_feedback,
                'feedback_given_at' => $submission->feedback_given_at,
                'submitted_at' => $submission->created_at,
                'updated_at' => $submission->updated_at,
            ];
        }

        // Calculate statistics
        $statistics = [
            'total_score' => $submissions->sum('score'),
            'total_attempts' => $submissions->sum('attempts'),
            'completed_steps' => $submissions->where('status', 'correct')->count(),
            'wrong_steps' => $submissions->where('status', 'wrong')->count(),
            'in_progress_steps' => 10 - $submissions->whereIn('status', ['correct', 'wrong'])->count(),
            'feedback_count' => $submissions->whereNotNull('instructor_feedback')->count(),
        ];

        return view('students.performance-tasks.my-progress', compact(
            'task',
            'submissionDetails',
            'stepTitles',
            'statistics'
        ));
    }

    /**
     * View instructor feedback for a specific step
     */
    public function viewFeedback($id, $step)
    {
        $user = auth()->user();
        
        $task = PerformanceTask::where('id', $id)
            ->whereHas('section.students', function ($query) use ($user) {
                $query->where('student_id', $user->student->id);
            })
            ->firstOrFail();

        $submission = PerformanceTaskSubmission::where([
            'task_id' => $task->id,
            'student_id' => $user->student->id,
            'step' => $step,
        ])->firstOrFail();

        if (!$submission->instructor_feedback) {
            return redirect()->route('students.performance-tasks.my-progress', $task->id)
                ->with('info', 'No instructor feedback available for this step yet.');
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

        return view('students.performance-tasks.view-feedback', compact(
            'task',
            'submission',
            'step',
            'stepTitles'
        ));
    }

}