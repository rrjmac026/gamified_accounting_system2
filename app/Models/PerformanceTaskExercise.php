<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceTaskExercise extends Model
{
    protected $fillable = [
        'performance_task_id',
        'step',
        'title',
        'description',
        'correct_data',
        'order',
    ];

    protected $casts = [
        'correct_data' => 'array',
    ];

    public function task()
    {
        return $this->belongsTo(PerformanceTask::class, 'performance_task_id');
    }
}