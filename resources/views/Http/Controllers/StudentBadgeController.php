<?php

namespace App\Http\Controllers;

use App\Models\StudentBadge;
use App\Models\Student;
use App\Models\Badge;
use Illuminate\Http\Request;

class StudentBadgeController extends Controller
{
    public function index()
    {
        $studentBadges = StudentBadge::with(['student', 'badge'])->get();
        return view('student-badges.index', compact('studentBadges'));
    }

    public function create()
    {
        $students = Student::all();
        $badges = Badge::all();
        return view('student-badges.create', compact('students', 'badges'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'badge_id' => 'required|exists:badges,id',
            'earned_at' => 'required|date'
        ]);

        StudentBadge::create($validated);
        return redirect()->route('student-badges.index')->with('success', 'Student badge assigned successfully');
    }

    public function show(StudentBadge $studentBadge)
    {
        return view('student-badges.show', compact('studentBadge'));
    }

    public function edit(StudentBadge $studentBadge)
    {
        $students = Student::all();
        $badges = Badge::all();
        return view('student-badges.edit', compact('studentBadge', 'students', 'badges'));
    }

    public function update(Request $request, StudentBadge $studentBadge)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'badge_id' => 'required|exists:badges,id',
            'earned_at' => 'required|date'
        ]);

        $studentBadge->update($validated);
        return redirect()->route('student-badges.index')->with('success', 'Student badge updated successfully');
    }

    public function destroy(StudentBadge $studentBadge)
    {
        $studentBadge->delete();
        return redirect()->route('student-badges.index')->with('success', 'Student badge removed successfully');
    }
}
