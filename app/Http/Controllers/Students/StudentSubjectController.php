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
            ->with(['instructors.user'])
            ->get();

        return view('students.subjects.index', compact('subjects'));
    }

    public function show($id)
    {
        $student = Auth::user()->student;

        $subject = $student->subjects()
            ->with([
                'instructors.user',
                'performanceTasks' => function ($query) use ($student) {
                    $query->with(['students' => function ($q) use ($student) {
                        $q->where('students.id', $student->id);
                    }]);
                }
            ])
            ->findOrFail($id);

        return view('students.subjects.show', compact('subject'));
    }
}