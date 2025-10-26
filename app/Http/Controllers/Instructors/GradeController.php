<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    /**
     * Display all grades.
     */
    public function index()
    {
        $grades = Grade::with(['student', 'subject'])->get();
        return view('instructors.grades.index', compact('grades'));
    }

    /**
     * Show form to create a new grade.
     */
    public function create()
    {
        $students = Student::all();
        $subjects = Subject::all();
        return view('instructors.grades.create', compact('students', 'subjects'));
    }

    /**
     * Store a new grade (manual entry).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'final_grade' => 'required|numeric|between:1,5',
        ]);

        // Manual entry flag
        $validated['is_manual'] = true;

        Grade::create($validated);

        return redirect()->route('instructors.grades.index')
            ->with('success', 'Grade added successfully.');
    }

    /**
     * Show a specific grade.
     */
    public function show(Grade $grade)
    {
        return view('instructors.grades.show', compact('grade'));
    }

    /**
     * Show form to edit a grade.
     */
    public function edit(Grade $grade)
    {
        $students = Student::all();
        $subjects = Subject::all();
        return view('instructors.grades.edit', compact('grade', 'students', 'subjects'));
    }

    /**
     * Update a grade (manual override).
     */
    public function update(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'final_grade' => 'required|numeric|between:1,5',
        ]);

        $grade->update([
            'final_grade' => $validated['final_grade'],
            'is_manual' => true, // mark as manual override
        ]);

        return redirect()->route('instructors.grades.show', $grade)
            ->with('success', 'Grade updated successfully.');
    }

    /**
     * Delete a grade record.
     */
    public function destroy(Grade $grade)
    {
        $grade->delete();
        return redirect()->route('instructors.grades.index')
            ->with('success', 'Grade deleted successfully.');
    }
}
