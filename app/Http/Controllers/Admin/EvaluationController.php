<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EvaluationController extends Controller
{
    public function __construct()
    {
        // Students can only create/store (and optionally view their own)
        $this->middleware('role:student')->only(['create', 'store', 'myEvaluations']);

        // Admins/Instructors can list, view, delete
        $this->middleware('role:admin,instructor')->only(['index', 'show', 'destroy']);
    }

    /**
     * Show all evaluations (Admin/Instructor only)
     */
    public function index()
    {
        $evaluations = Evaluation::with(['student', 'instructor', 'course'])
            ->latest('submitted_at')
            ->paginate(15);

        return view('admin.evaluations.index', compact('evaluations'));
    }

    /**
     * Show create form (Student only)
     */
    public function create()
    {
        $student = Auth::user()->student;
        
        // Get all instructors (or filter by student's enrolled courses)
        $instructors = Instructor::with('user')->get();
        
        // Get all courses the student is enrolled in
        // Assuming you have a many-to-many relationship between students and courses
        $courses = $student->courses ?? collect();
        
        // If no courses found, get all available courses
        if ($courses->isEmpty()) {
            $courses = \App\Models\Course::all();
        }
        
        // Example criteria (you can fetch from DB instead)
        $criteria = [
            'teaching_effectiveness' => 'How effective was the instructor\'s teaching?',
            'subject_knowledge' => 'How knowledgeable was the instructor about the subject?',
            'communication_clarity' => 'How clear was the instructor\'s communication?',
            'student_engagement' => 'How well did the instructor engage students?',
            'grading_fairness' => 'How fair was the instructor\'s grading?',
            'learning_materials' => 'How effective were the learning materials used?',
            'availability_support' => 'How available was the instructor for support?',
            'overall_satisfaction' => 'Overall satisfaction with the course'
        ];

        return view('students.evaluations.create', compact('instructors', 'courses', 'criteria'));
    }


    /**
     * Store a new evaluation (Student only)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'instructor_id' => 'required|exists:instructors,id',
            'course_id' => 'required|exists:courses,id',
            'responses' => 'required|array',
            'responses.*' => 'required|integer|min:1|max:5',
            'comments' => 'required|string|min:10|max:1000',
        ], [
            'instructor_id.required' => 'Please select an instructor.',
            'instructor_id.exists' => 'The selected instructor is invalid.',
            'course_id.required' => 'Please select a course.',
            'course_id.exists' => 'The selected course is invalid.',
            'responses.required' => 'Please provide ratings for all criteria.',
            'responses.array' => 'Invalid response format.',
            'responses.*.required' => 'Please rate all criteria.',
            'responses.*.integer' => 'Ratings must be numbers.',
            'responses.*.min' => 'Ratings must be between 1 and 5.',
            'responses.*.max' => 'Ratings must be between 1 and 5.',
            'comments.required' => 'Please provide your comments.',
            'comments.min' => 'Comments must be at least 10 characters.',
            'comments.max' => 'Comments cannot exceed 1000 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Evaluation::create([
            'student_id'    => Auth::user()->student->id,
            'instructor_id' => $request->instructor_id,
            'course_id'     => $request->course_id,
            'responses'     => $request->responses,
            'comments'      => $request->comments,
            'submitted_at'  => now(),
        ]);

        return redirect()->route('students.evaluations.index')
            ->with('success', 'Your evaluation has been submitted.');
    }

    /**
     * Show a single evaluation (Admin/Instructor only)
     */
    public function show(Evaluation $evaluation)
    {
        $evaluation->load(['student', 'instructor', 'course']);
        return view('admin.evaluations.show', compact('evaluation'));
    }

    /**
     * Show logged-in student's evaluations (Student only)
     */
    public function myEvaluations()
    {
        $evaluations = Evaluation::where('student_id', Auth::user()->student->id)
            ->with(['instructor', 'course'])
            ->latest('submitted_at')
            ->paginate(10);

        return view('students.evaluations.index', compact('evaluations'));
    }

    /**
     * Delete evaluation (Admin only)
     */
    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();

        return redirect()->route('admin.evaluations.index')
            ->with('success', 'Evaluation deleted successfully.');
    }
}
