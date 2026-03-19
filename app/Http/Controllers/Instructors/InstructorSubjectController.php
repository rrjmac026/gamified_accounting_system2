<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InstructorSubjectController extends Controller
{
    // List all subjects assigned to this instructor
    public function index(Request $request)
    {
        $instructor = auth()->user()->instructor;

        // Get subjects query
        $subjectsQuery = $instructor->subjects();

        // Apply search if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $subjectsQuery->where(function ($query) use ($search) {
                $query->where('subject_code', 'LIKE', "%{$search}%")
                      ->orWhere('subject_name', 'LIKE', "%{$search}%");
            });
        }

        // Get the subjects with tasks and sections
        $subjects = $subjectsQuery->with('tasks', 'sections')->paginate(9);

        return view('instructors.subjects.index', compact('subjects'));
    }

    // Show details of a single subject
    public function show($subjectId)
    {
        $instructor = auth()->user()->instructor;

        // Only allow access if this subject belongs to this instructor
        $subject = $instructor->subjects()->with('tasks', 'sections.students')->findOrFail($subjectId);

        return view('instructors.subjects.show', compact('subject'));
    }

    /**
     * Download a blank CSV import template for enrolling students into a subject.
     */
    public function downloadImportTemplate(Subject $subject)
    {
        // Ensure this subject belongs to the authenticated instructor
        $instructor = auth()->user()->instructor;
        $instructor->subjects()->findOrFail($subject->id);

        $filename = 'student_import_template_' . $subject->subject_code . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($subject) {
            $handle = fopen('php://output', 'w');

            // Instructions row
            fputcsv($handle, ['# Instructions: Fill in student_number column. Do not edit the header row.']);
            fputcsv($handle, ['# Subject: ' . $subject->subject_code . ' - ' . $subject->subject_name]);
            fputcsv($handle, ['']);

            // Header row
            fputcsv($handle, ['student_number']);

            // Example rows
            fputcsv($handle, ['2021-00001']);
            fputcsv($handle, ['2021-00002']);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Process an uploaded CSV and enroll students into the subject.
     */
    public function importStudents(Request $request, Subject $subject)
    {
        // Ensure this subject belongs to the authenticated instructor
        $instructor = auth()->user()->instructor;
        $instructor->subjects()->findOrFail($subject->id);

        $request->validate([
            'import_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file   = $request->file('import_file');
        $handle = fopen($file->getRealPath(), 'r');

        $enrolled   = 0;
        $skipped    = [];
        $notFound   = [];
        $alreadyIn  = [];
        $rowNumber  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Skip comment / blank rows
            $first = trim($row[0] ?? '');
            if (empty($first) || str_starts_with($first, '#') || strtolower($first) === 'student_number') {
                continue;
            }

            $studentNumber = $first;

            // Look up the student by student_number
            $student = Student::where('student_number', $studentNumber)->first();

            if (!$student) {
                $notFound[] = $studentNumber;
                continue;
            }

            // Check if already enrolled
            $alreadyEnrolled = DB::table('student_subjects')
                ->where('student_id', $student->id)
                ->where('subject_id', $subject->id)
                ->exists();

            if ($alreadyEnrolled) {
                $alreadyIn[] = $studentNumber;
                continue;
            }

            // Enroll the student
            DB::table('student_subjects')->insert([
                'student_id'       => $student->id,
                'subject_id'       => $subject->id,
                'enrollment_date'  => now(),
                'status'           => 'active',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            $enrolled++;
        }

        fclose($handle);

        // Build a descriptive flash message
        $message = "{$enrolled} student(s) successfully enrolled.";

        if (count($alreadyIn)) {
            $message .= ' ' . count($alreadyIn) . ' were already enrolled and skipped.';
        }
        if (count($notFound)) {
            $message .= ' ' . count($notFound) . ' student number(s) not found: ' . implode(', ', $notFound) . '.';
        }

        $type = ($enrolled > 0) ? 'success' : 'warning';

        return redirect()
            ->route('instructors.subjects.show', $subject->id)
            ->with($type, $message);
    }
}