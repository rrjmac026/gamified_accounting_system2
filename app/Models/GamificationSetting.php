<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamificationSetting extends Model
{
    protected $fillable = [
        'min_passing_score',
        'xp_per_task',
        'level_up_threshold',
        'late_penalty',
        'bonus_for_perfect'
    ];
}

