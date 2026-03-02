<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerformanceTaskComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'performance_task_id',
        'user_id',
        'parent_id',
        'body',
        'is_read',
        'sender_role',
        'step',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'step'    => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function task(): BelongsTo
    {
        return $this->belongsTo(PerformanceTask::class, 'performance_task_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(PerformanceTaskComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(PerformanceTaskComment::class, 'parent_id')->orderBy('created_at');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /** Top-level messages only (no replies) */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /** Comments for a specific step */
    public function scopeForStep($query, ?int $step)
    {
        return $query->where('step', $step);
    }

    /** Unread messages for a given user role */
    public function scopeUnreadFor($query, string $role)
    {
        // Unread = sender_role is NOT the viewer's role and is_read = false
        return $query->where('sender_role', '!=', $role)->where('is_read', false);
    }
}