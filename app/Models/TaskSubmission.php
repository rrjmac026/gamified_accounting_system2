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
        'xp_earned'
    ];

    protected $casts = [
        'submission_data' => 'array',
        'xp_earned' => 'integer', // ✅ Add this
    ];

    public function task()
    {
        return $this->belongsTo(PerformanceTask::class, 'task_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
