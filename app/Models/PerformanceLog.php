<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceLog extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'performance_task_id',
        'performance_metric',
        'value',
        'recorded_at'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'recorded_at' => 'datetime'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // Support both Task and PerformanceTask
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function performanceTask()
    {
        return $this->belongsTo(PerformanceTask::class, 'task_id');
    }

    // Accessor for student name
    public function getStudentNameAttribute()
    {
        return $this->student->user->full_name ?? 'N/A';
    }

    // Scopes for easier querying
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByMetric($query, $metric)
    {
        return $query->where('performance_metric', $metric);
    }

    // Helper method to get formatted metric name
    public function getFormattedMetricAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->performance_metric));
    }

    // Static method to get average performance for a student
    public static function getStudentAverage($studentId, $metric = null)
    {
        $query = static::where('student_id', $studentId);
        
        if ($metric) {
            $query->where('performance_metric', $metric);
        }
        
        return $query->avg('value');
    }

    // Static method to get student performance summary
    public static function getStudentSummary($studentId, $taskId = null)
    {
        $query = static::where('student_id', $studentId);
        
        if ($taskId) {
            $query->where('task_id', $taskId);
        }
        
        return [
            'total_logs' => $query->count(),
            'average_accuracy' => $query->where('performance_metric', 'LIKE', '%accuracy%')->avg('value'),
            'total_attempts' => $query->where('performance_metric', 'LIKE', '%attempts%')->sum('value'),
            'completed_tasks' => $query->where('performance_metric', 'task_completed')->count(),
        ];
    }
}