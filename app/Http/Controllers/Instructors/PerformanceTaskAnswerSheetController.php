<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\PerformanceTask;
use App\Models\PerformanceTaskAnswerSheet;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PerformanceTaskAnswerSheetController extends Controller
{
    /**
     * Show all performance tasks with answer sheet counts
     */
    public function index()
    {
        try {
            $instructor = auth()->user()->instructor;
            
            if (!$instructor) {
                throw new Exception('Not authorized as an instructor');
            }
            
            $tasks = PerformanceTask::where('instructor_id', $instructor->id)
                ->withCount('answerSheets')
                ->latest()
                ->get();

            return view('instructors.performance-tasks.answer-sheets.index', compact('tasks'));
        } catch (Exception $e) {
            Log::error('Error in PerformanceTaskAnswerSheetController@index: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while loading tasks. Please try again.');
        }
    }

    /**
     * Show the 10 steps for a selected performance task
     */
    public function show(PerformanceTask $task)
    {
        try {
            $instructor = auth()->user()->instructor;
            if ($task->instructor_id !== $instructor->id) {
                throw new Exception('Unauthorized access to task');
            }

            $answerSheets = PerformanceTaskAnswerSheet::where('performance_task_id', $task->id)
                ->orderBy('step')
                ->get()
                ->keyBy('step');

            // ✅ Add step titles
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

            return view('instructors.performance-tasks.answer-sheets.show', compact('task', 'answerSheets', 'stepTitles'));

        } catch (Exception $e) {
            Log::error('Error in PerformanceTaskAnswerSheetController@show: ' . $e->getMessage());
            return back()->with('error', 'Unable to load answer sheets. Please try again.');
        }
    }


    /**
     * Edit or Create specific step answer sheet
     */
    public function edit(PerformanceTask $task, $step)
    {
        // Verify ownership
        $instructor = auth()->user()->instructor;
        if ($task->instructor_id !== $instructor->id) {
            abort(403, 'Unauthorized access');
        }

        // Validate step number
        if ($step < 1 || $step > 10) {
            return redirect()->route('instructors.performance-tasks.answer-sheets.show', $task)
                ->with('error', 'Invalid step number. Must be between 1 and 10.');
        }

        $sheet = PerformanceTaskAnswerSheet::firstOrNew([
            'performance_task_id' => $task->id,
            'step' => $step,
        ]);

        return view("instructors.performance-tasks.answer-sheets.step-$step", compact('task', 'sheet'));
    }

    /**
     * Update or create answer sheet for a specific step
     */
    public function update(Request $request, PerformanceTask $task, $step)
    {
        DB::beginTransaction();
        try {
            // Verify ownership
            $instructor = auth()->user()->instructor;
            if ($task->instructor_id !== $instructor->id) {
                throw new Exception('Unauthorized access to task');
            }

            // Validate step number
            if (!in_array($step, range(1, 10))) {
                throw new Exception('Invalid step number. Must be between 1 and 10.');
            }

            // Validate the correct_data input
            $request->validate([
                'correct_data' => ['required', 'string', 'json'],
            ]);

            // Validate JSON structure based on step
            $data = json_decode($request->input('correct_data'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON data provided');
            }

            // Create or update the answer sheet
            $answerSheet = PerformanceTaskAnswerSheet::updateOrCreate(
                [
                    'performance_task_id' => $task->id,
                    'step' => $step
                ],
                [
                    'correct_data' => $request->input('correct_data')
                ]
            );

            DB::commit();

            $nextStep = $step + 1;
            
            if ($nextStep > 10) {
                return redirect()
                    ->route('instructors.performance-tasks.answer-sheets.show', $task)
                    ->with('success', "All answer sheets have been saved successfully!");
            }

            return redirect()
                ->route('instructors.performance-tasks.answer-sheets.edit', ['task' => $task, 'step' => $nextStep])
                ->with('success', "Answer sheet for Step {$step} saved successfully! Proceed to Step {$nextStep}.");

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in PerformanceTaskAnswerSheetController@update: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Error saving answer sheet: ' . $this->getUserFriendlyError($e->getMessage()));
        }
    }

    /**
     * Delete an answer sheet for a specific step
     */
    public function destroy(PerformanceTask $task, $step)
    {
        DB::beginTransaction();
        try {
            $instructor = auth()->user()->instructor;
            if ($task->instructor_id !== $instructor->id) {
                throw new Exception('Unauthorized access to task');
            }

            $answerSheet = PerformanceTaskAnswerSheet::where('performance_task_id', $task->id)
                ->where('step', $step)
                ->firstOrFail();

            $answerSheet->delete();
            DB::commit();

            return back()->with('success', "Answer sheet for Step {$step} deleted successfully.");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in PerformanceTaskAnswerSheetController@destroy: ' . $e->getMessage());
            
            return back()->with('error', 'Unable to delete answer sheet. Please try again.');
        }
    }

    /**
     * Convert technical error messages to user-friendly messages
     */
    private function getUserFriendlyError($message)
    {
        $friendlyMessages = [
            'Unauthorized access' => 'You do not have permission to perform this action.',
            'Invalid JSON data' => 'The answer sheet data is not in the correct format.',
            'Invalid step number' => 'Please select a valid step between 1 and 10.',
            'SQLSTATE' => 'A database error occurred.',
        ];

        foreach ($friendlyMessages as $technical => $friendly) {
            if (str_contains($message, $technical)) {
                return $friendly;
            }
        }

        return 'An unexpected error occurred. Please try again.';
    }
}