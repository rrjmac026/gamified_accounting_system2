<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XpTransaction extends Model
{
    protected $fillable = [
        'student_id',
        'amount',
        'type',           
        'source',         
        'source_id',   
        'description',
        'processed_at'
    ];

    protected $casts = [
        'amount' => 'integer',
        'processed_at' => 'datetime'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }


    public function getSourceModel()
    {
        $models = [
            'performance_task' => PerformanceTask::class,

        ];

        if (isset($models[$this->source]) && $this->source_id) {
            return $models[$this->source]::find($this->source_id);
        }

        return null;
    }
}