<?php

namespace App\Services;

use App\Models\ErrorRecord;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ErrorAnalyticsService
{
    public function getAnalyticsSummary($startDate = null, $endDate = null, $studentId = null)
    {
        $query = ErrorRecord::query();

        if ($startDate && $endDate) {
            $query->whereBetween('identified_at', [$startDate, $endDate]);
        }

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        return [
            'total_errors' => $query->count(),
            'average_severity' => $query->avg('severity_level'),
            'most_common_type' => $query->select('error_type', DB::raw('count(*) as count'))
                                      ->groupBy('error_type')
                                      ->orderByDesc('count')
                                      ->first(),
            'recent_trend' => $this->calculateRecentTrend(),
        ];
    }

    public function getErrorTrends($startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subMonths(3);
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();

        return ErrorRecord::select(
            DB::raw('DATE(identified_at) as date'),
            DB::raw('COUNT(*) as total'),
            DB::raw('AVG(severity_level) as avg_severity')
        )
        ->whereBetween('identified_at', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    }

    public function getTopErrors($limit = 5)
    {
        return ErrorRecord::select(
            'error_type',
            'error_description',
            DB::raw('COUNT(*) as frequency'),
            DB::raw('AVG(severity_level) as avg_severity')
        )
        ->groupBy('error_type', 'error_description')
        ->orderByDesc('frequency')
        ->limit($limit)
        ->get();
    }

    public function getSeverityDistribution()
    {
        return ErrorRecord::select(
            'severity_level',
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('severity_level')
        ->orderBy('severity_level')
        ->get();
    }

    private function calculateRecentTrend()
    {
        $lastMonth = Carbon::now()->subMonth();
        $currentMonth = Carbon::now();

        $lastMonthCount = ErrorRecord::whereBetween('identified_at', 
            [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()]
        )->count();

        $currentMonthCount = ErrorRecord::whereBetween('identified_at', 
            [$currentMonth->startOfMonth(), $currentMonth->endOfMonth()]
        )->count();

        return [
            'last_month' => $lastMonthCount,
            'current_month' => $currentMonthCount,
            'percentage_change' => $lastMonthCount > 0 
                ? (($currentMonthCount - $lastMonthCount) / $lastMonthCount) * 100 
                : 0
        ];
    }
}
