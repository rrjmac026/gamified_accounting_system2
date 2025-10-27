<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class StudentSubjectController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;

        // Get subjects the student is enrolled in
        $subjects = $student->subjects()
            ->with(['instructors.user']) // eager load instructors if you want
            ->get();

        return view('students.subjects.index', compact('subjects'));
    }

    public function show($id)
    {
        $student = Auth::user()->student;

        $subject = $student->subjects()
            ->with(['instructors.user', 'performanceTasks']) // Make sure this is 'performanceTasks'
            ->findOrFail($id);

        return view('students.subjects.show', compact('subject'));
    }
}