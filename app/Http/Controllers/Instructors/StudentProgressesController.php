<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\PerformanceTask;
use App\Models\XpTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentProgressesController extends Controller
{
    public function index(Request $request)
    {
        $instructor = auth()->user()->instructor;

        $students = Student::whereHas('sections', function($query) use ($instructor) {
            $query->whereHas('instructors', function($q) use ($instructor) {
                $q->where('instructors.id', $instructor->id);
            });
        })
        ->with(['user', 'course', 'sections', 'xpTransactions'])
        ->get();

        // Calculate XP & level from database for each student
        $students->map(function ($student) {
            $totalXp = $student->xpTransactions->sum('amount');
            $student->calculated_xp = $totalXp;
            $student->level = floor($totalXp / 1000) + 1;
            $student->xp_in_level = $totalXp % 1000;
            $student->progress_percentage = ($student->xp_in_level / 1000) * 100;
            return $student;
        });

        return view('instructors.progress.index', compact('students'));
    }

    public function show(Student $student)
    {
        $instructor = auth()->user()->instructor;

        // Load necessary relationships with proper eager loading
        $student->load([
            'user',
            'course',
            'sections',
            'xpTransactions' => function($query) {
                $query->orderBy(DB::raw('COALESCE(processed_at, created_at)'), 'desc');
            },
            'badges'
        ]);

        // Load performance tasks with pivot data for THIS instructor only
        $student->load(['performanceTasks' => function($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id)
                  ->withPivot(['status', 'score', 'submitted_at', 'graded_at'])
                  ->with('subject')
                  ->orderBy('due_date', 'desc');
        }]);

        // Debug logging
        Log::info('=== Student Progress Debug ===');
        Log::info('Student ID: ' . $student->id);
        Log::info('Student Name: ' . $student->user->name);
        Log::info('Performance Tasks Count: ' . $student->performanceTasks->count());
        Log::info('XP Transactions Count: ' . $student->xpTransactions->count());
        
        if ($student->performanceTasks->count() > 0) {
            Log::info('Sample Task Pivot:', $student->performanceTasks->first()->pivot->toArray());
        }

        // Calculate performance metrics
        $metrics = $this->calculateMetrics($student);

        // Get individual XP transactions for more detailed chart
        $xpTransactions = DB::table('xp_transactions')
            ->where('student_id', $student->id)
            ->whereNotNull(DB::raw('COALESCE(processed_at, created_at)'))
            ->orderBy(DB::raw('COALESCE(processed_at, created_at)'), 'asc')
            ->get(['id', 'amount', 'description', 'source', 'processed_at', 'created_at']);

        Log::info('Raw XP transactions:', $xpTransactions->toArray());

        // Calculate cumulative XP for each transaction
        $runningTotal = 0;
        $xpProgress = $xpTransactions->map(function($transaction, $index) use (&$runningTotal) {
            $runningTotal += (int) $transaction->amount;
            $timestamp = $transaction->processed_at ?? $transaction->created_at;
            
            return [
                'date' => Carbon::parse($timestamp)->format('M d H:i'),
                'label' => Carbon::parse($timestamp)->format('M d, g:ia'),
                'xp' => (int) $transaction->amount,
                'cumulative' => $runningTotal,
                'description' => $transaction->description ?? $transaction->source ?? 'XP Earned',
                'index' => $index + 1
            ];
        });

        Log::info('XP Progress Data Count: ' . $xpProgress->count());
        Log::info('Metrics calculated:', $metrics);

        return view('instructors.progress.show', compact(
            'student',
            'metrics',
            'xpProgress'
        ));
    }
    

    private function calculateMetrics(Student $student)
    {
        // All performance tasks for this student
        $performanceTasks = $student->performanceTasks;

        $totalTasks = $performanceTasks->count();
        
        Log::info('Calculating metrics for ' . $totalTasks . ' tasks');

        // Count completed tasks based on pivot status or score
        $completedTasks = $performanceTasks->filter(function ($task) {
            $status = strtolower($task->pivot->status ?? '');
            $hasScore = $task->pivot->score !== null;
            
            return in_array($status, ['submitted', 'graded', 'completed']) || $hasScore;
        })->count();

        Log::info('Completed tasks: ' . $completedTasks);

        // Average score for all graded tasks (tasks with scores)
        $tasksWithScores = $performanceTasks->filter(fn($task) => $task->pivot->score !== null);
        $averageScore = $tasksWithScores->count() > 0 
            ? $tasksWithScores->avg(fn($task) => $task->pivot->score) 
            : 0;

        Log::info('Tasks with scores: ' . $tasksWithScores->count() . ', Average: ' . $averageScore);

        // XP - Use direct query to ensure we get the latest data
        $totalXp = DB::table('xp_transactions')
            ->where('student_id', $student->id)
            ->sum('amount');

        Log::info('Total XP for student ' . $student->id . ': ' . $totalXp);

        // On-time vs late submissions
        $onTimeSubmissions = $performanceTasks->filter(function ($task) {
            if (!$task->pivot->submitted_at || !$task->due_date) {
                return false;
            }
            return Carbon::parse($task->pivot->submitted_at)->lte($task->due_date);
        })->count();

        $lateSubmissions = $performanceTasks->filter(function ($task) {
            if (!$task->pivot->submitted_at || !$task->due_date) {
                return false;
            }
            return Carbon::parse($task->pivot->submitted_at)->gt($task->due_date);
        })->count();

        // Badges earned (both manual and by XP threshold)
        $badgesEarnedManual = $student->badges->count();
        $badgesEarnedAuto = \App\Models\Badge::where('is_active', true)
            ->where('xp_threshold', '<=', $totalXp)
            ->count();
        $totalBadgesEarned = max($badgesEarnedManual, $badgesEarnedAuto);

        // Calculate completion rate
        $completionRate = $totalTasks > 0 
            ? round(($completedTasks / $totalTasks) * 100, 2) 
            : 0;

        $metrics = [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'completion_rate' => $completionRate,
            'average_score' => round($averageScore, 2),
            'on_time_submissions' => $onTimeSubmissions,
            'late_submissions' => $lateSubmissions,
            'total_xp' => $totalXp,
            'badges_earned' => $totalBadgesEarned,
            'class_rank' => $student->getLeaderboardRank(),
            'level' => floor($totalXp / 1000) + 1
        ];

        Log::info('Final metrics:', $metrics);

        return $metrics;
    }
}