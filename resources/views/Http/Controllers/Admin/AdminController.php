<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Subject;
use App\Models\Task;

class AdminController extends Controller
{
    /**
     * Admin dashboard statistics.
     */
    public function dashboard()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_instructors' => Instructor::count(),
            'total_subjects' => Subject::count(),
            'total_users' => User::count(),
        ];
        
        return view('admin.dashboard', compact('stats'));
    }
}
