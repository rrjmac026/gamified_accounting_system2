<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\PerformanceLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PerformanceLogController extends Controller
{
    /**
     * Display a paginated list of performance logs with optional filters.
     */
    public function index(Request $request)
    {
        $query = PerformanceLog::with(['student', 'subject', 'task']);

        // Apply filters
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('date_from')) {
            $query->where('recorded_at', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->where('recorded_at', '<=', Carbon::parse($request->date_to));
        }

        $logs = $query->latest('recorded_at')->paginate(15);

        return view('admin.performance_logs.index', compact('logs'));
    }

    /**
     * Show a single performance log in detail.
     */
    public function show(PerformanceLog $performanceLog)
    {
        $performanceLog->load(['student', 'subject', 'task']);
        return view('admin.performance_logs.show', compact('performanceLog'));
    }

    /**
     * Get performance summary for a specific student.
     */
    public function getStudentPerformance($studentId)
    {
        $logs = PerformanceLog::where('student_id', $studentId)
            ->with(['subject', 'task'])
            ->latest('recorded_at')
            ->get();

        $performance = [
            'overall_average' => $logs->avg('value'),
            'by_subject' => $logs->groupBy('subject_id')
                ->map(fn($group) => [
                    'subject_name' => $group->first()->subject->name,
                    'average' => $group->avg('value'),
                    'logs_count' => $group->count()
                ]),
            'recent_logs' => $logs->take(5)
        ];

        return view('admin.performance_logs.student_performance', compact('performance', 'logs'));
    }

    /**
     * Get performance statistics for a specific subject.
     */
    public function getSubjectStatistics($subjectId)
    {
        $logs = PerformanceLog::where('subject_id', $subjectId)
            ->with(['student'])
            ->get();

        $statistics = [
            'class_average' => $logs->avg('value'),
            'highest_score' => $logs->max('value'),
            'lowest_score' => $logs->min('value'),
            'total_logs' => $logs->count(),
            'performance_distribution' => $logs->groupBy('student_id')
                ->map(fn($group) => [
                    'student_name' => $group->first()->student->user->full_name,
                    'average' => $group->avg('value')
                ])
        ];

        return view('admin.performance_logs.subject_statistics', compact('statistics', 'logs'));
    }

    /**
     * Optionally allow deletion if necessary.
     */
    public function destroy(PerformanceLog $performanceLog)
    {
        $performanceLog->delete();

        return redirect()->route('admin.performance_logs.index')
            ->with('success', 'Performance log deleted successfully');
    }
}
