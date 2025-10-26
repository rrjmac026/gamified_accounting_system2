<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\ActivityLog;
use App\Http\Requests\ActivityLog\ActivityLogFilterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs
     */
    public function index(ActivityLogFilterRequest $request)
    {
        $query = ActivityLog::with('user');

        // Apply filters
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->where('performed_at', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->where('performed_at', '<=', Carbon::parse($request->date_to));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('action', 'LIKE', "%{$search}%")
                  ->orWhere('model_type', 'LIKE', "%{$search}%")
                  ->orWhere('ip_address', 'LIKE', "%{$search}%");
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'performed_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $logs = $query->paginate($request->per_page ?? 15);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $logs,
                'message' => 'Activity logs retrieved successfully'
            ]);
        }

        return view('admin.activity-logs.index', compact('logs'));
    }

    /**
     * Display the specified activity log
     */
    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $log,
                'message' => 'Activity log retrieved successfully'
            ]);
        }

        return view('admin.activity-logs.show', compact('log'));
    }

    /**
     * Get activity statistics
     */
    public function statistics()
    {
        $stats = [
            'total_logs' => ActivityLog::count(),
            'today_logs' => ActivityLog::whereDate('performed_at', Carbon::today())->count(),
            'actions_distribution' => ActivityLog::selectRaw('action, COUNT(*) as count')
                                               ->groupBy('action')
                                               ->get(),
            'models_distribution' => ActivityLog::selectRaw('model_type, COUNT(*) as count')
                                              ->groupBy('model_type')
                                              ->get(),
            'recent_activity' => ActivityLog::with('user')
                                          ->latest('performed_at')
                                          ->take(5)
                                          ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Activity statistics retrieved successfully'
        ]);
    }

    /**
     * Clear old activity logs
     */
    public function clear(Request $request)
    {
        $request->validate([
            'days_old' => 'required|integer|min:1'
        ]);

        $date = Carbon::now()->subDays($request->days_old);
        $count = ActivityLog::where('performed_at', '<', $date)->delete();

        return response()->json([
            'success' => true,
            'data' => ['deleted_count' => $count],
            'message' => "Successfully cleared {$count} old activity logs"
        ]);
    }
}
