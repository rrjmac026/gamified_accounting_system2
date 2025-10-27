<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'subject_id',
        'instructor_id',
        'section_id',
        'max_score',
        'xp_reward',
        'retry_limit',
        'late_penalty',
        'due_date',
        'instructions',
        'status',
        'is_active',
        'auto_grade',
        'parent_task_id',
        'attachment',
        'late_until',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'is_active' => 'boolean',
        'auto_grade' => 'boolean',
        'max_score' => 'integer',
        'xp_reward' => 'integer',
        'retry_limit' => 'integer',
        'late_penalty' => 'integer',
        'options' => 'array',
        'points' => 'integer',
        'order_index' => 'integer',
    ];

    // Relationships
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    // Many-to-many relationship with students through pivot table
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_tasks')
                    ->withPivot(['status', 'score', 'xp_earned', 'submitted_at', 'graded_at', 'retry_count'])
                    ->withTimestamps();
    }

    // Self-referencing relationship for questions
    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function questions()
    {
        return $this->hasMany(Task::class, 'parent_task_id')
                    ->where('type', 'question');
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    // Scopes
    public function scopeMainTasks($query)
    {
        return $query->whereNull('parent_task_id');
    }

    public function scopeQuestions($query)
    {
        return $query->where('type', 'question');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isQuestion()
    {
        return $this->type === 'question';
    }

    public function isMainTask()
    {
        return is_null($this->parent_task_id);
    }

    public function getQuestionsCount()
    {
        return $this->questions()->count();
    }

    public function getTotalPoints()
    {
        if ($this->isQuestion()) {
            return $this->points;
        }
        
        return $this->questions()->sum('points') ?: $this->max_score;
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}