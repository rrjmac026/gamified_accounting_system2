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
        'enabled_steps',   // ← NEW
    ];

    protected $casts = [
        'template_data'  => 'array',
        'enabled_steps'  => 'array',   // ← NEW: stored as JSON, cast to PHP array
        'due_date'       => 'datetime',
        'late_until'     => 'datetime',
    ];

    // ── NEW HELPERS ──────────────────────────────────────────────────────────

    /**
     * Returns the list of enabled step numbers (1–10).
     * If enabled_steps is null (legacy tasks), all 10 steps are enabled.
     */
    public function getEnabledStepsListAttribute(): array
    {
        return $this->enabled_steps ?? range(1, 10);
    }

    /**
     * Returns true if the given step number is enabled for this task.
     */
    public function isStepEnabled(int $step): bool
    {
        return in_array($step, $this->enabled_steps_list);
    }

    // ── RELATIONSHIPS ────────────────────────────────────────────────────────

    public function submissions()
    {
        return $this->hasMany(PerformanceTaskSubmission::class, 'task_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function steps()
    {
        return $this->hasMany(PerformanceTaskStep::class, 'performance_task_id');
    }

    public function answerSheets()
    {
        return $this->hasMany(PerformanceTaskAnswerSheet::class);
    }

    public function xpTransactions()
    {
        return $this->hasMany(XpTransaction::class, 'performance_task_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'performance_task_student')
                    ->withPivot('status', 'score', 'xp_earned', 'feedback', 'attempts', 'submitted_at', 'graded_at')
                    ->withTimestamps();
    }

    public function feedbacks()
    {
        return $this->hasMany(FeedbackRecord::class, 'performance_task_id');
    }

    public function exercises()
    {
        return $this->hasMany(PerformanceTaskExercise::class, 'performance_task_id');
    }

    public function exercisesForStep(int $step)
    {
        return $this->exercises()->where('step', $step)->orderBy('order')->get();
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\PerformanceTaskComment::class, 'performance_task_id');
    }
}