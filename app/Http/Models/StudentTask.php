<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTask extends Model
{
    

    protected $table = 'student_tasks';
    
    protected $fillable = [
        'student_id',
        'task_id',
        'status', // assigned, in_progress, submitted, graded, overdue
        'score',
        'xp_earned',
        'submitted_at',
        'graded_at'
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'xp_earned' => 'integer',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime'
        
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

}
