<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leaderboard extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'rank_position',
        'total_xp',
        'total_score',
        'tasks_completed',
        'period_type', // weekly, monthly, semester, overall
        'period_start',
        'period_end'
    ];

    protected $casts = [
        'period_start'   => 'date',
        'period_end'     => 'date',
        'total_xp'       => 'integer',
        'total_score'    => 'decimal:2',
        'tasks_completed'=> 'integer',
        'rank_position'  => 'integer'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    // Human-friendly period label
    public function getPeriodLabelAttribute()
    {
        return match ($this->period_type) {
            'weekly'   => 'This Week',
            'monthly'  => 'This Month',
            'semester' => 'This Semester',
            'overall'  => 'Overall',
            default    => ucfirst($this->period_type),
        };
    }

    // Rank text (1st, 2nd, 3rd, etc.)
    public function getRankTextAttribute()
    {
        $rank = $this->rank_position;
        if (!$rank) return null;

        return match (true) {
            $rank % 10 == 1 && $rank % 100 != 11 => $rank . 'st',
            $rank % 10 == 2 && $rank % 100 != 12 => $rank . 'nd',
            $rank % 10 == 3 && $rank % 100 != 13 => $rank . 'rd',
            default => $rank . 'th',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    // Query leaderboards for a specific period
    public function scopeForPeriod($query, $type)
    {
        if ($type === 'overall') {
            return $query;
        }

        $start = now()->startOf($type);
        $end   = now()->endOf($type);

        return $query->whereBetween('period_start', [$start, $end]);
    }
}
