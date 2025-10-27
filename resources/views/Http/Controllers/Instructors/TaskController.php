<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Subject;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\User;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Models\PerformanceTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\SystemNotification;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // Get task parameter from route
            $taskId = $request->route('task');
            $task = null;
            
            // Get task instance if taskId exists
            if ($taskId) {
                if ($taskId instanceof Task) {
                    $task = $taskId;
                } else {
                    $task = Task::find($taskId);
                }

                // If task exists, check ownership
                if ($task) {
                    $currentInstructorId = Auth::user()->instructor->id;
                    if ($task->instructor_id !== $currentInstructorId) {
                        abort(403, 'Unauthorized access to this task');
                    }
                }
            }
            
            return $next($request);
        })->except(['create', 'store']);
    }

    public function index()
    {
        $instructorId = Auth::user()->instructor->id;
        
        
        $tasks = Task::with(['subject', 'instructor', 'section', 'submissions', 'students'])
            ->where('instructor_id', $instructorId)
            ->where('parent_task_id', null)
            ->get()
            ->map(function ($task) {
                $task->task_type = 'regular';
                return $task;
            });
        
        // Fetch performance tasks with necessary relationships
        $performanceTasks = PerformanceTask::with(['subject', 'instructor', 'section', 'students'])
            ->where('instructor_id', $instructorId)
            ->get()
            ->map(function ($task) {
                $task->task_type = 'performance';
                
                $task->submissions = collect();
                return $task;
            });
        
        // Merge both collections and sort by newest first
        $allTasks = $tasks->concat($performanceTasks)
            ->sortByDesc('created_at');
        
        return view('instructors.tasks.index', compact('allTasks'));
    }

    public function create()
    {
        // Get the currently authenticated instructor
        $instructor = Auth::user()->instructor;
        
        // Get only the subjects assigned to this instructor
        $subjects = $instructor->subjects()->with('sections')->get();
        
        // Get sections where this instructor is assigned
        $sections = $instructor->sections;

        return view('instructors.tasks.create', compact('subjects', 'sections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:assignment,exercise,quiz,project',
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
            'retry_limit' => 'required|integer|min:1',
            'late_penalty' => 'nullable|integer|min:0',
            'max_score' => 'required|integer|min:0',
            'xp_reward' => 'required|integer|min:0',
            'due_date' => 'nullable|date', 
            'instructions' => 'required|string',
            'is_active' => 'required|boolean',
            'auto_grade' => 'required|boolean',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,png|max:10240',
            'late_until' => 'nullable|date|after:due_date',
        ]);

        // Custom validation: if allow_late_submission is true, due_date must be provided
        if ($request->has('allow_late_submission') && $request->boolean('allow_late_submission')) {
            if (empty($validated['due_date'])) {
                return back()->withErrors([
                    'due_date' => 'Due date is required when allowing late submissions.'
                ])->withInput();
            }
        }

        // Custom validation: late_until requires due_date
        if (!empty($validated['late_until']) && empty($validated['due_date'])) {
            return back()->withErrors([
                'late_until' => 'Late submission deadline requires a due date to be set.'
            ])->withInput();
        }

        $validated['allow_late_submission'] = $request->has('allow_late_submission');
        $validated['instructor_id'] = Auth::user()->instructor->id;

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('task_attachments', 'public');
            $validated['attachment'] = $path;
        }

        // Create task
        $task = Task::create($validated);

        // Auto-assign to section students
        $section = \App\Models\Section::with('students')->findOrFail($validated['section_id']);
        $attachData = [];
        if ($section->students && $section->students->count() > 0) {
            foreach ($section->students as $student) {
                $attachData[$student->id] = [
                    'status' => 'assigned',
                    // Only set due_date if task has one
                    'due_date' => $validated['due_date'] ? \Carbon\Carbon::parse($validated['due_date'])->format('Y-m-d H:i:s') : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $task->students()->attach($attachData);
            // 🔔 Notify all students in the section
            foreach ($section->students as $student) {
                SystemNotification::create([
                    'user_id' => $student->user->id,
                    'title'   => 'New Task Assigned',
                    'message' => "A new task '{$task->title}' has been assigned to your section.",
                    'type'    => 'info',
                    'is_read' => false,
                ]);
            }
        }

        

        return redirect()->route('instructors.tasks.index')
            ->with('success', 'Task created (with attachment if uploaded) and assigned to students.');
    }

    public function show(Task $task)
    {
        $task->load([
            'subject',
            'section',
            'instructor.user',
            'submissions',
            'questions',
            'students.user'
        ]);
        
        return view('instructors.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $instructor = Auth::user()->instructor;
        $subjects = $instructor->subjects;
        $sections = $instructor->sections;
        return view('instructors.tasks.edit', compact('task', 'subjects','sections'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:assignment,exercise,quiz,project',
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
            'retry_limit' => 'required|integer|min:1',
            'late_penalty' => 'nullable|integer|min:0',
            'max_score' => 'required|integer|min:0',
            'xp_reward' => 'required|integer|min:0',
            'due_date' => 'nullable|date', 
            'instructions' => 'required|string',
            'is_active' => 'required|boolean',
            'auto_grade' => 'required|boolean',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,png|max:10240',
            'late_until' => 'nullable|date|after:due_date',
        ]);

        // Custom validation: if allow_late_submission is true, due_date must be provided
        if ($request->boolean('allow_late_submission') && empty($validated['due_date'])) {
            return back()->withErrors([
                'due_date' => 'Due date is required when allowing late submissions.'
            ])->withInput();
        }

        // Custom validation: late_until requires due_date
        if (!empty($validated['late_until']) && empty($validated['due_date'])) {
            return back()->withErrors([
                'late_until' => 'Late submission deadline requires a due date to be set.'
            ])->withInput();
        }

        $validated['allow_late_submission'] = $request->boolean('allow_late_submission');

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($task->attachment && \Storage::disk('public')->exists($task->attachment)) {
                \Storage::disk('public')->delete($task->attachment);
            }

            // Save new file
            $path = $request->file('attachment')->store('task_attachments', 'public');
            $validated['attachment'] = $path;
        }

        // If user wants to remove the file (optional)
        if ($request->has('remove_attachment') && $request->remove_attachment == '1') {
            if ($task->attachment && \Storage::disk('public')->exists($task->attachment)) {
                \Storage::disk('public')->delete($task->attachment);
            }
            $validated['attachment'] = null;
        }

        $task->update($validated);

        foreach ($task->section->students as $student) {
        SystemNotification::create([
            'user_id' => $student->user->id,
            'title'   => 'Task Updated',
            'message' => "The task '{$task->title}' has been updated. Check for new instructions or deadlines.",
            'type'    => 'warning',
            'is_read' => false,
        ]);
    }

        return redirect()->route('instructors.tasks.index')
            ->with('success', 'Task updated successfully');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('instructors.tasks.index')
            ->with('success', 'Task deleted successfully');
    }

    public function showAssignStudentsForm(Task $task)
    {
        $students = Student::all(); // Or filter by course/subject
        return view('instructors.tasks.assign-students', compact('task', 'students'));
    }
    
    public function syncStudentsToTask(Task $task)
    {
        // Get all students currently in the task's section
        $sectionStudentIds = $task->section->students->pluck('id')->toArray();
        
        // Get students currently assigned to this task
        $assignedStudentIds = $task->students()->pluck('student_id')->toArray();
        
        // Find students who need to be assigned (in section but not assigned to task)
        $studentsToAssign = array_diff($sectionStudentIds, $assignedStudentIds);
        
        // Find students who need to be removed (assigned to task but not in section)
        $studentsToRemove = array_diff($assignedStudentIds, $sectionStudentIds);
        
        $changes = 0;
        
        // Assign new students
        if (!empty($studentsToAssign)) {
            $attachData = [];
            foreach ($studentsToAssign as $studentId) {
                $attachData[$studentId] = [
                    'status' => 'assigned',
                    'due_date' => $task->due_date, // This will be null if task has no due date
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            $task->students()->attach($attachData);
            $changes += count($studentsToAssign);
        }
        
        // Remove students who are no longer in the section
        if (!empty($studentsToRemove)) {
            $task->students()->detach($studentsToRemove);
            $changes += count($studentsToRemove);
        }
        
        if ($changes > 0) {
            $message = '';
            if (!empty($studentsToAssign)) {
                $message .= 'Assigned task to ' . count($studentsToAssign) . ' new students. ';
            }
            if (!empty($studentsToRemove)) {
                $message .= 'Removed ' . count($studentsToRemove) . ' students who are no longer in this section.';
            }
            
            return redirect()->back()->with('success', trim($message));
        }
        
        return redirect()->back()->with('info', 'All students are already properly assigned.');
    }

    public function syncAllStudentsToTasks()
    {
        $instructorId = Auth::user()->instructor->id;
        $tasks = Task::where('instructor_id', $instructorId)
                    ->where('is_active', true)
                    ->with('section.students', 'students')
                    ->get();
        
        $totalAssigned = 0;
        $totalRemoved = 0;
        
        foreach ($tasks as $task) {
            // Get all students currently in the task's section
            $sectionStudentIds = $task->section->students->pluck('id')->toArray();
            
            // Get students currently assigned to this task
            $assignedStudentIds = $task->students()->pluck('student_id')->toArray();
            
            // Find students who need to be assigned
            $studentsToAssign = array_diff($sectionStudentIds, $assignedStudentIds);
            
            // Find students who need to be removed
            $studentsToRemove = array_diff($assignedStudentIds, $sectionStudentIds);
            
            // Assign new students
            if (!empty($studentsToAssign)) {
                $attachData = [];
                foreach ($studentsToAssign as $studentId) {
                    $attachData[$studentId] = [
                        'status' => 'assigned',
                        'due_date' => $task->due_date, // This will be null if task has no due date
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                $task->students()->attach($attachData);
                $totalAssigned += count($studentsToAssign);
            }
            
            // Remove students who are no longer in the section
            if (!empty($studentsToRemove)) {
                $task->students()->detach($studentsToRemove);
                $totalRemoved += count($studentsToRemove);
            }
        }
        
        $message = '';
        if ($totalAssigned > 0) {
            $message .= "Assigned tasks to {$totalAssigned} students. ";
        }
        if ($totalRemoved > 0) {
            $message .= "Removed {$totalRemoved} students from tasks they're no longer eligible for.";
        }
        
        if ($totalAssigned > 0 || $totalRemoved > 0) {
            return redirect()->route('instructors.tasks.index')
                ->with('success', trim($message));
        } else {
            return redirect()->route('instructors.tasks.index')
                ->with('info', 'All students are already properly assigned to their section tasks.');
        }
    }
}