<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Badge;

class StudentProgressController extends Controller
{
    public function progress()
    {
        $student = auth()->user()->student;

        // eager load related models we'll use
        $student->load('xpTransactions', 'tasks.submissions', 'badges');

        // total xp (xpTransactions table)
        $totalXp = (int) $student->xpTransactions()->sum('amount');

        // tasks completed (same logic as your dashboard)
        $tasksCompleted = $student->tasks->filter(function ($task) {
            $submission = $task->submissions->first();
            return $task->pivot->status === 'submitted' ||
                   ($submission && $submission->score !== null);
        })->count();

        // xp breakdown by source (make plain array for easy blade use)
        $xpBreakdown = $student->xpTransactions()
            ->selectRaw('source, SUM(amount) as total')
            ->groupBy('source')
            ->pluck('total', 'source')
            ->toArray(); // <-- convert to array so blade can safely use $xpBreakdown['manual'] ?? 0

        return view('students.progress.index', [
            'student' => $student,
            'totalXp' => $totalXp,
            'tasksCompleted' => $tasksCompleted,
            'xpBreakdown' => $xpBreakdown,
            'nextLevelXp' => 1000,
            'leaderboardRank' => $student->getLeaderboardRank(),
        ]);
    }

    public function achievements()
    {
        $student = auth()->user()->student;

        // eager load xp & badges
        $student->load('xpTransactions', 'badges');

        // total xp
        $totalXp = (int) $student->xpTransactions()->sum('amount');

        // xp breakdown for display
        $xpBreakdown = $student->xpTransactions()
            ->selectRaw('source, SUM(amount) as total')
            ->groupBy('source')
            ->pluck('total', 'source')
            ->toArray();

        // Build badges with progress metadata
        $badges = Badge::where('is_active', true)->get()->map(function ($badge) use ($student, $totalXp) {
        $earnedBadge = $student->badges()->where('badges.id', $badge->id)->first();
        
        // Either already earned OR totalXp >= badge threshold
        $badge->earned = (bool) $earnedBadge || $totalXp >= $badge->xp_threshold;

        $badge->earned_at = $earnedBadge ? $earnedBadge->pivot->earned_at : null;

        if (!$badge->earned) {
            $badge->progress = $totalXp;
            $badge->remaining = max(0, $badge->xp_threshold - $totalXp);
        }

        return $badge;
    });


        return view('students.achievements.index', [
            'student' => $student,
            'badges' => $badges,
            'totalXp' => $totalXp,
            'xpBreakdown' => $xpBreakdown,
        ]);
    }
}
