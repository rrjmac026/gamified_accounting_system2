<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeedbackRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'performance_task_id',
        'feedback_type',
        'feedback_text',
        'recommendations',
        'rating',
        'generated_at',
        'is_read',
        'is_anonymous',
    ];

    protected $casts = [
        'recommendations' => 'array',
        'generated_at' => 'datetime',
        'feedback_date' => 'datetime',
        'is_read' => 'boolean',
        'is_anonymous' => 'boolean',
        'rating' => 'integer'
    ];

    // Set default values
    protected $attributes = [
        'is_read' => false,
        'is_anonymous' => false,
        'feedback_type' => 'general',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    // Update relationship
    public function performanceTask()
    {
        return $this->belongsTo(PerformanceTask::class, 'performance_task_id');
    }

    // Mutator to handle recommendations properly
    public function setRecommendationsAttribute($value)
    {
        if (is_string($value)) {
            // Convert string (from form) to array
            $recommendations = array_filter(
                array_map('trim', explode("\n", str_replace(["\r\n", "\r"], "\n", $value))),
                function($item) {
                    return !empty($item);
                }
            );
            $this->attributes['recommendations'] = json_encode($recommendations);
        } elseif (is_array($value)) {
            $this->attributes['recommendations'] = json_encode($value);
        } else {
            $this->attributes['recommendations'] = json_encode([]);
        }
    }

    // Accessor to ensure recommendations is always an array
    public function getRecommendationsAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
    }

    // Scopes for easier querying
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('feedback_type', $type);
    }

    public function scopeWithRating($query)
    {
        return $query->whereNotNull('rating');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('generated_at', '>=', now()->subDays($days));
    }
}