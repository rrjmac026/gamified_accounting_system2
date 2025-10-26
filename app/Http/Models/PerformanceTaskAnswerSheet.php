<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceTaskAnswerSheet extends Model
{
    protected $fillable = [
        'performance_task_id',
        'step',
        'correct_data',
    ];

    protected $casts = [
        'correct_data' => 'array',
    ];

    public function performanceTask()
    {
        return $this->belongsTo(PerformanceTask::class, 'performance_task_id');
    }
}
