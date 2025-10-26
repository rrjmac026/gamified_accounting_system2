<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\SystemNotification;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $notifications = SystemNotification::where('user_id', Auth::id())
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    })
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();

                $unreadCount = $notifications->where('is_read', false)->count();

                $view->with('notifications', $notifications)
                     ->with('unreadCount', $unreadCount);
            }
        });
    }
}
