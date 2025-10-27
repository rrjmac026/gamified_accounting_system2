<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\ResetsUserPasswords;
use App\Actions\Fortify\ResetUserPassword;
use App\Models\TaskSubmission;
use App\Observers\TaskSubmissionObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemNotification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ResetsUserPasswords::class, ResetUserPassword::class);
        
    
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // TaskSubmission::observe(TaskSubmissionObserver::class);
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                $notifications = SystemNotification::where('user_id', $user->id)
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                    })
                    ->latest()
                    ->take(5)
                    ->get();

                $unreadCount = SystemNotification::where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count();

                $view->with(compact('notifications', 'unreadCount'));
            }
        });
    }
}
