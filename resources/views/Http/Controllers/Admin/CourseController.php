<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Traits\Loggable;

class CourseController extends Controller
{
    use Loggable;
    /**
     * Display a listing of courses.
     */
    public function index(Request $request)
    {
        $query = Course::with('students'); // move `with` here so it works with search too

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                ->orWhere('course_name', 'like', "%{$search}%")
                ->orWhere('department', 'like', "%{$search}%");
            });
        }

        $courses = $query->paginate(15);

        return view('admin.courses.index', compact('courses'));
    }


    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_code' => 'required|string|max:20|unique:courses,course_code',
            'course_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department' => 'required|string|max:255',
            'duration_years' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean'
        ]);

        $course = Course::create($validated);

        $this->logActivity(
            "Created Course",  // More descriptive action
            "Course",
            $course->id,
            [
                'course_code' => $course->course_code,
                'course_name' => $course->course_name,
                'department' => $course->department
            ]
        );

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Course created successfully');
    }

    /**
     * Display the specified course.
     */
    public function show($id)
    {
        $course = Course::with('students')->find($id);

        if (!$course) {
            return redirect()->route('admin.courses.index')
                            ->with('error', 'Course not found');
        }

        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return redirect()->route('admin.courses.index')
                            ->with('error', 'Course not found');
        }

        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'course_code' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('courses', 'course_code')->ignore($course->id)
            ],
            'course_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'department' => 'sometimes|required|string|max:255',
            'duration_years' => 'sometimes|required|integer|min:1|max:10',
            'is_active' => 'boolean'
        ]);

        $originalData = $course->toArray();
        $course->update($validated);

        $this->logActivity(
            "Updated Course",  // More descriptive action
            "Course",
            $course->id,
            [
                'original' => $originalData,
                'changes' => $course->getChanges()
            ]
        );

        return redirect()->route('admin.courses.show', $course->id)
                        ->with('success', 'Course updated successfully');
    }

    /**
     * Remove the specified course.
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        if ($course->students()->exists()) {
            return redirect()->route('admin.courses.show', $course->id)
                            ->with('error', 'Cannot delete course. It has associated students.');
        }

        $courseData = $course->toArray();
        $course->delete();

        $this->logActivity(
            "Deleted Course",  // More descriptive action
            "Course",
            $id,
            [
                'course_code' => $courseData['course_code'],
                'course_name' => $courseData['course_name']
            ]
        );

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Course deleted successfully');
    }

    /**
     * Get active courses only.
     */
    public function getActiveCourses()
    {
        $courses = Course::where('is_active', true)
                        ->with('students')
                        ->paginate(15);

        return view('admin.courses.active', compact('courses'));
    }

    /**
     * Toggle course active status.
     */
    public function toggleStatus($id)
    {
        $course = Course::findOrFail($id);
        $originalStatus = $course->is_active;
        
        $course->update(['is_active' => !$course->is_active]);

        $this->logActivity(
            $course->is_active ? "Activated Course" : "Deactivated Course",  // More descriptive action
            "Course",
            $course->id,
            [
                'previous_status' => $originalStatus,
                'new_status' => $course->is_active
            ]
        );

        $status = $course->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.courses.show', $course->id)
                        ->with('success', "Course {$status} successfully");
    }
}
