<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'student_id',
        'instructor_id',
        'course_id',
        'responses',
        'comments',
        'submitted_at'
    ];

    protected $casts = [
        'responses' => 'array',
        'submitted_at' => 'datetime'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
