<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;

class InstructorSectionController extends Controller
{
    // List sections assigned to the instructor
    public function index()
    {
        $instructor = auth()->user()->instructor;

        // Get sections directly assigned to this instructor along with students only
        $sections = $instructor->sections()->with('students')->get();

        return view('instructors.sections.index', compact('sections'));
    }

    // Show details for a single section
    public function show($sectionId)
    {
        $instructor = auth()->user()->instructor;

        // Load section with students only
        $section = $instructor->sections()->with('students')->findOrFail($sectionId);

        return view('instructors.sections.show', compact('section'));
    }

    // Show form to add students to a section
    public function manageStudents($sectionId)
    {
        $instructor = auth()->user()->instructor;
        
        // Verify instructor has access to this section
        $section = $instructor->sections()->findOrFail($sectionId);
        
        // Load current students in the section
        $section->load('students');
        
        // Get students not yet assigned to any section or already in this section
        $availableStudents = Student::whereDoesntHave('sections')
            ->orWhereHas('sections', function($query) use ($sectionId) {
                $query->where('sections.id', $sectionId);
            })
            ->with('user')
            ->get();

        return view('instructors.sections.manage-students', compact('section', 'availableStudents'));
    }

    // Update students in a section
    public function updateStudents(Request $request, $sectionId)
    {
        $instructor = auth()->user()->instructor;
        
        // Verify instructor has access to this section
        $section = $instructor->sections()->findOrFail($sectionId);

        $request->validate([
            'students' => 'array|nullable',
            'students.*' => 'exists:students,id',
        ]);

        // Check capacity if set
        if ($section->capacity && count($request->students ?? []) > $section->capacity) {
            return redirect()->back()
                ->withErrors(['students' => "Cannot add more than {$section->capacity} students to this section."])
                ->withInput();
        }

        // Sync students using the pivot table
        $section->students()->sync($request->students ?? []);

        return redirect()->route('instructors.sections.show', $section->id)
            ->with('success', 'Students updated successfully for this section.');
    }

    // Add a single student to the section
    public function addStudent(Request $request, $sectionId)
    {
        $instructor = auth()->user()->instructor;
        
        // Verify instructor has access to this section
        $section = $instructor->sections()->findOrFail($sectionId);

        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        // Check if student is already in a section
        $student = Student::findOrFail($request->student_id);
        if ($student->sections()->exists()) {
            return redirect()->back()
                ->withErrors(['student_id' => 'This student is already assigned to a section.'])
                ->withInput();
        }

        // Check capacity
        if ($section->capacity && $section->students()->count() >= $section->capacity) {
            return redirect()->back()
                ->withErrors(['student_id' => 'This section has reached its maximum capacity.'])
                ->withInput();
        }

        // Attach the student
        $section->students()->attach($request->student_id);

        return redirect()->route('instructors.sections.show', $section->id)
            ->with('success', 'Student added successfully to the section.');
    }

    // Remove a student from the section
    public function removeStudent($sectionId, $studentId)
    {
        $instructor = auth()->user()->instructor;
        
        // Verify instructor has access to this section
        $section = $instructor->sections()->findOrFail($sectionId);

        // Detach the student
        $section->students()->detach($studentId);

        return redirect()->route('instructors.sections.show', $section->id)
            ->with('success', 'Student removed successfully from the section.');
    }

        // Show import form
    public function importStudentsForm($sectionId)
    {
        $instructor = auth()->user()->instructor;
        $section = $instructor->sections()->findOrFail($sectionId);

        return view('instructors.sections.import-students', compact('section'));
    }

    // Handle CSV/Excel import
    public function importStudents(Request $request, $sectionId)
    {
        $instructor = auth()->user()->instructor;
        $section    = $instructor->sections()->findOrFail($sectionId);
    
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048',
        ]);
    
        $file      = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $rows      = [];
    
        if (in_array($extension, ['xlsx', 'xls'])) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
                $rows        = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
            } catch (\Exception $e) {
                return back()->with('error', 'Could not read the file: ' . $e->getMessage());
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
            return back()->with('error', 'No data found in the uploaded file.');
        }
    
        // ── Detect header row ─────────────────────────────────────────────────────
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
    
        // ── Process rows ──────────────────────────────────────────────────────────
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
                $student  = \App\Models\Student::where('student_number', $studentNumber)->first();
            }
            if (!$student && $email !== '') {
                $tried[] = $email;
                $student = \App\Models\Student::where('student_number', $email)
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
            ->route('instructors.sections.show', $section->id)
            ->with('import_success', [
                'imported' => $imported,
                'skipped'  => $skipped,
            ])
            ->with('import_errors', $errors);
    }
    public function downloadImportTemplate()
    {
        // Store this xlsx in storage/app/templates/students_import_template.xlsx
        $path = storage_path('app/templates/students_import_template.xlsx');

        return response()->download(
            $path,
            'students_import_template.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
}