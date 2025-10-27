<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Subject;
use App\Models\Task;
use App\Models\ActivityLog;
use App\Models\FeedbackRecord;
use App\Models\Evaluation;
use App\Models\XpTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\StudentsReportExport;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function generateStudentReport(Request $request)
    {
        $students = Student::with(['user', 'subjects', 'assignedTasks'])
            ->when($request->date_from, function($query) use ($request) {
                return $query->where('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function($query) use ($request) {
                return $query->where('created_at', '<=', $request->date_to);
            })
            ->get();

        $stats = [
            'total_students' => $students->count(),
            'active_students' => $students->filter(fn($s) => $s->user->is_active)->count(),
            'average_xp' => $students->avg('total_xp'),
            'total_tasks_completed' => $students->sum(fn($s) => $s->assignedTasks->where('status', 'completed')->count())
        ];

        return view('admin.reports.students', compact('students', 'stats'));
    }

    public function generateInstructorReport(Request $request)
    {
        $instructors = Instructor::with(['user', 'subjects', 'tasks'])
            ->when($request->date_from, function($query) use ($request) {
                return $query->where('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function($query) use ($request) {
                return $query->where('created_at', '<=', $request->date_to);
            })
            ->get();

        $stats = [
            'total_instructors' => $instructors->count(),
            'active_instructors' => $instructors->filter(fn($i) => $i->user->is_active)->count(),
            'total_subjects_handled' => $instructors->sum(fn($i) => $i->subjects->count()),
            'total_tasks_created' => $instructors->sum(fn($i) => $i->tasks->count())
        ];

        return view('admin.reports.instructors', compact('instructors', 'stats'));
    }

    public function generateTaskReport(Request $request)
    {
        $tasks = Task::with(['instructor', 'subject', 'submissions'])
            ->when($request->date_from, function($query) use ($request) {
                return $query->where('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function($query) use ($request) {
                return $query->where('created_at', '<=', $request->date_to);
            })
            ->get();

        $stats = [
            'total_tasks' => $tasks->count(),
            'active_tasks' => $tasks->where('is_active', true)->count(),
            'average_completion_rate' => $tasks->avg(fn($t) => 
                $t->submissions->where('status', 'completed')->count() / max(1, $t->submissions->count()) * 100
            ),
            'tasks_by_type' => $tasks->groupBy('type')->map->count()
        ];

        return view('admin.reports.tasks', compact('tasks', 'stats'));
    }

    public function generateActivityReport(Request $request)
    {
        $activities = ActivityLog::with('user')
            ->when($request->date_from, function($query) use ($request) {
                return $query->where('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function($query) use ($request) {
                return $query->where('created_at', '<=', $request->date_to);
            })
            ->latest()
            ->get();

        $stats = [
            'total_activities' => $activities->count(),
            'activities_by_type' => $activities->groupBy('action')->map->count(),
            'most_active_users' => $activities->groupBy('user_id')
                ->map(fn($group) => [
                    'user' => $group->first()->user->name,
                    'count' => $group->count()
                ])
                ->sortByDesc('count')
                ->take(5)
        ];

        return view('admin.reports.activities', compact('activities', 'stats'));
    }

    public function generateEvaluationReport(Request $request)
    {
        $evaluations = Evaluation::with(['student', 'instructor'])
            ->when($request->date_from, function($query) use ($request) {
                return $query->where('submitted_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function($query) use ($request) {
                return $query->where('submitted_at', '<=', $request->date_to);
            })
            ->get();

        $stats = [
            'total_evaluations' => $evaluations->count(),
            'average_rating' => $evaluations->avg('rating'),
            'evaluations_by_instructor' => $evaluations->groupBy('instructor_id')
                ->map(fn($group) => [
                    'instructor' => $group->first()->instructor->user->name,
                    'count' => $group->count(),
                    'avg_rating' => $group->avg('rating')
                ])
        ];

        return view('admin.reports.evaluations', compact('evaluations', 'stats'));
    }

    public function exportStudentReport(Request $request)
    {
        $students = Student::with(['user', 'subjects', 'assignedTasks'])
            ->when($request->date_from, function($query) use ($request) {
                return $query->where('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function($query) use ($request) {
                return $query->where('created_at', '<=', $request->date_to);
            })
            ->get();

        $stats = [
            'total_students' => $students->count(),
            'active_students' => $students->filter(fn($s) => $s->user->is_active)->count(),
            'average_xp' => $students->avg('total_xp'),
            'total_tasks_completed' => $students->sum(fn($s) => $s->assignedTasks->where('status', 'completed')->count())
        ];

        if ($request->format === 'excel') {
            return Excel::download(new StudentsReportExport($students, $stats), 'students-report.xlsx');
        }

        // Default to PDF using FPDF
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        
        // Title
        $pdf->Cell(0, 10, 'Student Performance Report', 0, 1, 'C');
        $pdf->Ln(10);

        // Statistics
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Summary Statistics', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        foreach ($stats as $key => $value) {
            $label = ucwords(str_replace('_', ' ', $key));
            $pdf->Cell(0, 8, "$label: $value", 0, 1);
        }
        $pdf->Ln(10);

        // Table Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(60, 10, 'Student Name', 1);
        $pdf->Cell(40, 10, 'Total XP', 1);
        $pdf->Cell(45, 10, 'Tasks Completed', 1);
        $pdf->Cell(45, 10, 'Performance', 1);
        $pdf->Ln();

        // Table Content
        $pdf->SetFont('Arial', '', 12);
        foreach ($students as $student) {
            $pdf->Cell(60, 10, $student->user->name, 1);
            $pdf->Cell(40, 10, number_format($student->total_xp), 1);
            $pdf->Cell(45, 10, $student->assignedTasks->where('status', 'completed')->count(), 1);
            $pdf->Cell(45, 10, number_format($student->performance_rating, 1) . '%', 1);
            $pdf->Ln();
        }

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="students-report.pdf"'
        ]);
    }
}
