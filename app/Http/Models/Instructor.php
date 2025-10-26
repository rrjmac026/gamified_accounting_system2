<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'department',
        'specialization'
    ];

    protected $with = ['user'];

    protected $appends = ['name', 'email', 'stats'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'instructor_subject');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function students()
    {
        // Remove old hasManyThrough relationship and replace with this:
        return Student::whereHas('subjects', function($query) {
            $query->whereHas('instructors', function($q) {
                $q->where('instructors.id', $this->id);
            });
        });
    }

    // Add accessor for full name
    public function getNameAttribute()
    {
        return $this->user ? $this->user->name : 'N/A';
    }

    // Add accessor for email
    public function getEmailAttribute()
    {
        return $this->user ? $this->user->email : 'N/A';
    }

    public function getStatsAttribute()
    {
        return [
            'total_subjects' => $this->subjects()->count(),
            'total_tasks' => $this->tasks()->count(),
            'active_tasks' => $this->tasks()->where('is_active', true)->count(),
            'total_students' => $this->students()->count(),
        ];
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'instructor_section', 'instructor_id', 'section_id');
    }
    
    public function getSectionStudentsAttribute()
    {
        return Student::whereHas('sections', function($query) {
            $query->whereHas('instructors', function($q) {
                $q->where('instructors.id', $this->id);
            });
        })->get();
    }

}

