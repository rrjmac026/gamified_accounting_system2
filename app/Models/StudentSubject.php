<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSubject extends Model
{
    

    protected $table = 'student_subjects';
    
    protected $fillable = [
        'student_id',
        'subject_id',
        'enrollment_date',
        'status' // enrolled, completed, dropped
    ];

    protected $casts = [
        'enrollment_date' => 'date'
    ];
}
