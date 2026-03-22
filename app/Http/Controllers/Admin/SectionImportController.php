<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;

class SectionImportController extends Controller
{
    public function downloadTemplate()
    {
        $path = public_path('templates/student_section_import_template.xlsx');

        if (! file_exists($path)) {
            // Fall back to a simple CSV if xlsx template is missing
            return response()->streamDownload(function () {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['student_number']);
                fputcsv($handle, ['2024-00001']);
                fputcsv($handle, ['2024-00002']);
                fclose($handle);
            }, 'student_section_import_template.csv', [
                'Content-Type' => 'text/csv',
            ]);
        }

        return response()->download($path, 'student_section_import_template.xlsx');
    }

    public function import(Request $request, Section $section)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ]);

        $file      = $request->file('import_file');
        $extension = strtolower($file->getClientOriginalExtension());
        $rows      = [];

        // ── Parse file ────────────────────────────────────────────────────────
        if (in_array($extension, ['xlsx', 'xls'])) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
                $rows        = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
            } catch (\Exception $e) {
                return back()->with('import_error', 'Could not read the file: ' . $e->getMessage());
            }
        } else {
            if (($handle = fopen($file->getPathname(), 'r')) !== false) {
                while (($row = fgetcsv($handle)) !== false) {
                    $rows[] = $row;
                }
                fclose($handle);
            }
        }

        if (empty($rows)) {
            return back()->with('import_error', 'No data found in the uploaded file.');
        }

        // ── Detect header row ─────────────────────────────────────────────────
        $dataStartIndex = null;
        $colStudentNo   = null;
        $colEmail       = null;

        foreach ($rows as $i => $row) {
            $colStudentFound = null;
            $colEmailFound   = null;

            foreach ($row as $colIdx => $cell) {
                $val = strtolower(trim((string)($cell ?? '')));
                if ($val === 'student number' || $val === 'student_number') {
                    $colStudentFound = $colIdx;
                }
                if (str_contains($val, 'email')) {
                    $colEmailFound = $colIdx;
                }
            }

            if ($colStudentFound !== null) {
                $colStudentNo   = $colStudentFound;
                $colEmail       = $colEmailFound;
                $dataStartIndex = $i + 1;
                break;
            }

            $col0 = strtolower(trim((string)($row[0] ?? '')));
            if (in_array($col0, ['student_number', 'student number'])) {
                $colStudentNo   = 0;
                $colEmail       = 1;
                $dataStartIndex = $i + 1;
                break;
            }
        }

        if ($dataStartIndex === null) {
            $dataStartIndex = 0;
            $colStudentNo   = 0;
            $colEmail       = 1;
        }

        // ── Process rows ──────────────────────────────────────────────────────
        $imported  = 0;
        $skipped   = 0;
        $errors    = [];
        $remaining = $section->capacity
            ? ($section->capacity - $section->students()->count())
            : PHP_INT_MAX;

        $skipPhrases = ['fill either', 'color guide', 'blue =', 'instructions:'];

        foreach ($rows as $index => $row) {
            if ($index < $dataStartIndex) continue;

            $studentNumber = trim((string)($row[$colStudentNo] ?? ''));
            $email         = $colEmail !== null ? trim((string)($row[$colEmail] ?? '')) : '';

            if ($studentNumber === '' && $email === '') continue;

            $snLower = strtolower($studentNumber);
            $skip    = false;
            foreach ($skipPhrases as $phrase) {
                if (str_contains($snLower, $phrase)) { $skip = true; break; }
            }
            if ($skip) continue;

            if (is_numeric($studentNumber) && $email === '') continue;

            $student = null;
            $tried   = [];

            if ($studentNumber !== '') {
                $tried[]  = $studentNumber;
                $student  = Student::where('student_number', $studentNumber)->first();
            }
            if (!$student && $email !== '') {
                $tried[] = $email;
                $student = Student::where('student_number', $email)
                    ->orWhereHas('user', fn($q) => $q->where('email', $email))
                    ->first();
            }

            if (!$student) {
                $errors[] = "Row " . ($index + 1) . ": Student '" . implode("' / '", $tried) . "' not found in the system.";
                $skipped++;
                continue;
            }

            if ($section->students()->where('student_id', $student->id)->exists()) {
                $errors[] = "Row " . ($index + 1) . ": '{$student->user->name}' is already in this section.";
                $skipped++;
                continue;
            }

            if ($imported >= $remaining) {
                $errors[] = "Section capacity reached. Remaining rows were skipped.";
                $skipped++;
                break;
            }

            $section->students()->attach($student->id);
            $imported++;
        }

        return redirect()
            ->route('admin.sections.show', $section)
            ->with('import_success', [
                'imported' => $imported,
                'skipped'  => $skipped,
            ])
            ->with('import_errors', $errors);
    }
}