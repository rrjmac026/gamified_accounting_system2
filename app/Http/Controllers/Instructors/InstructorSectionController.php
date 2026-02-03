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
}