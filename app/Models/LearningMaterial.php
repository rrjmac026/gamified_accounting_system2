<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject_id',
        'instructor_id',
        'title',
        'description',
        'file_path',     // storage/app/public/materials/...
        'file_type',     // pdf, docx, pptx, video, link
        'file_size',     // in bytes
        'visibility',    // public, students_only, private
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'file_size'     => 'integer',
        'is_active'     => 'boolean',
        'published_at'  => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships pero ikaw wala ka ana 
    |--------------------------------------------------------------------------
    */

    // Belongs to a specific subject
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // Uploaded by an instructor
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    // Students who have accessed/downloaded 
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_learning_materials')
                    ->withPivot('accessed_at')
                    ->withTimestamps();
    }
}
