<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $periodType = $request->get('period', 'overall'); // default overall
        $sectionId  = $request->get('section');
        $sort       = $request->get('sort', 'xp');
        $direction  = $request->get('direction', 'desc');

        $instructor = auth()->user()->instructor;

        // Sections instructor handles
        $instructorSections = $instructor->sections->pluck('id');

        $query = Student::with(['xpTransactions', 'sections', 'course'])
                    ->whereHas('sections', fn($q) => $q->whereIn('sections.id', $instructorSections));

        // Apply section filter if selected
        if ($sectionId && in_array($sectionId, $instructorSections->toArray())) {
            $query->whereHas('sections', fn($q) => $q->where('sections.id', $sectionId));
        }

        // Apply period filter
        if ($periodType !== 'overall') {
            $periodStart = now()->startOf($periodType);
            $periodEnd   = now()->endOf($periodType);

            $query->whereHas('xpTransactions', function ($q) use ($periodStart, $periodEnd) {
                $q->whereBetween('processed_at', [$periodStart, $periodEnd]);
            });
        }

        // Map students for leaderboard
        $students = $query->get()->map(function ($student) use ($periodType) {
            $xpTransactions = $student->xpTransactions;
            if ($periodType !== 'overall') {
                $xpTransactions = $xpTransactions->where('processed_at', '>=', now()->startOf($periodType));
            }

            return [
                'student_id'      => $student->id,
                'name'            => $student->user->name,
                'total_xp'        => $xpTransactions->sum('amount'),
                'tasks_completed' => $xpTransactions->where('type', 'earned')->count(),
            ];
        });

        // Sort by total_xp
        $ranked = $students->sortByDesc('total_xp')->values()->map(function ($data, $index) {
            $data['rank_position'] = $index + 1;
            return $data;
        });

        // Sections for filter dropdown
        $sections = $instructor->sections()->orderBy('name')->get();

        return view('instructor.leaderboards.index', compact(
            'ranked', 'periodType', 'sections', 'sectionId', 'sort', 'direction'
        ));
    }

    public function export(Request $request)
    {
        $periodType = $request->get('period', 'overall');
        $sectionId  = $request->get('section');

        $instructorSections = auth()->user()->instructor->sections->pluck('id');

        $query = Student::with(['xpTransactions', 'sections', 'course'])
                    ->whereHas('sections', fn($q) => $q->whereIn('sections.id', $instructorSections));

        if ($sectionId && in_array($sectionId, $instructorSections->toArray())) {
            $query->whereHas('sections', fn($q) => $q->where('sections.id', $sectionId));
        }

        if ($periodType !== 'overall') {
            $periodStart = now()->startOf($periodType);
            $periodEnd   = now()->endOf($periodType);

            $query->whereHas('xpTransactions', function ($q) use ($periodStart, $periodEnd) {
                $q->whereBetween('processed_at', [$periodStart, $periodEnd]);
            });
        }

        $ranked = $query->get()->map(function ($student) use ($periodType) {
            $xpTransactions = $student->xpTransactions;
            if ($periodType !== 'overall') {
                $xpTransactions = $xpTransactions->where('processed_at', '>=', now()->startOf($periodType));
            }

            return [
                'Student Name' => $student->user->name,
                'Course' => $student->course->course_name,
                'Section' => $student->sections->pluck('name')->join(', '),
                'Total XP' => $xpTransactions->sum('amount'),
                'Tasks Completed' => $xpTransactions->where('type', 'earned')->count(),
            ];
        })->sortByDesc('Total XP')->values();

        return response()->streamDownload(function() use ($ranked) {
            $output = fopen('php://output', 'w');
            fputcsv($output, array_keys($ranked->first()));
            foreach ($ranked as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, 'leaderboard-' . now()->format('Y-m-d') . '.csv');
    }
}
