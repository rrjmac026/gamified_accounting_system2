<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use PragmaRX\Google2FA\Google2FA;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $student = null;
        $totalXp = 0;
        $badges = collect();
        
        // Only fetch badges data if user is a student
        if ($user->role === 'student') {
            $student = $user->student;
            $totalXp = (int) $student->xpTransactions()->sum('amount');
            
            // Get all badges and mark which ones are earned
            $badges = \App\Models\Badge::where('is_active', true)
                ->get()
                ->map(function ($badge) use ($student, $totalXp) {
                    $earnedBadge = $student->badges()
                        ->where('badges.id', $badge->id)
                        ->first();
                    
                    // Check if badge is earned
                    $badge->earned = (bool) $earnedBadge || $totalXp >= $badge->xp_threshold;
                    $badge->earned_at = $earnedBadge ? $earnedBadge->pivot->earned_at : null;
                    
                    // Add progress data for XP-based badges
                    if (!$badge->earned) {
                        $badge->progress = $totalXp;
                        $badge->remaining = max(0, $badge->xp_threshold - $totalXp);
                    }
                    
                    return $badge;
                });
        }

        return view('profile.edit', [
            'user' => $user,
            'student' => $student,
            'totalXp' => $totalXp,
            'badges' => $badges,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function showTwoFactorForm(Request $request)
    {
        $user = $request->user();
        $secret = $user->two_factor_secret;

        return view('profile.two-factor', [
            'user' => $user,
            'qrCodeUrl' => $secret ? app(\PragmaRX\Google2FAQRCode\Google2FA::class)
                                    ->getQRCodeUrl(config('app.name'), $user->email, decrypt($secret))
                                  : null,
        ]);
    }

    public function enableTwoFactor(Request $request): RedirectResponse 
    {
        $provider = new TwoFactorAuthenticationProvider(new Google2FA());
        
        $request->user()->forceFill([
            'two_factor_secret' => encrypt($provider->generateSecretKey()),
            'two_factor_confirmed_at' => now(),
        ])->save();

        return back()->with('status', 'two-factor-authentication-enabled');
    }

    public function disableTwoFactor(Request $request): RedirectResponse
    {
        $request->user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null
        ])->save();

        return back()->with('status', 'two-factor-authentication-disabled');
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = \App\Models\User::find(session('2fa:user:id'));
        $google2fa = new \PragmaRX\Google2FA\Google2FA();

        if ($google2fa->verifyKey(decrypt($user->two_factor_secret), $request->code)) {
            Auth::login($user);
            session()->forget('2fa:user:id');

            return redirect()->intended(match($user->role) {
                'admin' => 'admin/dashboard',
                'instructor' => 'instructor/dashboard',
                'student' => 'students/dashboard',
                default => 'dashboard',
            });
        }

        return back()->withErrors(['code' => 'Invalid two-factor authentication code.']);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    public function updateLeaderboardPrivacy(Request $request): RedirectResponse
    {
        try {
            // Get the authenticated user and their student record
            $user = auth()->user();
            
            if (!$user || !$user->student) {
                return back()->withErrors(['error' => 'Student record not found.']);
            }

            $student = $user->student;
            
            // Update the hide_from_leaderboard flag based on checkbox presence
            $hideFromLeaderboard = $request->has('hide_from_leaderboard');
            
            $student->update([
                'hide_from_leaderboard' => $hideFromLeaderboard
            ]);

            // Provide user-friendly feedback
            $message = $hideFromLeaderboard 
                ? 'Your name is now hidden from the leaderboard.' 
                : 'Your name is now visible on the leaderboard.';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            // Log the error for debugging (keep this for production monitoring)
            \Log::error('Error updating leaderboard privacy:', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'An error occurred while updating your privacy setting. Please try again.']);
        }
    }
}



//code ni para reset sa code if ever na delete
// $user = \App\Models\User::where('email', 'admin@admin.com')->first();
// $user->two_factor_secret = null;
// $user->two_factor_confirmed_at = null;
// $user->save();
