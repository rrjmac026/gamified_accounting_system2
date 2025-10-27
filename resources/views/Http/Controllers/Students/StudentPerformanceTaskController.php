<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\PerformanceTask;
use App\Models\PerformanceTaskSubmission;
use App\Models\PerformanceTaskAnswerSheet;
use Illuminate\Http\Request;

class StudentPerformanceTaskController extends Controller
{
    /**
     * Show list of performance tasks assigned to the logged-in student
     */
    public function index()
    {
        $user = auth()->user();

        // Fetch all tasks assigned to the student's section with relationships
        $performanceTasks = PerformanceTask::whereHas('section.students', function ($query) use ($user) {
            $query->where('student_id', $user->student->id);
        })
        ->with([
            'section',
            'subject',
            'instructor'
        ])
        ->latest()
        ->get();

        // Calculate progress for each task
        $performanceTasks->each(function ($task) use ($user) {
            // Count unique completed steps (regardless of correct/wrong status)
            $completedSteps = PerformanceTaskSubmission::where('task_id', $task->id)
                ->where('student_id', $user->student->id)
                ->distinct()
                ->pluck('step')
                ->unique()
                ->count();
            
            $task->progress = $completedSteps;
            $task->totalSteps = 10;
            $task->progressPercentage = ($completedSteps > 0) ? round(($completedSteps / 10) * 100, 2) : 0;
            
            // Add deadline status
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

            // 🔥 FIX: Check attempts BEFORE incrementing
            if ($submission && $submission->attempts >= $task->max_attempts) {
                return back()->with('error', "You have reached the maximum of {$task->max_attempts} attempts for this step.");
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
            $studentDataArray = json_decode($studentData, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->with('error', 'Invalid submission data format.');
            }

            // Update submission data and increment attempts
            $submission->submission_data = $studentData;
            $submission->attempts = $submission->attempts + 1;

            // Get answer sheet
            $answerSheet = PerformanceTaskAnswerSheet::where([
                'performance_task_id' => $task->id,
                'step' => $step
            ])->first();

            $awardXp = false;
            $errorCount = 0;
            $totalCells = 0;

            if ($answerSheet && $answerSheet->correct_data) {
                $correctData = is_string($answerSheet->correct_data)
                    ? json_decode($answerSheet->correct_data, true)
                    : $answerSheet->correct_data;

                // Count errors
                $errorDetails = $this->checkAnswersWithErrors($studentDataArray, $correctData);
                $errorCount = $errorDetails['errorCount'];
                $totalCells = $errorDetails['totalCells'];

                // 🔥 FIX: Calculate score fresh every time
                $calculatedScore = max(0, $task->max_score - ($errorCount * $task->deduction_per_error));
                
                // Determine thresholds
                $passingScore = $task->max_score * 0.7; // 70% threshold
                $isPerfect = ($errorCount === 0);
                $isPassing = ($calculatedScore >= $passingScore);

                // 🔥 IMPORTANT: Always update score regardless of status
                $submission->score = $calculatedScore;

                if ($isPerfect) {
                    // Award XP only on FIRST perfect score
                    $awardXp = ($previousStatus !== 'correct');
                    
                    $submission->status = 'correct';
                    $submission->remarks = $deadlineStatus['isLate']
                        ? "Perfect! {$calculatedScore}/{$task->max_score} points (Late submission)"
                        : "Perfect! {$calculatedScore}/{$task->max_score} points";
                        
                } elseif ($isPassing) {
                    // Award XP only on FIRST passing attempt (if never got correct before)
                    $awardXp = ($previousStatus !== 'correct' && $previousStatus !== 'passed');
                    
                    $submission->status = 'passed';
                    $submission->remarks = $deadlineStatus['isLate']
                        ? "Good job! {$calculatedScore}/{$task->max_score} points. {$errorCount} error(s) found. (Late submission)"
                        : "Good job! {$calculatedScore}/{$task->max_score} points. {$errorCount} error(s) found.";
                        
                } else {
                    // Failed - no XP awarded
                    $awardXp = false;
                    
                    $submission->status = 'wrong';
                    $submission->remarks = $deadlineStatus['isLate']
                        ? "Score: {$calculatedScore}/{$task->max_score}. {$errorCount} error(s) found. Please review and retry. (Late submission)"
                        : "Score: {$calculatedScore}/{$task->max_score}. {$errorCount} error(s) found. Please review and retry.";
                }
                
            } else {
                $submission->status = 'in-progress';
                $submission->score = 0;
                $submission->remarks = 'Answer sheet not found for this step.';
            }

            // 🔥 FIX: Save immediately and verify
            $saved = $submission->save();
            
            if (!$saved) {
                \Log::error("Failed to save submission for student {$user->student->id}, step {$step}");
                return back()->with('error', 'Failed to save your submission. Please try again.');
            }

            // Refresh to get updated data
            $submission->refresh();

            // Log performance metrics
            $this->logPerformance($user->student, $task, $step, $submission, $deadlineStatus['isLate'], $errorCount);

            // Build success message
            $message = "Step {$step} saved! (Attempt {$submission->attempts}/{$task->max_attempts}) - Score: {$submission->score}/{$task->max_score}";
            
            // Show score change if it decreased
            if ($previousScore > 0 && $submission->score < $previousScore) {
                $difference = $previousScore - $submission->score;
                $message .= " (⬇️ -{$difference} from previous attempt)";
            }
            
            if ($errorCount > 0) {
                $message .= " | {$errorCount} error(s) detected";
            }

            if ($deadlineStatus['isLate']) {
                $message .= " ⚠️ Late submission";
            }

            // Award XP based on score (proportional to performance)
            if ($awardXp && $submission->score > 0) {
                $this->awardXpForStep($user->student, $task, $step, $deadlineStatus['isLate'], $submission->score);
                $xpEarned = $this->calculateStepXp($step, $deadlineStatus['isLate'], $submission->score, $task->max_score);
                $message .= " 🎉 You earned {$xpEarned} XP!";
            }

            // Check if this is the final step
            if ($step >= 10) {
                if ($awardXp && $submission->score > 0) {
                    $this->awardCompletionBonus($user->student, $task);
                }
                $this->logTaskCompletion($user->student, $task);

                return redirect()->route('students.performance-tasks.index')
                    ->with('success', 'You have completed all 10 steps! Final score: ' . $submission->score . '/' . $task->max_score);
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

    private function checkAnswersWithErrors($studentData, $correctData)
    {
        $errorCount = 0;
        $totalCells = 0;

        if (!is_array($studentData) || !is_array($correctData)) {
            return ['errorCount' => 999, 'totalCells' => 0]; // Invalid data
        }

        foreach ($correctData as $rowIndex => $correctRow) {
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
                }
            }
        }

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
        $xpAmount = $this->calculateStepXp($step, $isLate, $score, $task->max_score);

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

    /**
     * Calculate XP amount based on step and timing
     */
    private function calculateStepXp($step, $isLate = false, $score = 0, $maxScore = 100)
    {
        $baseXp = 10; // Max XP per step
        
        // Calculate percentage score
        $percentage = $maxScore > 0 ? ($score / $maxScore) : 0;
        
        // XP proportional to score
        $xp = (int) ($baseXp * $percentage);
        
        // Apply late penalty (50% reduction)
        if ($isLate) {
            $xp = (int) ($xp * 0.5);
        }
        
        return max(0, $xp); // Ensure non-negative
    }

    /**
     * Award completion bonus for finishing all 10 steps
     */
    private function awardCompletionBonus($student, $task)
    {
        $bonusXp = 50; // Bonus for completing entire performance task

        $student->xpTransactions()->create([
            'amount' => $bonusXp,
            'source' => 'performance_task',
            'description' => "Completion Bonus: {$task->title}",
            'reference_id' => $task->id,
            'reference_type' => 'App\Models\PerformanceTask',
        ]);

        \Log::info("Awarded {$bonusXp} XP completion bonus to student {$student->id}");
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
        return $this->normalizeValue($value1) === $this->normalizeValue($value2);
    }

    private function normalizeValue($value)
    {
        if ($value === null || $value === '' || $value === 0) return '';
        if (is_numeric($value)) return number_format((float)$value, 2, '.', '');
        if (is_string($value)) return strtolower(trim($value));
        return (string)$value;
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
}