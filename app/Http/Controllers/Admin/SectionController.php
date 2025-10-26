<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Instructor;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    // Display a list of sections
    public function index()
    {
        $sections = Section::with(['course', 'subjects'])->paginate(10); 
        return view('admin.sections.index', compact('sections'));
    }

    // Show form to create a new section
    public function create()
    {
        $courses = Course::where('is_active', true)->get();
        $instructors = \App\Models\Instructor::with(['user', 'subjects'])->get();
        $students = Student::doesntHave('sections')->get();
        return view('admin.sections.create', compact('courses', 'students', 'instructors'));
    }

    // Store a new section
    // Store a new section
    public function store(Request $request)
    {
        $request->validate([
            'section_code' => 'required|string|unique:sections,section_code',
            'name' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'capacity' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'instructors' => 'array|nullable',
            'instructors.*' => 'exists:instructors,id',
            'students' => 'array|nullable',
            'students.*' => 'exists:students,id',
        ]);

        $section = Section::create($request->only(['section_code', 'name', 'course_id', 'capacity', 'notes']));

        // Attach instructors using the pivot table
        if ($request->has('instructors')) {
            $section->instructors()->attach($request->instructors);
        }

        // Attach students using the pivot table
        if ($request->has('students')) {
            $section->students()->attach($request->students);
        }

        return redirect()->route('admin.sections.index')
                        ->with('success', 'Section created successfully.');
    }


    // Show details of a section (students & subjects)
    public function show(Section $section)
    {
        $section->load([
            'students.user', 
            'instructors.user',
            'instructors.subjects' // Load the subjects for each instructor
        ]);
        return view('admin.sections.show', compact('section'));
    }

    // Show form to edit a section
    public function edit(Section $section)
    {
        $courses = Course::where('is_active', true)->get();
        $instructors = Instructor::with('user')->get();
        $students = Student::all();
         $section->load(['instructors', 'students']);
        return view('admin.sections.edit', compact('section', 'instructors', 'courses', 'students'));
    }

    // Update a section
    public function update(Request $request, Section $section)
    {
        $request->validate([
            'section_code' => 'required|string|unique:sections,section_code,' . $section->id,
            'name' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'capacity' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'instructors' => 'array|nullable',
            'instructors.*' => 'exists:instructors,id',
            'students' => 'array|nullable',
            'students.*' => 'exists:students,id',
        ]);

        $section->update($request->only(['section_code', 'name', 'course_id', 'capacity', 'notes']));

        // Sync instructors using the pivot table
        $section->instructors()->sync($request->instructors ?? []);

        // Sync students using the pivot table
        $section->students()->sync($request->students ?? []);

        return redirect()->route('admin.sections.index')
                        ->with('success', 'Section updated successfully.');
    }


    // Delete a section
    public function destroy(Section $section)
    {
        $section->delete();

        return redirect()->route('admin.sections.index')
                         ->with('success', 'Section deleted successfully.');
    }


    // Show form to manage subjects for a section
    public function manageSubjects(Section $section)
    {
        $subjects = Subject::where('is_active', true)->get();
        $section->load('subjects');

        return view('admin.sections.manage-subjects', compact('section', 'subjects'));
    }

    // Save subjects for a section
    public function updateSubjects(Request $request, Section $section)
    {
        $request->validate([
            'subjects' => 'array|nullable',
            'subjects.*' => 'exists:subjects,id',
        ]);

        $section->subjects()->sync($request->subjects ?? []);

        return redirect()->route('admin.sections.index')
                        ->with('success', 'Subjects updated for this section.');
    }
}
