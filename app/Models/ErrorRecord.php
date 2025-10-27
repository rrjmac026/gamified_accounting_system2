<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorRecord extends Model
{
    

    protected $fillable = [
        'task_submission_id',
        'student_id',
        'error_type',
        'error_description',
        'question_id',
        'frequency',
        'severity_level',
        'identified_at'
    ];

    protected $casts = [
        'frequency' => 'integer',
        'severity_level' => 'integer',
        'identified_at' => 'datetime'
    ];

    // Relationships
    public function submission()
    {
        return $this->belongsTo(TaskSubmission::class, 'task_submission_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function question()
    {
        return $this->belongsTo(TaskQuestion::class, 'question_id');
    }
}
