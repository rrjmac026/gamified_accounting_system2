<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Subject;
use App\Models\Section;
use App\Models\Task;
use App\Models\ActivityLog;
use App\Models\FeedbackRecord;
use App\Models\Evaluation;
use App\Models\XpTransaction;
use App\Models\PerformanceTask;
use App\Models\PerformanceTaskSubmission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsReportExport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use FPDF;
use Exception;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function index()
    {
        // Get all instructors with their sections for filtering
        $instructors = Instructor::with('user')->get();
        $sections = Section::with('course')->get();
        
        return view('admin.reports.index', compact('instructors', 'sections'));
    }

    /**
     * Export student grades by instructor and section to Excel
     */
    public function exportGradesExcel(Request $request)
    {
        try {
            $instructorId = $request->input('instructor_id');
            $sectionId = $request->input('section_id');

            // Build query with proper relationships
            $query = PerformanceTask::with([
                'submissions.student.user',
                'submissions.student.sections.course',
                'instructor.user',
                'subject'
            ]);

            if ($instructorId) {
                $query->where('instructor_id', $instructorId);
            }

            $tasks = $query->latest()->get();

            if ($tasks->isEmpty()) {
                return back()->with('error', 'No data found for the selected filters.');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set document properties
            $instructor = $instructorId ? Instructor::with('user')->find($instructorId) : null;
            $section = $sectionId ? Section::with('course')->find($sectionId) : null;
            
            $spreadsheet->getProperties()
                ->setCreator('Admin')
                ->setTitle('Student Grades Report')
                ->setSubject('Performance Task Grades')
                ->setDescription('Export of student grades by instructor and section');

            // Header styling
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];

            // Report title
            $sheet->setCellValue('A1', 'Student Grades Report');
            $sheet->mergeCells('A1:K1');
            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);

            // Filters info
            $row = 2;
            if ($instructor) {
                $sheet->setCellValue('A' . $row, 'Instructor: ' . $instructor->user->name);
                $sheet->mergeCells('A' . $row . ':K' . $row);
                $row++;
            }
            if ($section) {
                $sheet->setCellValue('A' . $row, 'Section: ' . $section->name . ' (' . $section->course->name . ')');
                $sheet->mergeCells('A' . $row . ':K' . $row);
                $row++;
            }
            $sheet->setCellValue('A' . $row, 'Generated: ' . date('F d, Y h:i A'));
            $sheet->mergeCells('A' . $row . ':K' . $row);
            $row += 2;

            // Set column headers
            $headers = ['#', 'Student Name', 'Student Email', 'Section', 'Instructor', 'Subject', 'Task Title', 'Total Score', 'Completed Steps', 'Total Attempts', 'Status'];
            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . $row, $header);
                $sheet->getStyle($column . $row)->applyFromArray($headerStyle);
                $column++;
            }

            // Auto-size columns
            foreach (range('A', 'K') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Populate data
            $row++;
            $counter = 1;

            foreach ($tasks as $task) {
                $submissions = $task->submissions->groupBy('student_id');

                foreach ($submissions as $studentId => $studentSubs) {
                    $student = $studentSubs->first()->student;
                    $user = $student->user;
                    
                    if (!$user) {
                        continue;
                    }

                    // Get student's sections
                    $studentSections = $student->sections;
                    
                    // If section filter is applied, skip students not in that section
                    if ($sectionId && !$studentSections->contains('id', $sectionId)) {
                        continue;
                    }
                    
                    // Get section names
                    $sectionNames = $studentSections->pluck('name')->join(', ') ?: 'N/A';
                    
                    $completedSteps = $studentSubs->where('status', 'correct')->count();
                    $totalScore = $studentSubs->sum('score');
                    $totalAttempts = $studentSubs->sum('attempts');
                    
                    // Determine status
                    if ($completedSteps == 10) {
                        $status = 'Completed';
                        $statusColor = '70AD47';
                    } elseif ($completedSteps > 0) {
                        $status = 'In Progress';
                        $statusColor = 'FFC000';
                    } else {
                        $status = 'Not Started';
                        $statusColor = 'C00000';
                    }

                    $sheet->setCellValue('A' . $row, $counter++);
                    $sheet->setCellValue('B' . $row, $user->name);
                    $sheet->setCellValue('C' . $row, $user->email);
                    $sheet->setCellValue('D' . $row, $sectionNames);
                    $sheet->setCellValue('E' . $row, $task->instructor->user->name ?? 'N/A');
                    $sheet->setCellValue('F' . $row, optional($task->subject)->subject_name ?? 'N/A');
                    $sheet->setCellValue('G' . $row, $task->title);
                    $sheet->setCellValue('H' . $row, $totalScore);
                    $sheet->setCellValue('I' . $row, $completedSteps . '/10');
                    $sheet->setCellValue('J' . $row, $totalAttempts);
                    $sheet->setCellValue('K' . $row, $status);

                    // Apply row styling
                    $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]]
                    ]);

                    // Color code status
                    $sheet->getStyle('K' . $row)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $statusColor]],
                        'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                    ]);

                    $row++;
                }
            }

            // Add summary row
            $row++;
            $sheet->setCellValue('A' . $row, 'Total Records:');
            $sheet->setCellValue('B' . $row, $counter - 1);
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7E6E6']]
            ]);

            // Generate filename
            $filename = 'student_grades_report_' . date('Y-m-d_His') . '.xlsx';

            // Create writer and download
            $writer = new Xlsx($spreadsheet);
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;

        } catch (Exception $e) {
            Log::error('Error exporting grades to Excel: ' . $e->getMessage());
            return back()->with('error', 'Failed to export grades. Please try again.');
        }
    }

    /**
     * Export student grades by instructor and section to PDF
     */
    public function exportGradesPdf(Request $request)
    {
        try {
            $instructorId = $request->input('instructor_id');
            $sectionId = $request->input('section_id');

            // Build query with proper relationships
            $query = PerformanceTask::with([
                'submissions.student.user',
                'submissions.student.sections.course',
                'instructor.user',
                'subject'
            ]);

            if ($instructorId) {
                $query->where('instructor_id', $instructorId);
            }

            $tasks = $query->latest()->get();

            if ($tasks->isEmpty()) {
                return back()->with('error', 'No data found for the selected filters.');
            }

            $instructor = $instructorId ? Instructor::with('user')->find($instructorId) : null;
            $section = $sectionId ? Section::with('course')->find($sectionId) : null;

            $pdf = new FPDF('L', 'mm', 'A4');
            $pdf->SetAutoPageBreak(true, 15);
            $pdf->AddPage();

            // Title
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'Student Grades Report', 0, 1, 'C');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 5, 'Generated on: ' . date('F d, Y h:i A'), 0, 1, 'C');
            
            if ($instructor) {
                $pdf->Cell(0, 5, 'Instructor: ' . $instructor->user->name, 0, 1, 'C');
            }
            if ($section) {
                $pdf->Cell(0, 5, 'Section: ' . $section->name . ' (' . $section->course->name . ')', 0, 1, 'C');
            }
            
            $pdf->Ln(5);

            // Table headers
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(68, 114, 196);
            $pdf->SetTextColor(255, 255, 255);
            
            $pdf->Cell(8, 8, '#', 1, 0, 'C', true);
            $pdf->Cell(40, 8, 'Student Name', 1, 0, 'C', true);
            $pdf->Cell(30, 8, 'Section', 1, 0, 'C', true);
            $pdf->Cell(35, 8, 'Instructor', 1, 0, 'C', true);
            $pdf->Cell(30, 8, 'Subject', 1, 0, 'C', true);
            $pdf->Cell(50, 8, 'Task Title', 1, 0, 'C', true);
            $pdf->Cell(18, 8, 'Score', 1, 0, 'C', true);
            $pdf->Cell(18, 8, 'Steps', 1, 0, 'C', true);
            $pdf->Cell(21, 8, 'Status', 1, 1, 'C', true);

            // Reset text color
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', '', 7);

            $counter = 1;
            $fillToggle = false;

            foreach ($tasks as $task) {
                $submissions = $task->submissions->groupBy('student_id');

                foreach ($submissions as $studentId => $studentSubs) {
                    $student = $studentSubs->first()->student;
                    $user = $student->user;
                    
                    if (!$user) {
                        continue;
                    }

                    // Get student's sections
                    $studentSections = $student->sections;
                    
                    // If section filter is applied, skip students not in that section
                    if ($sectionId && !$studentSections->contains('id', $sectionId)) {
                        continue;
                    }
                    
                    // Get section names (limit to first section for PDF space)
                    $sectionName = $studentSections->first()->name ?? 'N/A';
                    
                    $completedSteps = $studentSubs->where('status', 'correct')->count();
                    $totalScore = $studentSubs->sum('score');
                    
                    if ($completedSteps == 10) {
                        $status = 'Completed';
                    } elseif ($completedSteps > 0) {
                        $status = 'In Progress';
                    } else {
                        $status = 'Not Started';
                    }

                    if ($fillToggle) {
                        $pdf->SetFillColor(240, 240, 240);
                    } else {
                        $pdf->SetFillColor(255, 255, 255);
                    }

                    $pdf->Cell(8, 7, $counter++, 1, 0, 'C', true);
                    $pdf->Cell(40, 7, substr($user->name, 0, 22), 1, 0, 'L', true);
                    $pdf->Cell(30, 7, substr($sectionName, 0, 18), 1, 0, 'L', true);
                    $pdf->Cell(35, 7, substr($task->instructor->user->name ?? 'N/A', 0, 20), 1, 0, 'L', true);
                    $pdf->Cell(30, 7, substr(optional($task->subject)->subject_name ?? 'N/A', 0, 18), 1, 0, 'L', true);
                    $pdf->Cell(50, 7, substr($task->title, 0, 28), 1, 0, 'L', true);
                    $pdf->Cell(18, 7, $totalScore, 1, 0, 'C', true);
                    $pdf->Cell(18, 7, $completedSteps . '/10', 1, 0, 'C', true);
                    $pdf->Cell(21, 7, $status, 1, 1, 'C', true);

                    $fillToggle = !$fillToggle;

                    if ($pdf->GetY() > 180) {
                        $pdf->AddPage();
                        
                        // Repeat headers
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->SetFillColor(68, 114, 196);
                        $pdf->SetTextColor(255, 255, 255);
                        
                        $pdf->Cell(8, 8, '#', 1, 0, 'C', true);
                        $pdf->Cell(40, 8, 'Student Name', 1, 0, 'C', true);
                        $pdf->Cell(30, 8, 'Section', 1, 0, 'C', true);
                        $pdf->Cell(35, 8, 'Instructor', 1, 0, 'C', true);
                        $pdf->Cell(30, 8, 'Subject', 1, 0, 'C', true);
                        $pdf->Cell(50, 8, 'Task Title', 1, 0, 'C', true);
                        $pdf->Cell(18, 8, 'Score', 1, 0, 'C', true);
                        $pdf->Cell(18, 8, 'Steps', 1, 0, 'C', true);
                        $pdf->Cell(21, 8, 'Status', 1, 1, 'C', true);

                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetFont('Arial', '', 7);
                    }
                }
            }

            // Summary
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 8, 'Total Records: ' . ($counter - 1), 0, 1, 'L');

            $filename = 'student_grades_report_' . date('Y-m-d_His') . '.pdf';

            return response($pdf->Output('S', $filename))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (Exception $e) {
            Log::error('Error exporting grades to PDF: ' . $e->getMessage());
            return back()->with('error', 'Failed to export grades. Please try again.');
        }
    }

    /**
     * Get sections by instructor (AJAX endpoint)
     */
    public function getInstructorSections($instructorId)
    {
        $sections = Section::whereHas('instructors', function($query) use ($instructorId) {
            $query->where('instructor_id', $instructorId);
        })->with('course')->get();
        
        return response()->json($sections);
    }
}