<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 

use App\Models\ErrorRecord;
use App\Services\ErrorAnalyticsService;
use Illuminate\Http\Request;
use App\Http\Requests\ErrorRecordRequest;

class ErrorRecordController extends Controller
{
    protected $analyticsService;

    public function __construct(ErrorAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index(Request $request)
    {
        $query = ErrorRecord::with(['student', 'submission', 'question']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('error_type')) {
            $query->where('error_type', $request->error_type);
        }

        if ($request->filled('severity')) {
            $query->where('severity_level', $request->severity);
        }

        $errorRecords = $query->latest('identified_at')->paginate(15);

        $analytics = $this->analyticsService->getAnalyticsSummary();

        return view('error-records.index', compact('errorRecords', 'analytics'));
    }

    public function store(ErrorRecordRequest $request)
    {
        $errorRecord = ErrorRecord::create($request->validated());

        return redirect()->route('error-records.index')
                         ->with('success', 'Error record created successfully.');
    }

    public function show(ErrorRecord $errorRecord)
    {
        $errorRecord->load(['student', 'submission', 'question']);
        return view('error-records.show', compact('errorRecord'));
    }

    public function update(ErrorRecordRequest $request, ErrorRecord $errorRecord)
    {
        $errorRecord->update($request->validated());

        return redirect()->route('error-records.index')
                         ->with('success', 'Error record updated successfully.');
    }

    public function destroy(ErrorRecord $errorRecord)
    {
        $errorRecord->delete();

        return redirect()->route('error-records.index')
                         ->with('success', 'Error record deleted successfully.');
    }

    public function analytics(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $studentId = $request->get('student_id');

        $summary = $this->analyticsService->getAnalyticsSummary($startDate, $endDate, $studentId);
        $trends = $this->analyticsService->getErrorTrends($startDate, $endDate);
        $topErrors = $this->analyticsService->getTopErrors();
        $severityDistribution = $this->analyticsService->getSeverityDistribution();

        return view('error-records.analytics', compact('summary', 'trends', 'topErrors', 'severityDistribution'));
    }
}
