<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;

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

            return redirect()->route('two-factor.challenge');
        }

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
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
