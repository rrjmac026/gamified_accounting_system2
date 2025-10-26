<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\Loggable;

use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class UserController extends Controller
{
    use Loggable;

    /**
     * Display a listing of users with filtering and pagination
     */
    public function index(Request $request)
    {
        $query = User::query()->with(['student', 'instructor']);

        // Apply filters
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('id_number', 'LIKE', "%{$search}%");
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Users retrieved successfully'
            ]);
        }

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = ['student', 'instructor', 'admin'];
        $courses = Course::all();
        $sections = Section::all();
        $subjects = Subject::all();
        
        return view('admin.users.create', compact('roles', 'courses', 'sections', 'subjects'));
    }

    /**
     * Store a newly created user in storage
     */
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:student,instructor,admin',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|max:100',
            'is_active' => 'boolean',
            
            // Student specific fields
            'course' => 'nullable|required_if:role,student|string|max:100',
            'year_level' => 'nullable|required_if:role,student|integer|min:1|max:5',
            'section' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9\-]+$/',
            
            // Instructor specific fields
            'employee_id' => 'nullable|required_if:role,instructor|string|max:20|unique:instructors,employee_id',
            'department' => 'nullable|string|max:100',
            'specialization' => 'nullable|string|max:255',
        ], [
            'first_name.required' => 'The first name field is required.',
            'last_name.required' => 'The last name field is required.',
            'email.unique' => 'This email address is already in use.',
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, and one number.',
            'section.regex' => 'The section may only contain letters, numbers, and hyphens.',
            'course.required_if' => 'The course field is required when role is student.',
            'year_level.required_if' => 'The year level field is required when role is student.',
            'employee_id.required_if' => 'The employee ID field is required when role is instructor.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'permissions' => $request->permissions ?? [],
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Create role-specific records
            if ($request->role === 'student') {
                Student::create([
                    'user_id' => $user->id,
                    'course' => $request->course,
                    'year_level' => $request->year_level,
                    'section' => $request->section,
                    'total_xp' => 0,
                    'current_level' => 1,
                    'performance_rating' => 0.00,
                ]);
            } elseif ($request->role === 'instructor') {
                Instructor::create([
                    'user_id' => $user->id,
                    'employee_id' => $request->employee_id,
                    'department' => $request->department,
                    'specialization' => $request->specialization,
                ]);
            }

            // Log the activity (updated to match SubjectController pattern)
            $this->logActivity(
                "Created User",
                "User",
                $user->id,
                [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'role' => $user->role
                ]
            );

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $user->load(['student', 'instructor']),
                    'message' => 'User created successfully'
                ], 201);
            }

            return redirect()->route('admin.users.index')
                           ->with('success', 'User created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        $user = User::with(['student', 'instructor', 'activityLogs' => function($query) {
            $query->latest()->take(10);
        }])->findOrFail($id);

        // Get additional statistics
        $stats = [
            'total_logins' => $user->activityLogs()->where('action', 'login')->count(),
            'last_activity' => $user->activityLogs()->latest()->first()?->performed_at,
            'account_age' => $user->created_at->diffInDays(now()),
        ];

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => array_merge($user->toArray(), ['stats' => $stats]),
                'message' => 'User retrieved successfully'
            ]);
        }

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $user->load(['student.subjects']); // Eager load the relationships
        $courses = Course::all();
        $sections = Section::all();
        $subjects = Subject::all();
        
        return view('admin.users.edit', compact('user', 'courses', 'sections', 'subjects'));
    }

    /**
     * Update the specified user in storage
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:student,instructor,admin',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
            
            // Student specific fields
            'course' => 'nullable|required_if:role,student|string|max:100',
            'year_level' => 'nullable|required_if:role,student|integer|min:1|max:5',
            'section' => 'nullable|string|max:50',
            
            // Instructor specific fields
            'employee_id' => [
                'nullable',
                'required_if:role,instructor',
                'string',
                'max:20',
                Rule::unique('instructors')->ignore($user->instructor?->id ?? null)
            ],
            'department' => 'nullable|string|max:100',
            'specialization' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::findOrFail($id);
            $originalData = $user->toArray();

            // Update user data
            $userData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'role' => $request->role,
                'permissions' => $request->permissions ?? [],
                'is_active' => $request->boolean('is_active', true),
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Handle role-specific updates
            if ($request->role === 'student') {
                if ($user->student) {
                    $user->student->update([
                        'course' => $request->course,
                        'year_level' => $request->year_level,
                        'section' => $request->section,
                    ]);
                } else {
                    Student::create([
                        'user_id' => $user->id,
                        'course' => $request->course,
                        'year_level' => $request->year_level,
                        'section' => $request->section,
                        'total_xp' => 0,
                        'current_level' => 1,
                        'performance_rating' => 0.00,
                    ]);
                }
                // Remove instructor record if exists
                if ($user->instructor) {
                    $user->instructor->delete();
                }
            } elseif ($request->role === 'instructor') {
                if ($user->instructor) {
                    $user->instructor->update([
                        'employee_id' => $request->employee_id,
                        'department' => $request->department,
                        'specialization' => $request->specialization,
                    ]);
                } else {
                    Instructor::create([
                        'user_id' => $user->id,
                        'employee_id' => $request->employee_id,
                        'department' => $request->department,
                        'specialization' => $request->specialization,
                    ]);
                }
                // Remove student record if exists
                if ($user->student) {
                    $user->student->delete();
                }
            } else { // admin role
                // Remove both student and instructor records
                if ($user->student) {
                    $user->student->delete();
                }
                if ($user->instructor) {
                    $user->instructor->delete();
                }
            }

            // Log the activity (updated to match SubjectController pattern)
            $this->logActivity(
                "Updated User",
                "User",
                $user->id,
                [
                    'original' => $originalData,
                    'changes' => $user->getChanges()
                ]
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $user->fresh()->load(['student', 'instructor']),
                    'message' => 'User updated successfully'
                ]);
            }

            return redirect()->route('admin.users.show', $user)
                           ->with('success', 'User updated successfully');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update user: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                           ->with('error', 'Failed to update user: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Remove the specified user from storage
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent deletion of current user
        if ($user->id === Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account'
                ], 403);
            }
            return redirect()->back()->with('error', 'You cannot delete your own account');
        }

        try {
            $userData = $user->toArray();
            $user->delete();

            // Log the activity (updated to match SubjectController pattern)
            $this->logActivity(
                "Deleted User",
                "User",
                $user->id,
                ['user_data' => $userData]
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User deleted successfully'
                ]);
            }

            return redirect()->route('admin.users.index')
                           ->with('success', 'User deleted successfully');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete user: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                           ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Activate a user account
     */
    public function activate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->is_active) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already active'
                ], 400);
            }
            return redirect()->back()->with('info', 'User is already active');
        }

        $user->update(['is_active' => true]);

        $this->logActivity('user_activated', 'User', $user->id, [
            'activated_by' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User activated successfully'
            ]);
        }

        return redirect()->back()->with('success', 'User activated successfully');
    }

    /**
     * Deactivate a user account
     */
    public function deactivate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent deactivation of current user
        if ($user->id === Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot deactivate your own account'
                ], 403);
            }
            return redirect()->back()->with('error', 'You cannot deactivate your own account');
        }

        if (!$user->is_active) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already inactive'
                ], 400);
            }
            return redirect()->back()->with('info', 'User is already inactive');
        }

        $user->update(['is_active' => false]);

        $this->logActivity('user_deactivated', 'User', $user->id, [
            'deactivated_by' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User deactivated successfully'
            ]);
        }

        return redirect()->back()->with('success', 'User deactivated successfully');
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'role' => 'required|in:student,instructor,admin',
            'permissions' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator);
        }

        $originalRole = $user->role;

        $user->update([
            'role' => $request->role,
            'permissions' => $request->permissions ?? [],
        ]);

        $this->logActivity('role_assigned', 'User', $user->id, [
            'previous_role' => $originalRole,
            'new_role' => $request->role,
            'assigned_by' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Role assigned successfully'
            ]);
        }

        return redirect()->back()->with('success', 'Role assigned successfully');
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated. Please contact the administrator.'
                ], 403);
            }

            // Update last login
            $user->update(['last_login_at' => now()]);

            // Log the activity
            $this->logActivity('login', 'User', $user->id, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user->load(['student', 'instructor']),
                    'token' => $token
                ],
                'message' => 'Login successful'
            ]);
        }

        // Log failed login attempt
        $this->logActivity('failed_login', null, [
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // Log the activity
            $this->logActivity('logout', $user->id, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Revoke current token
            $request->user()->currentAccessToken()->delete();
        }

        Auth::logout();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    /**
     * Get user statistics
     */
    public function statistics()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'students' => User::where('role', 'student')->count(),
            'instructors' => User::where('role', 'instructor')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'recent_registrations' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'recent_logins' => User::where('last_login_at', '>=', now()->subDays(1))->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Statistics retrieved successfully'
        ]);
    }

    /**
     * Search users
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2|max:100',
            'role' => 'nullable|in:student,instructor,admin',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = User::query()
            ->where(function (Builder $q) use ($request) {
                $search = $request->query;
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->with(['student', 'instructor'])
                      ->limit($request->get('limit', 10))
                      ->get();

        return response()->json([
            'success' => true,
            'data' => $users,
            'message' => 'Search completed successfully'
        ]);
    }

    /**
     * Bulk operations on users
     */
    public function bulkOperation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'operation' => 'required|in:activate,deactivate,delete,assign_role',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
            'role' => 'nullable|required_if:operation,assign_role|in:student,instructor,admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userIds = $request->user_ids;
        $currentUserId = Auth::id();
        
        // Remove current user from operations to prevent self-modification
        $userIds = array_filter($userIds, fn($id) => $id !== $currentUserId);

        if (empty($userIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid users selected for operation'
            ], 400);
        }

        try {
            $affectedCount = 0;

            switch ($request->operation) {
                case 'activate':
                    $affectedCount = User::whereIn('id', $userIds)->update(['is_active' => true]);
                    break;

                case 'deactivate':
                    $affectedCount = User::whereIn('id', $userIds)->update(['is_active' => false]);
                    break;

                case 'delete':
                    $affectedCount = User::whereIn('id', $userIds)->delete();
                    break;
            }

            // Log bulk operation
            $this->logActivity('bulk_operation', 'User', null, [
                'operation' => $request->operation,
                'user_ids' => $userIds,
                'affected_count' => $affectedCount,
                'performed_by' => $currentUserId,
            ]);

            return response()->json([
                'success' => true,
                'data' => ['affected_count' => $affectedCount],
                'message' => "Bulk {$request->operation} completed successfully. {$affectedCount} users affected."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk operation failed: ' . $e->getMessage()
            ], 500);
        }
    }
}