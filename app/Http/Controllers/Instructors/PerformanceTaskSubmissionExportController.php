<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\PerformanceTask;
use App\Models\PerformanceTaskSubmission;
use Exception;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use FPDF;

class PerformanceTaskSubmissionExportController extends Controller
{
    /**
     * Export all submissions to Excel
     */
    public function exportExcel()
    {
        try {
            $instructor = auth()->user()->instructor;
            
            if (!$instructor) {
                throw new Exception('Not authorized as an instructor');
            }

            // Get all performance tasks for this instructor with proper eager loading
            $tasks = PerformanceTask::where('instructor_id', $instructor->id)
                ->with(['submissions.student.user']) // Load student AND user
                ->latest()
                ->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator($instructor->user->name)
                ->setTitle('Performance Task Submissions Report')
                ->setSubject('Student Submissions')
                ->setDescription('Export of all performance task submissions');

            // Header styling
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];

            // Set column headers
            $headers = ['#', 'Student Name', 'Student Email', 'Task Title', 'Total Score', 'Completed Steps', 'Total Attempts', 'Progress %', 'Status'];
            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '1', $header);
                $sheet->getStyle($column . '1')->applyFromArray($headerStyle);
                $column++;
            }

            // Auto-size columns
            foreach (range('A', 'I') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Populate data
            $row = 2;
            $counter = 1;

            foreach ($tasks as $task) {
                $submissions = $task->submissions->groupBy('student_id');

                foreach ($submissions as $studentId => $studentSubs) {
                    $student = $studentSubs->first()->student;
                    $user = $student->user; // Get the user from student
                    
                    // Skip if user doesn't exist
                    if (!$user) {
                        continue;
                    }
                    
                    $completedSteps = $studentSubs->where('status', 'correct')->count();
                    $totalScore = $studentSubs->sum('score');
                    $totalAttempts = $studentSubs->sum('attempts');
                    $progressPercent = ($completedSteps / 10) * 100;
                    
                    // Determine status
                    if ($completedSteps == 10) {
                        $status = 'Completed';
                        $statusColor = '70AD47'; // Green
                    } elseif ($completedSteps > 0) {
                        $status = 'In Progress';
                        $statusColor = 'FFC000'; // Orange
                    } else {
                        $status = 'Not Started';
                        $statusColor = 'C00000'; // Red
                    }

                    $sheet->setCellValue('A' . $row, $counter++);
                    $sheet->setCellValue('B' . $row, $user->name);
                    $sheet->setCellValue('C' . $row, $user->email);
                    $sheet->setCellValue('D' . $row, $task->title);
                    $sheet->setCellValue('E' . $row, $totalScore);
                    $sheet->setCellValue('F' . $row, $completedSteps . '/10');
                    $sheet->setCellValue('G' . $row, $totalAttempts);
                    $sheet->setCellValue('H' . $row, number_format($progressPercent, 1) . '%');
                    $sheet->setCellValue('I' . $row, $status);

                    // Apply row styling
                    $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]]
                    ]);

                    // Color code status
                    $sheet->getStyle('I' . $row)->applyFromArray([
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
            $filename = 'performance_task_submissions_' . date('Y-m-d_His') . '.xlsx';

            // Create writer and download
            $writer = new Xlsx($spreadsheet);
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;

        } catch (Exception $e) {
            Log::error('Error exporting submissions to Excel: ' . $e->getMessage());
            return back()->with('error', 'Failed to export submissions. Please try again.');
        }
    }

    /**
     * Export all submissions to PDF
     */
    public function exportPdf()
    {
        try {
            $instructor = auth()->user()->instructor;
            
            if (!$instructor) {
                throw new Exception('Not authorized as an instructor');
            }

            // Get all performance tasks for this instructor with proper eager loading
            $tasks = PerformanceTask::where('instructor_id', $instructor->id)
                ->with(['submissions.student.user']) // Load student AND user
                ->latest()
                ->get();

            $pdf = new Fpdf('L', 'mm', 'A4'); // Landscape orientation
            $pdf->SetAutoPageBreak(true, 15);
            $pdf->AddPage();

            // Title
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'Performance Task Submissions Report', 0, 1, 'C');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 5, 'Generated on: ' . date('F d, Y h:i A'), 0, 1, 'C');
            $pdf->Cell(0, 5, 'Instructor: ' . $instructor->user->name, 0, 1, 'C');
            $pdf->Ln(5);

            // Table headers
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(68, 114, 196);
            $pdf->SetTextColor(255, 255, 255);
            
            $pdf->Cell(10, 8, '#', 1, 0, 'C', true);
            $pdf->Cell(50, 8, 'Student Name', 1, 0, 'C', true);
            $pdf->Cell(65, 8, 'Task Title', 1, 0, 'C', true);
            $pdf->Cell(20, 8, 'Score', 1, 0, 'C', true);
            $pdf->Cell(25, 8, 'Steps', 1, 0, 'C', true);
            $pdf->Cell(25, 8, 'Attempts', 1, 0, 'C', true);
            $pdf->Cell(25, 8, 'Progress', 1, 0, 'C', true);
            $pdf->Cell(30, 8, 'Status', 1, 1, 'C', true);

            // Reset text color
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', '', 8);

            $counter = 1;
            $fillToggle = false;

            foreach ($tasks as $task) {
                $submissions = $task->submissions->groupBy('student_id');

                foreach ($submissions as $studentId => $studentSubs) {
                    $student = $studentSubs->first()->student;
                    $user = $student->user; // Get the user from student
                    
                    // Skip if user doesn't exist
                    if (!$user) {
                        continue;
                    }
                    
                    $completedSteps = $studentSubs->where('status', 'correct')->count();
                    $totalScore = $studentSubs->sum('score');
                    $totalAttempts = $studentSubs->sum('attempts');
                    $progressPercent = number_format(($completedSteps / 10) * 100, 1);
                    
                    // Determine status
                    if ($completedSteps == 10) {
                        $status = 'Completed';
                    } elseif ($completedSteps > 0) {
                        $status = 'In Progress';
                    } else {
                        $status = 'Not Started';
                    }

                    // Alternate row colors
                    if ($fillToggle) {
                        $pdf->SetFillColor(240, 240, 240);
                    } else {
                        $pdf->SetFillColor(255, 255, 255);
                    }

                    $pdf->Cell(10, 7, $counter++, 1, 0, 'C', true);
                    $pdf->Cell(50, 7, substr($user->name, 0, 30), 1, 0, 'L', true);
                    $pdf->Cell(65, 7, substr($task->title, 0, 40), 1, 0, 'L', true);
                    $pdf->Cell(20, 7, $totalScore, 1, 0, 'C', true);
                    $pdf->Cell(25, 7, $completedSteps . '/10', 1, 0, 'C', true);
                    $pdf->Cell(25, 7, $totalAttempts, 1, 0, 'C', true);
                    $pdf->Cell(25, 7, $progressPercent . '%', 1, 0, 'C', true);
                    $pdf->Cell(30, 7, $status, 1, 1, 'C', true);

                    $fillToggle = !$fillToggle;

                    // Check if we need a new page
                    if ($pdf->GetY() > 180) {
                        $pdf->AddPage();
                        
                        // Repeat headers
                        $pdf->SetFont('Arial', 'B', 9);
                        $pdf->SetFillColor(68, 114, 196);
                        $pdf->SetTextColor(255, 255, 255);
                        
                        $pdf->Cell(10, 8, '#', 1, 0, 'C', true);
                        $pdf->Cell(50, 8, 'Student Name', 1, 0, 'C', true);
                        $pdf->Cell(65, 8, 'Task Title', 1, 0, 'C', true);
                        $pdf->Cell(20, 8, 'Score', 1, 0, 'C', true);
                        $pdf->Cell(25, 8, 'Steps', 1, 0, 'C', true);
                        $pdf->Cell(25, 8, 'Attempts', 1, 0, 'C', true);
                        $pdf->Cell(25, 8, 'Progress', 1, 0, 'C', true);
                        $pdf->Cell(30, 8, 'Status', 1, 1, 'C', true);

                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetFont('Arial', '', 8);
                    }
                }
            }

            // Summary
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 8, 'Total Records: ' . ($counter - 1), 0, 1, 'L');

            // Generate filename
            $filename = 'performance_task_submissions_' . date('Y-m-d_His') . '.pdf';

            // Output PDF
            return response($pdf->Output('S', $filename))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (Exception $e) {
            Log::error('Error exporting submissions to PDF: ' . $e->getMessage());
            return back()->with('error', 'Failed to export submissions. Please try again.');
        }
    }
}