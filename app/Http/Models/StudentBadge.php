<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentBadge extends Model
{
    

    protected $table = 'student_badges';
    
    protected $fillable = [
        'student_id',
        'badge_id',
        'earned_at'
    ];

    protected $casts = [
        'earned_at' => 'datetime'
    ];
}
