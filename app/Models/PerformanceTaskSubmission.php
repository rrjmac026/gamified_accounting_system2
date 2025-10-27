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
        'attempts'      
    ];

    protected $casts = [
        'submission_data' => 'array',
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