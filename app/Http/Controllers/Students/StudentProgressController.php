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
        $student->load('xpTransactions', 'badges');

        // total xp (xpTransactions table)
        $totalXp = (int) $student->xpTransactions()->sum('amount');

        // ✅ Count completed performance tasks from pivot table
        $tasksCompleted = \DB::table('performance_task_student')
            ->where('student_id', $student->id)
            ->where('status', 'graded')
            ->count();

        // ✅ FIX: Calculate total score from graded performance tasks
        $totalScore = \DB::table('performance_task_student')
            ->where('student_id', $student->id)
            ->where('status', 'graded')
            ->sum('score');

        // xp breakdown by source (make plain array for easy blade use)
        $xpBreakdown = $student->xpTransactions()
            ->selectRaw('source, SUM(amount) as total')
            ->groupBy('source')
            ->pluck('total', 'source')
            ->toArray();

        return view('students.progress.index', [
            'student' => $student,
            'totalXp' => $totalXp,
            'totalScore' => $totalScore, // ✅ Pass this to the view
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
