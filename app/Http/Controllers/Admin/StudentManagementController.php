<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\Loggable;
use App\Models\Student;
use App\Models\User;
use App\Models\Subject;
use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;


class StudentManagementController extends Controller
{
    use Loggable;

    public function index(Request $request)
    {
        $query = Student::with(['user', 'course', 'sections'])
            ->join('users', 'students.user_id', '=', 'users.id');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.first_name', 'like', "%{$search}%")
                ->orWhere('users.last_name', 'like', "%{$search}%")
                ->orWhere('users.email', 'like', "%{$search}%")
                ->orWhere('students.student_number', 'like', "%{$search}%");
            });
        }

        // Sort alphabetically by last name, then first name
        $query->orderBy('users.last_name', 'asc')
            ->orderBy('users.first_name', 'asc');

        // Select only students.* to avoid column conflicts
        $students = $query->select('students.*')->paginate(10);

        return view('admin.student.index', compact('students'));
    }

    public function create()
    {
        
        $subjects = Subject::all();
        $courses = Course::all();
        $sections = Section::all();
        return view('admin.student.create', compact('subjects', 'courses', 'sections'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'student_number' => 'required|string|unique:students,student_number',
            'course_id' => 'required|exists:courses,id',
            'year_level' => 'required|integer|min:1|max:5',
            'section_id' => 'nullable|exists:sections,id',
            'password' => 'required|string|min:8',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'is_active' => true
            ]);


            // Create student
            $student = Student::create([
                'user_id' => $user->id,
                'student_number' => $request->student_number,
                'course_id' => $request->course_id,
                'year_level' => $request->year_level,
                'section_id' => $request->section_id ?? null,
                'total_xp' => 0,
                'current_level' => 1,
                'performance_rating' => 0.00
            ]);

            // Attach section if provided (for many-to-many relationship)
            if ($request->section_id) {
                $student->sections()->attach($request->section_id);
            }

            // Attach subjects if provided
            if ($request->has('subjects') && !empty($request->subjects)) {
                $student->subjects()->attach($request->subjects);
            }

            DB::commit();

            $this->logActivity(
                "Created Student",
                "Student",
                $student->id,
                [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'student_number' => $student->student_number
                ]
            );

            return redirect()->route('admin.student.index')
                ->with('success', 'Student created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Student creation failed: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error creating student: ' . $e->getMessage());
        }
    }
    

    /**
     * Read CSV file natively without Laravel Excel package
     */
    private function readCsvFile($file)
    {
        $data = [];
        $path = $file->getRealPath();
        
        if (($handle = fopen($path, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }
        
        return $data;
    }

    /**
     * Read Excel file using Laravel Excel package
     */
    private function readExcelFile($file)
    {
        try {
            // Use the correct namespace - try different approaches
            if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
                return \Maatwebsite\Excel\Facades\Excel::toArray([], $file)[0];
            } elseif (class_exists('Excel')) {
                return \Excel::toArray([], $file)[0];
            } else {
                throw new \Exception('Laravel Excel package not properly installed');
            }
        } catch (\Exception $e) {
            Log::error('Excel reading error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if Laravel Excel package is available
     */
    private function isExcelPackageAvailable()
    {
        return class_exists('Maatwebsite\Excel\Facades\Excel') || 
               class_exists('Excel') || 
               class_exists('\Maatwebsite\Excel\Excel');
    }

    public function import(Request $request) 
    {
        // Validate file based on available packages
        $allowedMimes = ['csv'];
        if ($this->isExcelPackageAvailable()) {
            $allowedMimes = ['xlsx', 'xls', 'csv'];
        }

        $request->validate([
            'file' => 'required|mimes:' . implode(',', $allowedMimes) . '|max:2048'
        ]);

        try {
            // Start database transaction
            DB::beginTransaction();
            
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            
            Log::info('File upload details:', [
                'original_name' => $file->getClientOriginalName(),
                'extension' => $extension,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);
            
            // Handle different file types
            if (in_array(strtolower($extension), ['csv'])) {
                // Handle CSV files natively
                Log::info('Processing CSV file');
                $data = $this->readCsvFile($file);
            } elseif (in_array(strtolower($extension), ['xlsx', 'xls'])) {
                // Handle Excel files using Laravel Excel package
                Log::info('Processing Excel file');
                if ($this->isExcelPackageAvailable()) {
                    $data = $this->readExcelFile($file);
                } else {
                    throw new \Exception('Laravel Excel package is required for Excel files. Please install it with: composer require maatwebsite/excel OR convert your Excel file to CSV format.');
                }
            } else {
                throw new \Exception('Unsupported file type. Please upload a CSV' . ($this->isExcelPackageAvailable() ? ' or Excel' : '') . ' file.');
            }

            // Debug: Log the raw data structure
            Log::info('Import data structure:', [
                'total_rows' => count($data),
                'first_row' => isset($data[0]) ? $data[0] : 'No data',
                'second_row' => isset($data[1]) ? $data[1] : 'No second row'
            ]);
            
            if (empty($data)) {
                throw new \Exception('No data found in the uploaded file.');
            }
            
            $processedCount = 0;
            $skippedCount = 0;
            $errors = [];

            foreach ($data as $index => $row) {
                // Skip header row (row 0)
                if ($index === 0) {
                    Log::info('Header row:', ['header' => $row]);
                    continue;
                }

                // Debug: Log each row being processed
                Log::info("Processing row {$index}:", ['row' => $row]);

                // Handle empty rows
                if (empty(array_filter($row))) {
                    Log::info("Skipping empty row {$index}");
                    $skippedCount++;
                    continue;
                }

                // Extract data with better error handling - matching your CSV column order
                // CSV columns: first_name, last_name, email, course, year_level, section, password, id_number
                $first_name = isset($row[0]) ? trim($row[0]) : null;
                $last_name = isset($row[1]) ? trim($row[1]) : null;
                $email = isset($row[2]) ? trim($row[2]) : null;
                $course = isset($row[3]) ? trim($row[3]) : '';
                $year_level = isset($row[4]) ? (int)$row[4] : 1;
                $section = isset($row[5]) ? trim($row[5]) : '';
                $password = isset($row[6]) ? trim($row[6]) : 'password123';
                $id_number = isset($row[7]) ? trim($row[7]) : null;

                // Validate required fields
                if (empty($first_name) || empty($last_name) || empty($email)) {
                    $errors[] = "Row {$index}: Missing required fields (first name, last name, or email)";
                    Log::warning("Row {$index}: Missing required fields", [
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'email' => $email
                    ]);
                    $skippedCount++;
                    continue;
                }

                // Validate email format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row {$index}: Invalid email format ({$email})";
                    Log::warning("Row {$index}: Invalid email format", ['email' => $email]);
                    $skippedCount++;
                    continue;
                }

                try {
                    // Use provided id_number or generate one if empty
                    if (empty($id_number)) {
                        $id_number = 'STU' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                    }
                    
                    // Create or update user
                    $user = User::updateOrCreate(
                        ['email' => $email],
                        [
                            'id_number' => $id_number,
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'password' => Hash::make($password),
                            'role' => 'student',
                            'is_active' => true
                        ]
                    );

                    Log::info("User created/updated:", [
                        'id' => $user->id,
                        'email' => $user->email,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name
                    ]);

                    // Create or update student
                    $student = Student::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'course' => $course,
                            'year_level' => $year_level,
                            'section' => $section
                        ]
                    );

                    Log::info("Student created/updated:", [
                        'id' => $student->id,
                        'user_id' => $student->user_id,
                        'course' => $student->course,
                        'year_level' => $student->year_level,
                        'section' => $student->section
                    ]);

                    $processedCount++;

                } catch (\Exception $e) {
                    $errors[] = "Row {$index}: Database error - " . $e->getMessage();
                    Log::error("Error processing row {$index}:", [
                        'error' => $e->getMessage(),
                        'data' => compact('first_name', 'last_name', 'email', 'course', 'year_level', 'section')
                    ]);
                    $skippedCount++;
                }
            }

            // Commit transaction if we have at least one successful import
            if ($processedCount > 0) {
                DB::commit();
                
                $message = "Import completed! Processed: {$processedCount}, Skipped: {$skippedCount}";
                if (!empty($errors)) {
                    $message .= " | Errors: " . implode(', ', array_slice($errors, 0, 3));
                    if (count($errors) > 3) {
                        $message .= " and " . (count($errors) - 3) . " more...";
                    }
                }
                
                return back()->with('success', $message);
            } else {
                DB::rollback();
                return back()->with('error', 'No students were imported. Errors: ' . implode(', ', $errors));
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Import failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Error importing students: ' . $e->getMessage());
        }
    }

    public function show(Student $student)
    {
        $student->load(['user', 'subjects', 'badges', 'tasks']);
        return view('admin.student.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $subjects = Subject::all();
        $courses = Course::all();
        $sections = Section::all();
        return view('admin.student.edit', compact('student', 'subjects', 'courses', 'sections'));
    }

    public function update(Request $request, Student $student)
{
    $validator = Validator::make($request->all(), [
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => ['required', 'email', Rule::unique('users')->ignore($student->user_id)],
        'student_number' => ['required', 'string', Rule::unique('students')->ignore($student->id)],
        'course_id' => 'required|exists:courses,id',
        'year_level' => 'required|integer|min:1|max:5',
        'section_id' => 'nullable|exists:sections,id',
        'subjects' => 'nullable|array',
        'subjects.*' => 'exists:subjects,id',
        'password' => 'nullable|min:8|confirmed' // Make sure you have password_confirmation field in your form
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('error', 'Please check the form for errors.');
    }

    try {
        DB::beginTransaction();

        // Update user details
        $userData = [
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $student->user->update($userData);

        // Update student
        $studentData = [
            'student_number' => $request->student_number,
            'course_id' => $request->course_id,
            'year_level' => $request->year_level,
            'section_id' => $request->section_id ?? null,
        ];

        
        if ($request->filled('section_id')) {
            $studentData['section_id'] = $request->section_id;
        }

        $student->update($studentData);

        
        if ($request->filled('section_id')) {
            $student->sections()->sync([$request->section_id]);
        } else {
            $student->sections()->detach();
        }

        // Update subjects
        if ($request->has('subjects')) {
            $student->subjects()->sync($request->subjects ?? []);
        } else {
            $student->subjects()->detach();
        }

        DB::commit();

        $this->logActivity(
            "Updated Student",
            "Student",
            $student->id,
            [
                'student_id' => $student->id,
                'changes' => array_merge(
                    $student->getChanges(),
                    $student->user->getChanges()
                )
            ]
        );

        return redirect()->route('admin.student.show', $student)
            ->with('success', 'Student updated successfully');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Student update failed: ' . $e->getMessage());
        return back()->withInput()
            ->with('error', 'Error updating student: ' . $e->getMessage());
    }
}

    public function destroy(Student $student)
    {
        try {
            DB::beginTransaction();

            // Store user ID before deleting student
            $userId = $student->user_id;

            // Delete the student record first (this will cascade delete related records)
            $student->delete();

            // Delete the associated user record
            User::where('id', $userId)->delete();

            DB::commit();

            $this->logActivity($userId, "Deleted student with user_id {$userId}");

            return redirect()->route('admin.student.index')
                ->with('success', 'Student deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting student: ' . $e->getMessage());
            
            return redirect()->route('admin.student.index')
                ->with('error', 'Error deleting student. Please try again.');
        }
    }

}