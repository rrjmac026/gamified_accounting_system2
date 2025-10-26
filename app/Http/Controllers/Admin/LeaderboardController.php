<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leaderboard;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\PDF\LeaderboardPDF;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period' => 'nullable|in:weekly,monthly,semester,overall',
            'section' => 'nullable|exists:sections,id',
            'course' => 'nullable|exists:courses,id',
            'sort' => 'nullable|in:xp,tasks,name',
            'direction' => 'nullable|in:asc,desc'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $periodType = $request->get('period', 'overall'); // default overall
        $sectionId = $request->get('section');
        $courseId = $request->get('course');
        $sort = $request->get('sort', 'xp');
        $direction = $request->get('direction', 'desc');

        // Query all students (do NOT filter out hidden ones)
        $query = Student::with(['xpTransactions', 'sections', 'course']);

        // Apply filters
        if ($sectionId) {
            $query->whereHas('sections', fn($q) => $q->where('sections.id', $sectionId));
        }

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($periodType !== 'overall') {
            $periodStart = now()->startOf($periodType);
            $periodEnd   = now()->endOf($periodType);

            $query->whereHas('xpTransactions', function ($q) use ($periodStart, $periodEnd) {
                $q->whereBetween('processed_at', [$periodStart, $periodEnd]);
            });
        }

        $students = $query->get()->map(function ($student) use ($periodType) {
            return [
                'student_id'      => $student->id,
                // Mask the name if the student chose to hide
                'name'            => $student->hide_from_leaderboard ? 'Hidden' : $student->user->name,
                'total_xp'        => $student->xpTransactions
                                        ->when($periodType !== 'overall', function ($txs) use ($periodType) {
                                            return $txs->where('processed_at', '>=', now()->startOf($periodType));
                                        })
                                        ->sum('amount'),
                'tasks_completed' => $student->xpTransactions
                                        ->where('type', 'earned')
                                        ->count(),
            ];
        });

        // Sort by XP and rank
        $ranked = $students->sortByDesc('total_xp')->values()->map(function ($data, $index) {
            $data['rank_position'] = $index + 1;
            return $data;
        });

        // Get sections and courses for filters
        $sections = \App\Models\Section::orderBy('name')->get();
        $courses = \App\Models\Course::orderBy('course_name')->get();

        return view('admin.leaderboards.index', compact(
            'ranked', 
            'periodType', 
            'sections', 
            'courses',
            'sectionId',
            'courseId',
            'sort',
            'direction'
        ));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'rank_position' => 'required|integer|min:1',
            'total_xp' => 'required|integer|min:0',
            'total_score' => 'required|numeric|min:0',
            'tasks_completed' => 'required|integer|min:0',
            'period_type' => 'required|in:weekly,monthly,semester,overall',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            
            $leaderboard = Leaderboard::create($validator->validated());
            
            DB::commit();
            
            return redirect()->route('admin.leaderboards.show', $leaderboard)
                ->with('success', 'Leaderboard entry created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to create leaderboard entry: ' . $e->getMessage());
        }
    }

    public function show(Leaderboard $leaderboard)
    {
        return view('admin.leaderboards.show', compact('leaderboard'));
    }

    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'format' => 'required|in:csv,pdf'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $query = Student::with(['xpTransactions', 'sections', 'course']);
        
        // Apply filters from request
        if ($request->section) {
            $query->whereHas('sections', fn($q) => $q->where('sections.id', $request->section));
        }

        if ($request->course) {
            $query->where('course_id', $request->course);
        }

        $periodType = $request->get('period', 'overall');
        if ($periodType !== 'overall') {
            $periodStart = now()->startOf($periodType);
            $periodEnd = now()->endOf($periodType);
            
            $query->whereHas('xpTransactions', function ($q) use ($periodStart, $periodEnd) {
                $q->whereBetween('processed_at', [$periodStart, $periodEnd]);
            });
        }

        // Get ranked data
        $ranked = $query->get()->map(function ($student) use ($periodType) {
            return [
                'Student Name' => $student->user->name,
                'Course' => $student->course->course_name,
                'Section' => $student->sections->pluck('name')->join(', '),
                'Total XP' => $student->xpTransactions
                    ->when($periodType !== 'overall', function ($txs) use ($periodType) {
                        return $txs->where('processed_at', '>=', now()->startOf($periodType));
                    })
                    ->sum('amount'),
                'Tasks Completed' => $student->xpTransactions
                    ->where('type', 'earned')
                    ->count(),
            ];
        })->sortByDesc('Total XP')->values();

        if ($request->format === 'pdf') {
            $pdf = new LeaderboardPDF();
            $pdf->generateLeaderboard(
                $ranked, 
                $periodType, 
                now()->format('F d, Y h:i A')
            );
            return response($pdf->Output('S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="leaderboard-' . now()->format('Y-m-d') . '.pdf"'
            ]);
        }

        // Default CSV export
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
