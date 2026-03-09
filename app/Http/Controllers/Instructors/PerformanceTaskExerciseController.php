<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\PerformanceTask;
use App\Models\PerformanceTaskExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PerformanceTaskExerciseController extends Controller
{
    private array $stepTitles = [
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
     * Show only ENABLED steps for this task, with exercise counts per step.
     */
    public function show(PerformanceTask $task)
    {
        $this->authorizeTask($task);

        $exercisesByStep = PerformanceTaskExercise::where('performance_task_id', $task->id)
            ->orderBy('step')
            ->orderBy('order')
            ->get()
            ->groupBy('step');

        // ✅ Only pass the enabled steps to the view — disabled steps are hidden
        $stepTitles = collect($this->stepTitles)
            ->only($task->enabled_steps_list)
            ->toArray();

        return view('instructors.performance-tasks.exercises.show', compact(
            'task',
            'exercisesByStep',
            'stepTitles',
        ));
    }

    /**
     * Show form to add a new exercise — only if the step is enabled.
     */
    public function create(PerformanceTask $task, int $step)
    {
        $this->authorizeTask($task);
        $this->validateStep($task, $step); // ✅ now checks enabled_steps too

        $stepTitle = $this->stepTitles[$step];

        $nextNumber = PerformanceTaskExercise::where([
            'performance_task_id' => $task->id,
            'step'                => $step,
        ])->count() + 1;

        return view('instructors.performance-tasks.exercises.form', compact(
            'task', 'step', 'stepTitle', 'nextNumber'
        ));
    }

    /**
     * Store a new exercise — only if the step is enabled.
     */
    public function store(Request $request, PerformanceTask $task, int $step)
    {
        $this->authorizeTask($task);
        $this->validateStep($task, $step); // ✅ now checks enabled_steps too

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string|max:1000',
            'correct_data' => ['required', 'string', 'json'],
        ]);

        DB::beginTransaction();
        try {
            $maxOrder = PerformanceTaskExercise::where([
                'performance_task_id' => $task->id,
                'step'                => $step,
            ])->max('order') ?? 0;

            PerformanceTaskExercise::create([
                'performance_task_id' => $task->id,
                'step'                => $step,
                'title'               => $validated['title'],
                'description'         => $validated['description'] ?? null,
                'correct_data'        => $validated['correct_data'],
                'order'               => $maxOrder + 1,
            ]);

            DB::commit();

            return redirect()
                ->route('instructors.performance-tasks.exercises.show', $task)
                ->with('success', "Exercise added to Step {$step}: {$this->stepTitles[$step]}!");

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error storing exercise: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to save exercise. Please try again.');
        }
    }

    /**
     * Show form to edit a specific exercise.
     * No enabled_steps check needed here — editing existing exercises on
     * disabled steps is allowed (instructor may want to fix data before re-enabling).
     */
    public function edit(PerformanceTask $task, PerformanceTaskExercise $exercise)
    {
        $this->authorizeTask($task);

        $step       = $exercise->step;
        $stepTitle  = $this->stepTitles[$step];
        $nextNumber = $exercise->order;

        return view('instructors.performance-tasks.exercises.form', compact(
            'task', 'exercise', 'step', 'stepTitle', 'nextNumber'
        ));
    }

    /**
     * Update a specific exercise.
     */
    public function update(Request $request, PerformanceTask $task, PerformanceTaskExercise $exercise)
    {
        $this->authorizeTask($task);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string|max:1000',
            'correct_data' => ['required', 'string', 'json'],
        ]);

        DB::beginTransaction();
        try {
            $exercise->update([
                'title'        => $validated['title'],
                'description'  => $validated['description'] ?? null,
                'correct_data' => $validated['correct_data'],
            ]);

            DB::commit();

            return redirect()
                ->route('instructors.performance-tasks.exercises.show', $task)
                ->with('success', 'Exercise updated successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating exercise: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update exercise. Please try again.');
        }
    }

    /**
     * Delete a specific exercise.
     */
    public function destroy(PerformanceTask $task, PerformanceTaskExercise $exercise)
    {
        $this->authorizeTask($task);

        DB::beginTransaction();
        try {
            $step = $exercise->step;
            $exercise->delete();

            // Re-number remaining exercises in the same step
            PerformanceTaskExercise::where([
                'performance_task_id' => $task->id,
                'step'                => $step,
            ])->orderBy('order')->get()->each(function ($ex, $i) {
                $ex->update(['order' => $i + 1]);
            });

            DB::commit();

            return back()->with('success', 'Exercise deleted successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting exercise: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete exercise.');
        }
    }

    /**
     * Reorder exercises within a step (drag-and-drop support).
     */
    public function reorder(Request $request, PerformanceTask $task, int $step)
    {
        $this->authorizeTask($task);

        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer|exists:performance_task_exercises,id',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->order as $position => $exerciseId) {
                PerformanceTaskExercise::where('id', $exerciseId)
                    ->where('performance_task_id', $task->id)
                    ->where('step', $step)
                    ->update(['order' => $position + 1]);
            }

            DB::commit();
            return response()->json(['success' => true]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function authorizeTask(PerformanceTask $task): void
    {
        if ($task->instructor_id !== auth()->user()->instructor->id) {
            abort(403, 'Unauthorized access to task.');
        }
    }

    /**
     * Validates step is in range 1–10 AND is enabled for this task.
     */
    private function validateStep(PerformanceTask $task, int $step): void
    {
        if ($step < 1 || $step > 10) {
            abort(422, 'Invalid step number. Must be between 1 and 10.');
        }

        if (!$task->isStepEnabled($step)) {
            abort(403, "Step {$step} is not enabled for this task.");
        }
    }
}