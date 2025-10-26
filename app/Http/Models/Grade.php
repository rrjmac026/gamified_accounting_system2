<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'semester',
        'academic_year',
        'final_grade',
        'remarks',
        'is_manual',
    ];

    protected $casts = [
        'final_grade' => 'decimal:2',
        'is_manual' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // PH grading system ni siya bay
    public function setFinalGradeAttribute($value)
    {
        $this->attributes['final_grade'] = $value;

        // e set ni niya og di ka gustog manual
        if (!isset($this->attributes['is_manual']) || !$this->attributes['is_manual']) {
            $this->attributes['remarks'] = $value <= 3.0 ? 'Passed' : 'Failed';
        }
    }
}

