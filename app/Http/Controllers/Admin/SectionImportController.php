<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SectionImportController extends Controller
{
    public function downloadTemplate()
    {
        $path = public_path('templates/student_section_import_template.xlsx');

        if (! file_exists($path)) {
            return back()->with('import_error', 'Template file not found. Please contact the administrator.');
        }

        return response()->download($path, 'student_section_import_template.xlsx');
    }

    public function import(Request $request, Section $section)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        Log::info('SectionImport - STARTED for section: ' . $section->section_code . ' (ID: ' . $section->id . ')');

        try {
            $spreadsheet = IOFactory::load($request->file('import_file')->getRealPath());
        } catch (\Exception $e) {
            Log::error('SectionImport - File load failed: ' . $e->getMessage());
            return back()->with('import_error', 'Could not read the file: ' . $e->getMessage());
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows  = $sheet->toArray(null, true, true, true);

        Log::info('SectionImport - Total rows in sheet: ' . count($rows));

        // ── Auto-detect the header row ────────────────────────────────────────
        // Find the row where column A contains "student_number" (case-insensitive)
        // If no header found, assume data starts at row 1 (plain file with no header)
        $dataStartRow = 1;
        foreach ($rows as $rowIndex => $row) {
            $cellA = strtolower(trim((string)($row['A'] ?? '')));
            if ($cellA === 'student_number') {
                $dataStartRow = $rowIndex + 1; // data is the row after the header
                Log::info("SectionImport - Header found at row {$rowIndex}, data starts at row {$dataStartRow}");
                break;
            }
        }

        Log::info("SectionImport - Data start row: {$dataStartRow}");

        // Log first 5 data rows for debugging
        $preview = [];
        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex < $dataStartRow) continue;
            $val = trim((string)($row['A'] ?? ''));
            if ($val === '') continue;
            $preview[] = "Row {$rowIndex}: '{$val}'";
            if (count($preview) >= 5) break;
        }
        Log::info('SectionImport - First data rows: ' . (empty($preview) ? 'NONE FOUND' : implode(', ', $preview)));

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex < $dataStartRow) continue;

            $studentNumber = trim((string)($row['A'] ?? ''));
            if ($studentNumber === '') continue;

            $yearLevel = trim((string)($row['B'] ?? ''));

            Log::info("SectionImport - Processing row {$rowIndex}: student_number='{$studentNumber}'");

            // ── Look up student ───────────────────────────────────────────
            $student = Student::where('student_number', $studentNumber)->first();

            if (! $student) {
                $msg = "Row {$rowIndex}: Student number '{$studentNumber}' not found in the system.";
                $errors[] = $msg;
                Log::warning('SectionImport - ' . $msg);
                $skipped++;
                continue;
            }

            // ── Capacity check ────────────────────────────────────────────
            if ($section->capacity) {
                $currentCount = $section->students()->count();
                if ($currentCount >= $section->capacity) {
                    $msg = "Section is full (capacity: {$section->capacity}). '{$studentNumber}' and remaining rows were skipped.";
                    $errors[] = $msg;
                    Log::warning('SectionImport - ' . $msg);
                    $skipped++;
                    break;
                }
            }

            // ── Duplicate check ───────────────────────────────────────────
            if ($section->students()->where('students.id', $student->id)->exists()) {
                $msg = "Row {$rowIndex}: '{$studentNumber}' is already in this section (skipped).";
                $errors[] = $msg;
                Log::info('SectionImport - ' . $msg);
                $skipped++;
                continue;
            }

            // ── Attach to section ─────────────────────────────────────────
            try {
                $section->students()->attach($student->id);
                Log::info("SectionImport - SUCCESS: attached {$studentNumber} (student ID: {$student->id}) to section {$section->section_code}");
            } catch (\Exception $e) {
                $msg = "Row {$rowIndex}: Database error attaching '{$studentNumber}': " . $e->getMessage();
                $errors[] = $msg;
                Log::error('SectionImport - ' . $msg);
                $skipped++;
                continue;
            }

            // ── Update year_level if provided ─────────────────────────────
            if ($yearLevel !== '' && in_array((int) $yearLevel, [1, 2, 3, 4])) {
                $student->update(['year_level' => (int) $yearLevel]);
            }

            $imported++;
        }

        Log::info("SectionImport - DONE: {$imported} imported, {$skipped} skipped.");

        if ($imported === 0 && $skipped === 0) {
            $message = "No student numbers found in the file. Make sure column A contains student numbers (e.g. S82582294).";
        } else {
            $message = "Import complete: {$imported} student(s) added, {$skipped} skipped.";
        }

        return redirect()
            ->route('admin.sections.show', $section)
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
}