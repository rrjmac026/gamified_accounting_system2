<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use App\Models\ActivityLog;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // ✅ Check if the user has 2FA enabled
        if (!empty($user->two_factor_secret)) {
            Auth::logout(); // temporarily logout the user
            session(['2fa:user:id' => $user->id]); // store user ID in session

            // Log 2FA challenge initiated
            $this->logActivity('2fa challenge initiated', $user->id, [
                'email' => $user->email,
                'role' => $user->role,
            ]);

            return redirect()->route('two-factor.challenge');
        }

        // ✅ Log successful login
        $this->logActivity('user logged in', $user->id, [
            'email' => $user->email,
            'role' => $user->role,
            'name' => $user->name,
        ]);

        // ✅ Otherwise, continue to normal login flow
        $request->session()->regenerate();
        return $this->redirectToRoleDashboard();
    }

    /**
     * Redirect user to their role-specific dashboard.
     */
    protected function redirectToRoleDashboard(): RedirectResponse
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin' => redirect()->intended(route('admin.dashboard')),
            'instructor' => redirect()->intended(route('instructors.dashboard')),
            'student' => redirect()->intended(route('students.dashboard')),
            default => redirect()->intended('/'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // ✅ Log logout before destroying session
        if ($user) {
            $this->logActivity('user logged out', $user->id, [
                'email' => $user->email,
                'role' => $user->role,
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Simple logging helper for auth events
     */
    protected function logActivity(string $action, ?int $userId, array $details = []): void
    {
        ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => 'User',
            'model_id' => $userId,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'performed_at' => now(),
        ]);
    }
}