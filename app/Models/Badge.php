<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    

    protected $fillable = [
        'name',
        'description',
        'icon_path',
        'criteria',
        'xp_threshold',
        'is_active'
    ];

    protected $casts = [
        'xp_threshold' => 'integer',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_badges')
                    ->withPivot('earned_at')
                    ->withTimestamps();
    }
}
