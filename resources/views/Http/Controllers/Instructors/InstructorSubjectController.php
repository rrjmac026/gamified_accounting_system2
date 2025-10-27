<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

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
            $subjectsQuery->where(function($query) use ($search) {
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
}
