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
     * PDF column widths (mm).
     * A4 landscape = 297mm. With 18mm total margins (9mm each side) = 279mm usable.
     * We use 260mm total so there is a 19mm safety buffer — Status will never clip.
     */
    private array $pdfCols = [
        '#'        =>  7,
        'Section'  => 28,
        'Student'  => 40,
        'Task'     => 45,
        'Score'    => 15,
        'Answered' => 18,
        'Correct'  => 16,
        'Passed'   => 15,
        'Wrong'    => 15,
        'Attempts' => 18,
        'Progress' => 19,
        'Status'   => 24,
    ]; // total = 260mm

    // ── Shared stats helper ───────────────────────────────────────────────────
    private function stepStats($studentSubs): array
    {
        $answered = $studentSubs->count();
        return [
            'answered' => $answered,
            'correct'  => $studentSubs->where('status', 'correct')->count(),
            'passed'   => $studentSubs->where('status', 'passed')->count(),
            'wrong'    => $studentSubs->where('status', 'wrong')->count(),
            'score'    => $studentSubs->sum('score'),
            'attempts' => $studentSubs->sum('attempts'),
            'progress' => number_format(($answered / 10) * 100, 1),
            'status'   => $answered === 10 ? 'All Answered'
                        : ($answered  >  0 ? 'In Progress' : 'Not Started'),
        ];
    }

    private function truncate(string $text, int $max): string
    {
        return mb_strlen($text) > $max ? mb_substr($text, 0, $max - 1) . '~' : $text;
    }

    // =========================================================================
    // EXCEL EXPORT
    // =========================================================================
    public function exportExcel()
    {
        try {
            $instructor = auth()->user()->instructor;
            if (!$instructor) throw new Exception('Not authorized as an instructor');

            $tasks = PerformanceTask::where('instructor_id', $instructor->id)
                ->with(['submissions.student.user', 'section'])
                ->latest()
                ->get();

            $spreadsheet = new Spreadsheet();
            $sheet       = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Submissions');

            $spreadsheet->getProperties()
                ->setCreator($instructor->user->name)
                ->setTitle('Performance Task Submissions Report');

            $headerStyle = [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical'   => Alignment::VERTICAL_CENTER,
                                'wrapText'   => true],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                                                 'color'       => ['rgb' => '2F5496']]],
            ];

            $headers = ['#', 'Section', 'Student Name', 'Student Email', 'Task Title',
                        'Total Score', 'Answered', 'Correct', 'Passed', 'Wrong',
                        'Not Started', 'Total Attempts', 'Progress %', 'Status'];

            $col = 'A';
            foreach ($headers as $h) {
                $sheet->setCellValue($col . '1', $h);
                $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
                $col++;
            }

            $sheet->getRowDimension(1)->setRowHeight(22);
            $sheet->freezePane('A2');

            foreach (range('A', 'N') as $c) {
                $sheet->getColumnDimension($c)->setAutoSize(true);
            }

            $row     = 2;
            $counter = 1;

            foreach ($tasks as $task) {
                $sectionName = $task->section->name ?? 'No Section';

                foreach ($task->submissions->groupBy('student_id') as $studentSubs) {
                    $user = $studentSubs->first()->student->user ?? null;
                    if (!$user) continue;

                    $s = $this->stepStats($studentSubs);

                    $statusColor = match($s['status']) {
                        'All Answered' => '70AD47',
                        'In Progress'  => 'FFC000',
                        default        => 'C00000',
                    };

                    $rowBg = $row % 2 === 0 ? 'EBF1FA' : 'FFFFFF';
                    $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                                                       'color'       => ['rgb' => 'D0D7E8']]],
                    ]);

                    $sheet->setCellValue("A{$row}", $counter++);
                    $sheet->setCellValue("B{$row}", $sectionName);
                    $sheet->setCellValue("C{$row}", $user->name);
                    $sheet->setCellValue("D{$row}", $user->email);
                    $sheet->setCellValue("E{$row}", $task->title);
                    $sheet->setCellValue("F{$row}", $s['score']);
                    $sheet->setCellValue("G{$row}", $s['answered'] . '/10');
                    $sheet->setCellValue("H{$row}", $s['correct']);
                    $sheet->setCellValue("I{$row}", $s['passed']);
                    $sheet->setCellValue("J{$row}", $s['wrong']);
                    $sheet->setCellValue("K{$row}", 10 - $s['answered']);
                    $sheet->setCellValue("L{$row}", $s['attempts']);
                    $sheet->setCellValue("M{$row}", $s['progress'] . '%');
                    $sheet->setCellValue("N{$row}", $s['status']);

                    // Center numeric columns
                    foreach (['A', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'] as $c) {
                        $sheet->getStyle("{$c}{$row}")
                              ->getAlignment()
                              ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }

                    // Status cell
                    $sheet->getStyle("N{$row}")->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $statusColor]],
                        'font'      => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);

                    // Breakdown font colors
                    if ($s['correct'] > 0) $sheet->getStyle("H{$row}")->getFont()->getColor()->setRGB('375623');
                    if ($s['passed']  > 0) $sheet->getStyle("I{$row}")->getFont()->getColor()->setRGB('1F4E79');
                    if ($s['wrong']   > 0) $sheet->getStyle("J{$row}")->getFont()->getColor()->setRGB('9C0006');

                    $row++;
                }
            }

            // Summary
            $row++;
            $sheet->setCellValue("A{$row}", 'Total Records:');
            $sheet->setCellValue("B{$row}", $counter - 1);
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFDA']],
            ]);

            $filename = 'performance_task_submissions_' . date('Y-m-d_His') . '.xlsx';
            $writer   = new Xlsx($spreadsheet);

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

    // =========================================================================
    // PDF EXPORT
    // =========================================================================
    public function exportPdf()
    {
        try {
            $instructor = auth()->user()->instructor;
            if (!$instructor) throw new Exception('Not authorized as an instructor');

            $tasks = PerformanceTask::where('instructor_id', $instructor->id)
                ->with(['submissions.student.user', 'section'])
                ->latest()
                ->get();

            // A4 Landscape. SetMargins(left, top, right).
            // We use 9mm each side = 279mm usable. Our columns = 260mm. Buffer = 19mm.
            $pdf = new Fpdf('L', 'mm', 'A4');
            $pdf->SetMargins(9, 10, 9);
            $pdf->SetAutoPageBreak(true, 12);
            $pdf->AddPage();

            $cols = $this->pdfCols;

            // ── Title block ───────────────────────────────────────────────
            $pdf->SetFont('Arial', 'B', 15);
            $pdf->SetTextColor(31, 73, 125);
            $pdf->Cell(0, 9, 'Performance Task Submissions Report', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor(80, 80, 80);
            $pdf->Cell(0, 5,
                'Generated on: ' . date('F d, Y h:i A') .
                '   |   Instructor: ' . $instructor->user->name,
                0, 1, 'C'
            );
            $pdf->Ln(2);

            // Divider
            $pdf->SetDrawColor(68, 114, 196);
            $pdf->SetLineWidth(0.5);
            $pdf->Line(9, $pdf->GetY(), 288, $pdf->GetY());
            $pdf->SetLineWidth(0.2);
            $pdf->SetDrawColor(180, 198, 231);
            $pdf->Ln(3);

            // Legend
            $pdf->SetFont('Arial', 'I', 7.5);
            $pdf->SetTextColor(110, 110, 110);
            $pdf->Cell(0, 4,
                'Answered = all submitted steps   |   Correct = perfect score' .
                '   |   Passed = above threshold   |   Wrong = below threshold / attempts exhausted',
                0, 1, 'C'
            );
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(2);

            // ── Table header closure ──────────────────────────────────────
            $printHeader = function () use ($pdf, $cols) {
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->SetFillColor(68, 114, 196);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(47, 84, 150);
                $pdf->SetLineWidth(0.3);

                foreach ($cols as $label => $width) {
                    $ln = ($label === 'Status') ? 1 : 0;
                    $pdf->Cell($width, 8, $label, 1, $ln, 'C', true);
                }

                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetDrawColor(180, 198, 231);
                $pdf->SetLineWidth(0.2);
            };

            $printHeader();

            $counter    = 1;
            $fillToggle = false;

            foreach ($tasks as $task) {
                $sectionName = $task->section->name ?? 'No Section';

                foreach ($task->submissions->groupBy('student_id') as $studentSubs) {
                    $user = $studentSubs->first()->student->user ?? null;
                    if (!$user) continue;

                    $s = $this->stepStats($studentSubs);

                    // Alternate row fill
                    $fillToggle
                        ? $pdf->SetFillColor(235, 241, 250)
                        : $pdf->SetFillColor(255, 255, 255);

                    $fillToggle = !$fillToggle;

                    // All data cells except Status
                    $cells = [
                        '#'        => [$counter++,                            'C'],
                        'Section'  => [$this->truncate($sectionName, 17),    'L'],
                        'Student'  => [$this->truncate($user->name, 25),     'L'],
                        'Task'     => [$this->truncate($task->title, 28),    'L'],
                        'Score'    => [$s['score'],                          'C'],
                        'Answered' => [$s['answered'] . '/10',               'C'],
                        'Correct'  => [$s['correct'],                        'C'],
                        'Passed'   => [$s['passed'],                         'C'],
                        'Wrong'    => [$s['wrong'],                          'C'],
                        'Attempts' => [$s['attempts'],                       'C'],
                        'Progress' => [$s['progress'] . '%',                 'C'],
                    ];

                    foreach ($cells as $key => [$value, $align]) {
                        $pdf->Cell($cols[$key], 7, $value, 1, 0, $align, true);
                    }

                    // Status cell — colored fill, white bold text, ends the row
                    [$r, $g, $b] = match($s['status']) {
                        'All Answered' => [112, 173,  71],
                        'In Progress'  => [255, 192,   0],
                        default        => [192,   0,   0],
                    };
                    $pdf->SetFillColor($r, $g, $b);
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->SetFont('Arial', 'B', 7.5);
                    $pdf->Cell($cols['Status'], 7, $s['status'], 1, 1, 'C', true);

                    // Reset
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('Arial', '', 8);

                    // New page with repeated header
                    if ($pdf->GetY() > 188) {
                        $pdf->AddPage();
                        $printHeader();
                        $fillToggle = false;
                    }
                }
            }

            // ── Footer ────────────────────────────────────────────────────
            $pdf->Ln(4);
            $pdf->SetDrawColor(68, 114, 196);
            $pdf->SetLineWidth(0.4);
            $pdf->Line(9, $pdf->GetY(), 288, $pdf->GetY());
            $pdf->SetLineWidth(0.2);
            $pdf->Ln(3);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetTextColor(31, 73, 125);
            $pdf->Cell(0, 6, 'Total Records: ' . ($counter - 1), 0, 1, 'L');

            // Color key
            $pdf->Ln(2);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetTextColor(60, 60, 60);
            $pdf->Cell(22, 5, 'Status Key: ', 0, 0);

            foreach ([
                'All Answered' => [112, 173, 71],
                'In Progress'  => [255, 192,  0],
                'Not Started'  => [192,   0,  0],
            ] as $label => [$r, $g, $b]) {
                $pdf->SetFillColor($r, $g, $b);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont('Arial', 'B', 7.5);
                $pdf->Cell(28, 5, $label, 1, 0, 'C', true);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(3, 5, '', 0, 0); // spacer
            }

            $filename = 'performance_task_submissions_' . date('Y-m-d_His') . '.pdf';

            return response($pdf->Output('S', $filename))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (Exception $e) {
            Log::error('Error exporting submissions to PDF: ' . $e->getMessage());
            return back()->with('error', 'Failed to export submissions. Please try again.');
        }
    }
}