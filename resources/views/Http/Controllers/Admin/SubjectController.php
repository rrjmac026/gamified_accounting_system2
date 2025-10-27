<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; 

use App\Models\Subject;
use App\Models\Instructor;
use App\Traits\Loggable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    use Loggable;

    public function index()
    {
        $subjects = Subject::with('instructors.user')->paginate(10);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $instructors = Instructor::all();
        return view('admin.subjects.create', compact('instructors'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_code'   => 'required|string|unique:subjects,subject_code',
            'subject_name'   => 'required|string|max:255',
            'description'    => 'required|string',
            'instructor_ids' => 'required|array',
            'instructor_ids.*' => 'exists:instructors,id',
            'semester'       => 'required|string',
            'academic_year'  => 'required|string',
            'units'          => 'required|integer|min:1|max:6',
            'is_active'      => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $subject = Subject::create([
                'subject_code' => $request->subject_code,
                'subject_name' => $request->subject_name,
                'description' => $request->description,
                'semester' => $request->semester,
                'academic_year' => $request->academic_year,
                'units' => $request->units,
                'is_active' => $request->is_active
            ]);

            $subject->instructors()->attach($request->instructor_ids);

            DB::commit();

            $this->logActivity(
                "Created Subject",
                "Subject",
                $subject->id,
                [
                    'subject_code' => $subject->subject_code,
                    'subject_name' => $subject->subject_name
                ]
            );

            return redirect()->route('admin.subjects.index')
                ->with('success', 'Subject created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to create subject: ' . $e->getMessage());
        }
    }

    public function show(Subject $subject)
    {
        $subject->load(['instructors.user', 'students.user', 'students.course']);
        $allInstructors = Instructor::with('user')->get();
        
        return view('admin.subjects.show', compact('subject', 'allInstructors'));
    }

    public function edit(Subject $subject)
    {
        $instructors = Instructor::all();
        return view('admin.subjects.edit', compact('subject', 'instructors'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validator = Validator::make($request->all(), [
            'subject_code'   => ['required', 'string', Rule::unique('subjects')->ignore($subject->id)],
            'subject_name'   => 'required|string|max:255',
            'description'    => 'required|string',
            'instructor_ids' => 'required|array',
            'instructor_ids.*' => 'exists:instructors,id',
            'semester'       => 'required|string',
            'academic_year'  => 'required|string',
            'units'          => 'required|integer|min:1|max:6',
            'is_active'      => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            $originalData = $subject->toArray();

            $subject->update([
                'subject_code' => $request->subject_code,
                'subject_name' => $request->subject_name,
                'description' => $request->description,
                'semester' => $request->semester,
                'academic_year' => $request->academic_year,
                'units' => $request->units,
                'is_active' => $request->is_active
            ]);

            $subject->instructors()->sync($request->instructor_ids);

            DB::commit();

            $this->logActivity(
                "Updated Subject",
                "Subject",
                $subject->id,
                [
                    'original' => $originalData,
                    'changes' => $subject->getChanges()
                ]
            );

            return redirect()->route('admin.subjects.index')
                ->with('success', 'Subject updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to update subject: ' . $e->getMessage());
        }
    }

    public function destroy(Subject $subject)
    {
        $subjectData = $subject->toArray();
        $subject->delete();

        $this->logActivity(
            "Deleted Subject",
            "Subject",
            $subject->id,
            ['subject_data' => $subjectData]
        );

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully');
    }

    public function showAssignInstructorsForm(Subject $subject)
    {
        $allInstructors = Instructor::with(['user'])->get();
        return view('admin.subjects.assign-instructors', compact('subject', 'allInstructors'));
    }

    public function assignInstructors(Request $request, Subject $subject)
    {
        $request->validate([
            'instructors' => 'required|array',
            'instructors.*' => 'exists:instructors,id'
        ]);

        $subject->instructors()->sync($request->instructors);
        
        $this->logActivity(
            "Assigned Instructors to Subject",
            "Subject",
            $subject->id,
            [
                'subject_code' => $subject->subject_code,
                'instructor_ids' => $request->instructors
            ]
        );

        return redirect()->route('admin.subjects.show', $subject)
            ->with('success', 'Instructors assigned successfully.');
    }
}
