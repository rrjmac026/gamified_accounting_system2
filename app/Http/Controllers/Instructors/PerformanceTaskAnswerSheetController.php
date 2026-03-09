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
    private array $allStepTitles = [
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

    /**
     * Show all performance tasks with answer sheet counts.
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
     * Show only ENABLED steps for this task, with their answer sheet status.
     */
    public function show(PerformanceTask $task)
    {
        try {
            $instructor = auth()->user()->instructor;
            if ($task->instructor_id !== $instructor->id) {
                throw new Exception('Unauthorized access to task');
            }

            // ✅ Check exercises instead of answer sheets
            $answerSheets = \App\Models\PerformanceTaskExercise::where('performance_task_id', $task->id)
                ->where('order', 1)
                ->orderBy('step')
                ->get()
                ->keyBy('step');

            $stepTitles = collect($this->allStepTitles)
                ->only($task->enabled_steps_list)
                ->toArray();

            return view('instructors.performance-tasks.answer-sheets.show', compact(
                'task', 'answerSheets', 'stepTitles'
            ));

        } catch (Exception $e) {
            Log::error('Error in PerformanceTaskAnswerSheetController@show: ' . $e->getMessage());
            return back()->with('error', 'Unable to load answer sheets. Please try again.');
        }
    }

    /**
     * Edit or create a specific step's answer sheet — only if step is enabled.
     */
    public function edit(PerformanceTask $task, $step)
    {
        $instructor = auth()->user()->instructor;
        if ($task->instructor_id !== $instructor->id) {
            abort(403, 'Unauthorized access');
        }

        if ($step < 1 || $step > 10) {
            return redirect()->route('instructors.performance-tasks.answer-sheets.show', $task)
                ->with('error', 'Invalid step number. Must be between 1 and 10.');
        }

        if (!$task->isStepEnabled((int) $step)) {
            return redirect()->route('instructors.performance-tasks.answer-sheets.show', $task)
                ->with('error', "Step {$step} is not enabled for this task.");
        }

        // ✅ Load from PerformanceTaskExercise instead of PerformanceTaskAnswerSheet
        $exercise = \App\Models\PerformanceTaskExercise::where([
            'performance_task_id' => $task->id,
            'step'                => $step,
            'order'               => 1,
        ])->first();

        // ✅ Map to $sheet so all 10 step blades work without any blade changes
        // The blades all reference $sheet->correct_data — we just point it at the exercise
        $sheet = $exercise;

        return view("instructors.performance-tasks.answer-sheets.step-$step", compact('task', 'sheet'));
    }

    /**
     * Save the answer sheet for a specific step.
     * After saving, redirects to the next ENABLED step (not just step+1).
     */
    public function update(Request $request, PerformanceTask $task, $step)
    {
        DB::beginTransaction();
        try {
            $instructor = auth()->user()->instructor;
            if ($task->instructor_id !== $instructor->id) {
                throw new Exception('Unauthorized access to task');
            }

            if (!in_array($step, range(1, 10))) {
                throw new Exception('Invalid step number. Must be between 1 and 10.');
            }

            if (!$task->isStepEnabled((int) $step)) {
                throw new Exception("Step {$step} is not enabled for this task.");
            }

            $request->validate([
                'correct_data' => ['required', 'string', 'json'],
            ]);

            $data = json_decode($request->input('correct_data'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON data provided');
            }

            // ✅ Save into PerformanceTaskExercise instead of PerformanceTaskAnswerSheet
            // One exercise per step — updateOrCreate so re-saving just overwrites
            \App\Models\PerformanceTaskExercise::updateOrCreate(
                [
                    'performance_task_id' => $task->id,
                    'step'                => $step,
                    'order'               => 1,
                ],
                [
                    'title'        => $this->allStepTitles[$step],
                    'correct_data' => $request->input('correct_data'),
                ]
            );

            DB::commit();

            // ✅ Find the next ENABLED step after the current one
            $nextStep = collect($task->enabled_steps_list)
                ->first(fn($s) => $s > $step);

            if (!$nextStep) {
                return redirect()
                    ->route('instructors.performance-tasks.answer-sheets.show', $task)
                    ->with('success', 'All answer sheets have been saved successfully!');
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
     * Delete an answer sheet for a specific step.
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

    private function getUserFriendlyError($message): string
    {
        $friendlyMessages = [
            'Unauthorized access'  => 'You do not have permission to perform this action.',
            'Invalid JSON data'    => 'The answer sheet data is not in the correct format.',
            'Invalid step number'  => 'Please select a valid step between 1 and 10.',
            'not enabled'          => 'That step is not enabled for this task.',
            'SQLSTATE'             => 'A database error occurred.',
        ];

        foreach ($friendlyMessages as $technical => $friendly) {
            if (str_contains($message, $technical)) {
                return $friendly;
            }
        }

        return 'An unexpected error occurred. Please try again.';
    }
}