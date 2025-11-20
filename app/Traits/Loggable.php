<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait Loggable
{
    /**
     * Log an activity - Simple and straightforward
     *
     * @param string $action What happened (e.g., 'created task', 'updated student', 'deleted course')
     * @param array $details Any additional info you want to store
     * @return ActivityLog
     */
    protected function logActivity(string $action, array $details = []): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => class_basename($this),
            'model_id' => $this->id ?? null,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'performed_at' => now(),
        ]);
    }
}