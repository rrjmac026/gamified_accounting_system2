<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizScore extends Model
{
    

    protected $fillable = [
        'student_id',
        'task_id',
        'score',
        'max_score',
        'percentage',
        'time_taken',
        'attempt_number',
        'completed_at'
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'time_taken' => 'integer', // in seconds
        'attempt_number' => 'integer',
        'completed_at' => 'datetime'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
