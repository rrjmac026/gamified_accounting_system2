<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{

    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'student_number',
        'course_id',
        'year_level',
        'section_id',
        'total_xp',
        'current_level',
        'performance_rating',
        'hide_from_leaderboard',
    ];

    protected $casts = [
        'total_xp' => 'integer',
        'current_level' => 'integer',
        'performance_rating' => 'decimal:2',
        'hide_from_leaderboard' => 'boolean'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'student_tasks')
                    ->withPivot('status', 'score', 'xp_earned', 'submitted_at', 'graded_at', 'retry_count', 'due_date')
                    ->withTimestamps();
    }



    public function studentTasks()
    {
        // Full access: gives you StudentTask models
        return $this->hasMany(StudentTask::class);
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function taskSubmissions()
    {
        return $this->hasMany(\App\Models\TaskSubmission::class);
    }

    public function submittedTasks()
    {
        return $this->belongsToMany(\App\Models\Task::class, 'task_submissions')
                    ->withPivot(['status', 'score', 'xp_earned', 'submitted_at', 'graded_at']);
    }

    public function performanceLogs()
    {
        return $this->hasMany(PerformanceLog::class);
    }

    public function quizScores()
    {
        return $this->hasMany(QuizScore::class);
    }

    public function xpTransactions()
    {
        return $this->hasMany(XpTransaction::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'student_badges')
                    ->withPivot('earned_at')
                    ->withTimestamps();
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'student_subjects')
                    ->withPivot('enrollment_date', 'status')
                    ->withTimestamps();
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'section_student')
                    ->withTimestamps();
    }

    public function getLeaderboardRank()
    {
        // Example: rank within same section
        $sectionIds = $this->sections->pluck('id');

        $studentsInSection = Student::whereHas('sections', fn($q) => $q->whereIn('sections.id', $sectionIds))
            ->with('xpTransactions')
            ->get();

        $ranked = $studentsInSection->sortByDesc(fn($s) => $s->xpTransactions->sum('amount'))->values();

        return $ranked->search(fn($s) => $s->id === $this->id) + 1;
    }

    public function getTotalXp()
    {
        return $this->xpTransactions()->sum('amount') ?? 0;
    }

    public function checkAndAwardBadges()
    {
        $totalXp = $this->getTotalXp();
        
        $eligibleBadges = Badge::where(function($query) use ($totalXp) {
                $query->where('xp_threshold', '<=', $totalXp)
                      ->where('is_active', true);
            })
            ->whereNotIn('id', $this->badges()->pluck('badges.id'))
            ->get();
        
        $newlyAwarded = [];
        foreach ($eligibleBadges as $badge) {
            // Actually award the badge by adding to pivot table
            $this->badges()->attach($badge->id, [
                'earned_at' => now(),
            ]);
            $newlyAwarded[] = $badge;
        }
        
        return collect($newlyAwarded);
    }

    public function performanceTasks()
    {
        return $this->belongsToMany(PerformanceTask::class, 'performance_task_student')
                    ->withPivot('status', 'score', 'xp_earned', 'feedback', 'attempts', 'submitted_at', 'graded_at')
                    ->withTimestamps();
    }


    public function performanceTaskSubmissions()
    {
        return $this->hasMany(\App\Models\PerformanceTaskSubmission::class, 'student_id');
    }


    
}
