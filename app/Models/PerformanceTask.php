<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceTask extends Model
{
    protected $fillable = [
        'title',
        'description',
        'max_attempts',
        'xp_reward',
        'subject_id',
        'section_id',
        'instructor_id',
        'due_date',
        'late_until',
        'max_score',
        'deduction_per_error',
    ];

    protected $casts = [
        'template_data' => 'array',
        'due_date' => 'datetime',
        'late_until' => 'datetime',
    ];

    // Submissions per student (individual tracking)
    public function submissions()
    {
        return $this->hasMany(PerformanceTaskSubmission::class, 'task_id');
    }

    // The instructor who created this task
    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    // The subject this task belongs to
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    // The section this task is assigned to
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    // Steps/questions within this task
    public function steps()
    {
        return $this->hasMany(PerformanceTaskStep::class, 'performance_task_id');
    }

    // Answer sheets for this task
    public function answerSheets()
    {
        return $this->hasMany(PerformanceTaskAnswerSheet::class);
    }

    // XP transactions related to this task
    public function xpTransactions()
    {
        return $this->hasMany(XpTransaction::class, 'performance_task_id');
    }

    public function students()
{
    return $this->belongsToMany(Student::class, 'performance_task_student')
        ->withPivot(['status', 'score', 'attempts']) // Removed 'completed_at'
        ->withTimestamps();
}
}