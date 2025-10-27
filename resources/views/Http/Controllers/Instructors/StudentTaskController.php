<?php

namespace App\Http\Controllers\Instructors;

use App\Http\Controllers\Controller;
use App\Models\StudentTask;
use App\Models\Student;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentTaskController extends Controller
{
    public function index()
    {
        $studentTasks = StudentTask::with(['student.user', 'task.subject'])->paginate(10);
        return view('instructors.student-tasks.index', compact('studentTasks'));
    }

    public function create()
    {
        $instructor = Auth::user()->instructor;
        $tasks = Task::with('subject')->where('instructor_id', $instructor->id)->get();
        
        // Get only students from instructor's sections
        $students = Student::whereHas('sections', function($query) use ($instructor) {
            $query->whereHas('instructors', function($q) use ($instructor) {
                $q->where('instructors.id', $instructor->id);
            });
        })->with('user')->get();

        return view('instructors.student-tasks.create', compact('students', 'tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'task_id' => 'required|exists:tasks,id',
            'status' => 'required|in:assigned,in_progress,submitted,graded,overdue',
            'score' => 'nullable|numeric|min:0',
            'xp_earned' => 'nullable|integer|min:0',
            'submitted_at' => 'nullable|date',
            'graded_at' => 'nullable|date'
        ]);

        StudentTask::create($validated);
        return redirect()->route('instructors.student-tasks.index')
            ->with('success', 'Task assigned to student successfully');
    }

    public function show(StudentTask $studentTask)
    {
        $studentTask->load(['student.user', 'task.subject']);
        return view('instructors.student-tasks.show', compact('studentTask'));
    }

    public function edit(StudentTask $studentTask)
    {
        $students = Student::with('user')->get();
        $tasks = Task::with('subject')->get();
        return view('instructors.student-tasks.edit', compact('studentTask', 'students', 'tasks'));
    }

    public function update(Request $request, StudentTask $studentTask)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'task_id' => 'required|exists:tasks,id',
            'status' => 'required|in:assigned,in_progress,submitted,graded,overdue',
            'score' => 'nullable|numeric|min:0',
            'xp_earned' => 'nullable|integer|min:0',
            'submitted_at' => 'nullable|date',
            'graded_at' => 'nullable|date'
        ]);

        $studentTask->update($validated);
        return redirect()->route('instructors.student-tasks.show', $studentTask)
            ->with('success', 'Student task updated successfully');
    }

    public function destroy(StudentTask $studentTask)
    {
        $studentTask->delete();
        return redirect()->route('instructors.student-tasks.index')
            ->with('success', 'Student task deleted successfully');
    }

    public function grade(StudentTask $studentTask)
    {
        return view('instructors.student-tasks.grade', compact('studentTask'));
    }

    public function submitGrade(Request $request, StudentTask $studentTask)
    {
        $validated = $request->validate([
            'score' => 'required|numeric|min:0',
            'xp_earned' => 'required|integer|min:0'
        ]);

        $studentTask->update([
            'score' => $validated['score'],
            'xp_earned' => $validated['xp_earned'],
            'status' => 'graded',
            'graded_at' => now()
        ]);

        return redirect()->route('instructors.student-tasks.show', $studentTask)
            ->with('success', 'Task graded successfully');
    }

    // CSV Upload Methods
    public function csvUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'handle_missing_students' => 'nullable|in:skip,create' // Add this option
        ]);

        try {
            $path = $request->file('csv_file')->getRealPath();
            $csvData = $this->parseCsv($path);
            
            $validatedTasks = $this->validateCsvData($csvData);
            $createdCount = $this->createTasksFromCsv($validatedTasks);

            $successMessage = "Successfully uploaded and created {$createdCount} student task assignments!";
            
            // Add information about warnings and created students
            $warnings = session('csv_warnings', []);
            $createdStudents = session('csv_created_students', []);
            
            if (!empty($createdStudents)) {
                $successMessage .= "\n\nNew student accounts created: " . count($createdStudents);
            }
            
            if (!empty($warnings)) {
                session(['upload_warnings' => $warnings]);
            }

            return redirect()->route('instructors.student-tasks.index')
                ->with('success', $successMessage)
                ->with('warnings', $warnings);

        } catch (\Exception $e) {
            return back()->withErrors(['csv_file' => 'Error processing CSV: ' . $e->getMessage()]);
        }
    }

    public function downloadCsvTemplate()
    {
        $filename = 'student_tasks_template.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'student_email',
                'task_id',
                'status',
                'score',
                'xp_earned',
                'submitted_at',
                'graded_at'
            ]);

            // Sample data
            fputcsv($file, [
                'student@example.com',
                '1',
                'assigned',
                '',
                '',
                '',
                ''
            ]);

            fputcsv($file, [
                'student2@example.com',
                '2',
                'submitted',
                '85',
                '50',
                '2024-03-15 14:30:00',
                ''
            ]);

            fputcsv($file, [
                'student3@example.com',
                '1',
                'graded',
                '92',
                '75',
                '2024-03-14 16:45:00',
                '2024-03-16 09:20:00'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function parseCsv($filePath)
    {
        $csvData = [];
        $header = null;

        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$header) {
                    $header = array_map('trim', $row);
                } else {
                    $csvData[] = array_combine($header, array_map('trim', $row));
                }
            }
            fclose($handle);
        }

        return $csvData;
    }

    private function validateCsvData($csvData)
    {
        $validatedTasks = [];
        $errors = [];
        $warnings = [];
        $createdStudents = [];

        foreach ($csvData as $index => $row) {
            $rowNumber = $index + 2; // +2 because index starts at 0 and we skip header

            // Find student by email
            $student = Student::whereHas('user', function($query) use ($row) {
                $query->where('email', $row['student_email']);
            })->first();

            // Handle missing student based on configuration
            if (!$student) {
                $student = $this->handleMissingStudent($row['student_email'], $rowNumber, $errors, $warnings, $createdStudents);
                if (!$student) {
                    continue; // Skip this row if student couldn't be created/found
                }
            }

            // Validate task exists
            $task = Task::find($row['task_id']);
            if (!$task) {
                $errors[] = "Row {$rowNumber}: Task with ID '{$row['task_id']}' not found";
                continue;
            }

            // Check for duplicate assignment
            $existingTask = StudentTask::where('student_id', $student->id)
                                      ->where('task_id', $task->id)
                                      ->first();
            
            if ($existingTask) {
                $errors[] = "Row {$rowNumber}: Task '{$task->title}' is already assigned to '{$row['student_email']}'";
                continue;
            }

            // Validate the row data
            $validator = Validator::make($row, [
                'student_email' => 'required|email',
                'task_id' => 'required|integer',
                'status' => 'required|in:assigned,in_progress,submitted,graded,overdue',
                'score' => 'nullable|numeric|min:0',
                'xp_earned' => 'nullable|integer|min:0',
                'submitted_at' => 'nullable|date',
                'graded_at' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                $errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                continue;
            }

            $validatedTasks[] = [
                'student_id' => $student->id,
                'task_id' => $task->id,
                'status' => $row['status'],
                'score' => !empty($row['score']) ? (float) $row['score'] : null,
                'xp_earned' => !empty($row['xp_earned']) ? (int) $row['xp_earned'] : 0,
                'submitted_at' => !empty($row['submitted_at']) ? $row['submitted_at'] : null,
                'graded_at' => !empty($row['graded_at']) ? $row['graded_at'] : null,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        if (!empty($errors)) {
            $errorMessage = "CSV validation failed:\n" . implode("\n", $errors);
            if (!empty($warnings)) {
                $errorMessage .= "\n\nWarnings:\n" . implode("\n", $warnings);
            }
            if (!empty($createdStudents)) {
                $errorMessage .= "\n\nNote: Created new student accounts for: " . implode(", ", $createdStudents);
            }
            throw new \Exception($errorMessage);
        }

        // Store any warnings in session to show to user
        if (!empty($warnings)) {
            session(['csv_warnings' => $warnings]);
        }
        if (!empty($createdStudents)) {
            session(['csv_created_students' => $createdStudents]);
        }

        return $validatedTasks;
    }

    private function handleMissingStudent($email, $rowNumber, &$errors, &$warnings, &$createdStudents)
    {
        // Option 1: Skip with error (current behavior)
        // $errors[] = "Row {$rowNumber}: Student with email '{$email}' not found";
        // return null;

        // Option 2: Auto-create student account
        return $this->autoCreateStudent($email, $rowNumber, $warnings, $createdStudents);

        // Option 3: Create pending student (you could implement this)
        // return $this->createPendingStudent($email, $rowNumber, $warnings);
    }

    private function autoCreateStudent($email, $rowNumber, &$warnings, &$createdStudents)
    {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null; // Skip invalid emails
        }

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            // User exists but no student record - create student record
            if (!$existingUser->student) {
                $student = Student::create([
                    'user_id' => $existingUser->id,
                    'student_id' => $this->generateStudentId(),
                    'enrollment_date' => now(),
                    'status' => 'active'
                ]);
                
                $warnings[] = "Row {$rowNumber}: Created student record for existing user '{$email}'";
                $createdStudents[] = $email;
                return $student;
            }
            return $existingUser->student;
        }

        try {
            // Create new user and student
            DB::beginTransaction();

            $user = User::create([
                'name' => $this->extractNameFromEmail($email),
                'email' => $email,
                'password' => bcrypt($this->generateTemporaryPassword()),
                'email_verified_at' => null // They'll need to verify
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'enrollment_date' => now(),
                'status' => 'pending' // Mark as pending until they verify
            ]);

            DB::commit();

            $warnings[] = "Row {$rowNumber}: Created new student account for '{$email}' - they will need to verify their email and set password";
            $createdStudents[] = $email;

            // Optional: Send invitation email
            // $this->sendStudentInvitation($user, $temporaryPassword);

            return $student;

        } catch (\Exception $e) {
            DB::rollBack();
            $warnings[] = "Row {$rowNumber}: Failed to create student account for '{$email}': " . $e->getMessage();
            return null;
        }
    }

    private function extractNameFromEmail($email)
    {
        // Extract name from email (e.g., john.doe@example.com -> John Doe)
        $localPart = explode('@', $email)[0];
        $nameParts = explode('.', $localPart);
        return ucwords(implode(' ', $nameParts));
    }

    // Remove the generateStudentId method since it's not needed
    // private function generateStudentId() { ... }

    private function generateTemporaryPassword()
    {
        // Generate secure temporary password
        return 'TempPass' . rand(1000, 9999) . '!';
    }

    // Optional: Send invitation email method
    private function sendStudentInvitation($user, $temporaryPassword)
    {
        // Implementation for sending invitation email
        // You can use Laravel's Mail facade here
        /*
        Mail::to($user->email)->send(new StudentInvitation($user, $temporaryPassword));
        */
    }
    

    private function createTasksFromCsv($validatedTasks)
    {
        $createdCount = 0;
        
        DB::transaction(function () use ($validatedTasks, &$createdCount) {
            foreach ($validatedTasks as $taskData) {
                StudentTask::create($taskData);
                $createdCount++;
            }
        });

        return $createdCount;
    }
}