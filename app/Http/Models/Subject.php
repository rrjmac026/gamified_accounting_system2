<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
   use HasFactory;

    protected $fillable = [
        'subject_code',
        'subject_name',
        'description',
        'semester',
        'academic_year',
        'is_active',
        'units',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function instructors()
    {
        return $this->belongsToMany(Instructor::class, 'instructor_subject')
                    ->withTimestamps();
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_subjects')
                    ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function performanceLogs()
    {
        return $this->hasMany(PerformanceLog::class);
    }
    
    public function sections()
    {
        return $this->belongsToMany(Section::class, 'section_subject', 'subject_id', 'section_id')
                    ->withTimestamps();
    }

    public function performanceTasks()
    {
        return $this->hasMany(PerformanceTask::class, 'subject_id');
    }

}

