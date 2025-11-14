<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceTaskSubmission extends Model
{
    protected $fillable = [
    'task_id',
    'student_id',
    'step',
    'submission_data',
    'status',
    'score',  
    'remarks',    
    'attempts',
    'instructor_feedback',
    'feedback_given_at',
    'needs_feedback'
    ];

    protected $casts = [
        'submission_data' => 'array',
        'feedback_given_at' => 'datetime',
        'needs_feedback' => 'boolean',
    ];

    public function task()
    {
        return $this->belongsTo(PerformanceTask::class, 'task_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}