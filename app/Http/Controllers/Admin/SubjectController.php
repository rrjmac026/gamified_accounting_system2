<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; 

use App\Models\Subject;
use App\Models\Instructor;
use App\Traits\Loggable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    use Loggable;

    public function index()
    {
        $subjects = Subject::with('instructors.user')->paginate(10);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $instructors = Instructor::all();
        return view('admin.subjects.create', compact('instructors'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_code'   => 'required|string|unique:subjects,subject_code',
            'subject_name'   => 'required|string|max:255',
            'description'    => 'required|string',
            'instructor_ids' => 'required|array',
            'instructor_ids.*' => 'exists:instructors,id',
            'semester'       => 'required|string',
            'academic_year'  => 'required|string',
            'units'          => 'required|integer|min:1|max:6',
            'is_active'      => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $subject = Subject::create([
                'subject_code' => $request->subject_code,
                'subject_name' => $request->subject_name,
                'description' => $request->description,
                'semester' => $request->semester,
                'academic_year' => $request->academic_year,
                'units' => $request->units,
                'is_active' => $request->is_active
            ]);

            $subject->instructors()->attach($request->instructor_ids);

            DB::commit();

            $this->logActivity(
                "Created Subject",
                "Subject",
                $subject->id,
                [
                    'subject_code' => $subject->subject_code,
                    'subject_name' => $subject->subject_name
                ]
            );

            return redirect()->route('admin.subjects.index')
                ->with('success', 'Subject created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to create subject: ' . $e->getMessage());
        }
    }

    public function show(Subject $subject)
    {
        $subject->load(['instructors.user', 'students.user', 'students.course']);
        $allInstructors = Instructor::with('user')->get();
        
        return view('admin.subjects.show', compact('subject', 'allInstructors'));
    }

    public function edit(Subject $subject)
    {
        $instructors = Instructor::all();
        return view('admin.subjects.edit', compact('subject', 'instructors'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validator = Validator::make($request->all(), [
            'subject_code'   => ['required', 'string', Rule::unique('subjects')->ignore($subject->id)],
            'subject_name'   => 'required|string|max:255',
            'description'    => 'required|string',
            'instructor_ids' => 'required|array',
            'instructor_ids.*' => 'exists:instructors,id',
            'semester'       => 'required|string',
            'academic_year'  => 'required|string',
            'units'          => 'required|integer|min:1|max:6',
            'is_active'      => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            $originalData = $subject->toArray();

            $subject->update([
                'subject_code' => $request->subject_code,
                'subject_name' => $request->subject_name,
                'description' => $request->description,
                'semester' => $request->semester,
                'academic_year' => $request->academic_year,
                'units' => $request->units,
                'is_active' => $request->is_active
            ]);

            $subject->instructors()->sync($request->instructor_ids);

            DB::commit();

            $this->logActivity(
                "Updated Subject",
                "Subject",
                $subject->id,
                [
                    'original' => $originalData,
                    'changes' => $subject->getChanges()
                ]
            );

            return redirect()->route('admin.subjects.index')
                ->with('success', 'Subject updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to update subject: ' . $e->getMessage());
        }
    }

    public function destroy(Subject $subject)
    {
        $subjectData = $subject->toArray();
        $subject->delete();

        $this->logActivity(
            "Deleted Subject",
            "Subject",
            $subject->id,
            ['subject_data' => $subjectData]
        );

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully');
    }

    public function showAssignInstructorsForm(Subject $subject)
    {
        $allInstructors = Instructor::with(['user'])->get();
        return view('admin.subjects.assign-instructors', compact('subject', 'allInstructors'));
    }

    public function assignInstructors(Request $request, Subject $subject)
    {
        $request->validate([
            'instructors' => 'required|array',
            'instructors.*' => 'exists:instructors,id'
        ]);

        $subject->instructors()->sync($request->instructors);
        
        $this->logActivity(
            "Assigned Instructors to Subject",
            "Subject",
            $subject->id,
            [
                'subject_code' => $subject->subject_code,
                'instructor_ids' => $request->instructors
            ]
        );

        return redirect()->route('admin.subjects.show', $subject)
            ->with('success', 'Instructors assigned successfully.');
    }

    public function downloadImportTemplate(Subject $subject)
    {
        $templatePath = public_path('templates/students_import_template.xlsx');

        if (!file_exists($templatePath)) {
            return $this->generateFallbackCsvTemplate();
        }

        return response()->download(
            $templatePath,
            'students_import_template.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    private function generateFallbackCsvTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_import_template.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['student_number']);
            fputcsv($handle, ['2024-00001']);
            fputcsv($handle, ['2024-00002']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }


    // ============================================================
    // REPLACE importStudents() in SubjectController.php
    // ============================================================

    public function importStudents(Request $request, Subject $subject)
    {
        $request->validate([
            'student_file' => 'required|file|mimes:csv,txt,xlsx|max:5120',
        ]);

        $file      = $request->file('student_file');
        $extension = strtolower($file->getClientOriginalExtension());

        try {
            $rows = $extension === 'xlsx'
                ? $this->parseXlsx($file->getRealPath())
                : $this->parseCsv($file->getRealPath());
        } catch (\Exception $e) {
            return back()->withErrors(['student_file' => 'Could not read file: ' . $e->getMessage()]);
        }

        if (empty($rows)) {
            return back()->withErrors(['student_file' => 'The file appears to be empty or unreadable.']);
        }

        // Find the header row dynamically — handles templates with instruction rows above data
        $headerRowIndex = null;
        foreach ($rows as $index => $row) {
            $firstCell = strtolower(trim($row[0] ?? ''));
            if ($firstCell === 'student_number') {
                $headerRowIndex = $index;
                break;
            }
        }

        if ($headerRowIndex === null) {
            return back()->withErrors([
                'student_file' => 'Invalid template. Could not find a "student_number" column header. Please use the provided template.'
            ]);
        }

        $imported   = 0;
        $skipped    = 0;
        $duplicates = 0;

        try {
            DB::beginTransaction();

            // Only process rows AFTER the header row
            foreach (array_slice($rows, $headerRowIndex + 1) as $row) {
                $studentNumber = trim($row[0] ?? '');

                // Skip empty or sample/example rows
                if (empty($studentNumber) || str_contains(strtolower($studentNumber), 'e.g')) {
                    continue;
                }

                $student = \App\Models\Student::where('student_number', $studentNumber)->first();

                if (!$student) {
                    $skipped++;
                    continue;
                }

                $alreadyEnrolled = $subject->students()
                    ->where('students.id', $student->id)
                    ->exists();

                if ($alreadyEnrolled) {
                    $duplicates++;
                    continue;
                }

                $subject->students()->attach($student->id, [
                    'enrollment_date' => now(),
                    'status'          => 'enrolled',
                ]);

                $imported++;
            }

            DB::commit();

            $this->logActivity("Imported Students to Subject", "Subject", $subject->id, [
                'subject_code' => $subject->subject_code,
                'imported'     => $imported,
                'skipped'      => $skipped,
                'duplicates'   => $duplicates,
            ]);

            $message = "Import complete: {$imported} student(s) enrolled.";
            if ($skipped > 0)    $message .= " {$skipped} not found in system and skipped.";
            if ($duplicates > 0) $message .= " {$duplicates} already enrolled (skipped).";

            return redirect()->route('admin.subjects.show', $subject)->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    // ── Private Helpers ───────────────────────────────────────────

    private function parseCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) throw new \Exception('Cannot open file.');
        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }
        fclose($handle);
        return $rows;
    }

    private function parseXlsx(string $path): array
    {
        // Uses PhpSpreadsheet if installed, otherwise falls back to ZIP/XML
        if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows  = [];
            foreach ($sheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $cells = [];
                foreach ($cellIterator as $cell) {
                    $cells[] = (string) $cell->getValue();
                }
                if (empty(array_filter($cells))) break;
                $rows[] = $cells;
            }
            return $rows;
        }

        // Fallback ZIP/XML parser (no extra dependency)
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) throw new \Exception('Cannot open XLSX file.');
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        $sheetXml         = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        $strings = [];
        if ($sharedStringsXml) {
            $ssDoc = simplexml_load_string($sharedStringsXml);
            foreach ($ssDoc->si as $si) {
                $strings[] = (string) $si->t;
            }
        }

        $rows = [];
        if ($sheetXml) {
            $doc = simplexml_load_string($sheetXml);
            foreach ($doc->sheetData->row as $row) {
                $cells = [];
                foreach ($row->c as $cell) {
                    $type  = (string) $cell['t'];
                    $value = (string) $cell->v;
                    if ($type === 's') $value = $strings[(int)$value] ?? '';
                    $cells[] = $value;
                }
                $rows[] = $cells;
            }
        }

        return $rows;
    }
}
