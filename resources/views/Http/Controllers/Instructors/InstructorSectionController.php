<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\Section;
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
}
