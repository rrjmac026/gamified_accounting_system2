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
     * Show all steps for a task, with exercise counts per step.
     * Instructor picks which step to add exercises to — no forced order.
     */
    public function show(PerformanceTask $task)
    {
        $this->authorizeTask($task);

        // Group all exercises by step
        $exercisesByStep = PerformanceTaskExercise::where('performance_task_id', $task->id)
            ->orderBy('step')
            ->orderBy('order')
            ->get()
            ->groupBy('step');

        return view('instructors.performance-tasks.exercises.show', compact(
            'task',
            'exercisesByStep',
        ))->with('stepTitles', $this->stepTitles);
    }

    /**
     * Show form to add a new exercise to a specific step.
     */
    public function create(PerformanceTask $task, int $step)
    {
        $this->authorizeTask($task);
        $this->validateStep($step);

        $stepTitle = $this->stepTitles[$step];

        // How many exercises already exist for this step (for auto-numbering)
        $existingCount = PerformanceTaskExercise::where([
            'performance_task_id' => $task->id,
            'step'                => $step,
        ])->count();

        $nextNumber = $existingCount + 1;

        return view("instructors.performance-tasks.exercises.steps.step-{$step}", compact(
            'task', 'step', 'stepTitle', 'nextNumber'
        ));
    }

    /**
     * Store a new exercise for a specific step.
     */
    public function store(Request $request, PerformanceTask $task, int $step)
    {
        $this->authorizeTask($task);
        $this->validateStep($step);

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
     */
    public function edit(PerformanceTask $task, PerformanceTaskExercise $exercise)
    {
        $this->authorizeTask($task);

        $step      = $exercise->step;
        $stepTitle = $this->stepTitles[$step];

        return view("instructors.performance-tasks.exercises.steps.step-{$step}", compact(
            'task', 'exercise', 'step', 'stepTitle'
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
                ->with('success', "Exercise updated successfully!");

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
            $remaining = PerformanceTaskExercise::where([
                'performance_task_id' => $task->id,
                'step'                => $step,
            ])->orderBy('order')->get();

            foreach ($remaining as $i => $ex) {
                $ex->update(['order' => $i + 1]);
            }

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

    private function validateStep(int $step): void
    {
        if ($step < 1 || $step > 10) {
            abort(422, 'Invalid step number. Must be between 1 and 10.');
        }
    }
}