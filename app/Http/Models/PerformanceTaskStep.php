<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceTaskStep extends Model
{
    protected $fillable = [
        'performance_task_id',
        'step_number',
        'title',
        'sheet_name',
        'template_data',
    ];

    protected $casts = [
        'template_data' => 'array',
    ];

    public function performanceTask()
    {
        return $this->belongsTo(PerformanceTask::class);
    }
}
