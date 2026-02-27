<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceTaskSubmissionHistory extends Model
{
    protected $fillable = [
        'submission_id',
        'task_id',
        'student_id',
        'step',
        'attempt_number',
        'submission_data',
        'status',
        'score',
        'remarks',
        'error_count',
        'is_late',
    ];

    protected $casts = [
        'submission_data' => 'array',
        'is_late'         => 'boolean',
        'score'           => 'float',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function submission()
    {
        return $this->belongsTo(PerformanceTaskSubmission::class, 'submission_id');
    }

    public function task()
    {
        return $this->belongsTo(PerformanceTask::class, 'task_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Returns a human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'correct'     => 'Perfect',
            'passed'      => 'Passed',
            'wrong'       => 'Wrong',
            'in-progress' => 'In Progress',
            default       => ucfirst($this->status),
        };
    }

    /**
     * Returns tailwind colour classes based on status.
     */
    public function getStatusColorAttribute(): array
    {
        return match ($this->status) {
            'correct' => [
                'bg'     => 'bg-green-50',
                'border' => 'border-green-200',
                'text'   => 'text-green-700',
                'badge'  => 'bg-green-100 text-green-800',
                'dot'    => 'bg-green-500',
            ],
            'passed' => [
                'bg'     => 'bg-blue-50',
                'border' => 'border-blue-200',
                'text'   => 'text-blue-700',
                'badge'  => 'bg-blue-100 text-blue-800',
                'dot'    => 'bg-blue-500',
            ],
            'wrong' => [
                'bg'     => 'bg-red-50',
                'border' => 'border-red-200',
                'text'   => 'text-red-700',
                'badge'  => 'bg-red-100 text-red-800',
                'dot'    => 'bg-red-500',
            ],
            default => [
                'bg'     => 'bg-gray-50',
                'border' => 'border-gray-200',
                'text'   => 'text-gray-700',
                'badge'  => 'bg-gray-100 text-gray-800',
                'dot'    => 'bg-gray-400',
            ],
        };
    }
}