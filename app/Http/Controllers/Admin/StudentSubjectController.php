<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentSubject;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class StudentSubjectController extends Controller
{
    public function index()
    {
        $studentSubjects = StudentSubject::with(['student', 'subject'])->get();
        return view('student-subjects.index', compact('studentSubjects'));
    }

    public function create()
    {
        $students = Student::all();
        $subjects = Subject::all();
        return view('student-subjects.create', compact('students', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:enrolled,completed,dropped'
        ]);

        StudentSubject::create($validated);
        return redirect()->route('student-subjects.index')
            ->with('success', 'Student subject enrollment created successfully');
    }

    public function show(StudentSubject $studentSubject)
    {
        return view('student-subjects.show', compact('studentSubject'));
    }

    public function edit(StudentSubject $studentSubject)
    {
        $students = Student::all();
        $subjects = Subject::all();
        return view('student-subjects.edit', compact('studentSubject', 'students', 'subjects'));
    }

    public function update(Request $request, StudentSubject $studentSubject)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:enrolled,completed,dropped'
        ]);

        $studentSubject->update($validated);
        return redirect()->route('student-subjects.index')
            ->with('success', 'Student subject enrollment updated successfully');
    }

    public function destroy(StudentSubject $studentSubject)
    {
        $studentSubject->delete();
        return redirect()->route('student-subjects.index')
            ->with('success', 'Student subject enrollment deleted successfully');
    }
}
