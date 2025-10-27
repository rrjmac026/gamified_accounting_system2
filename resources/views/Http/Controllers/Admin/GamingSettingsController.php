<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GamificationSetting;
use Illuminate\Http\Request;

class GamificationSettingsController extends Controller
{
    public function edit()
    {
        $settings = GamificationSetting::firstOrCreate([]);
        return view('admin.gamification-settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'min_passing_score' => 'required|integer|min:0|max:100',
            'xp_per_task' => 'required|integer|min:0',
            'level_up_threshold' => 'required|integer|min:0',
            'late_penalty' => 'required|integer|min:0',
            'bonus_for_perfect' => 'required|integer|min:0'
        ]);

        $settings = GamificationSetting::first();
        $settings->update($validated);

        return back()->with('success', 'Gamification settings updated successfully.');
    }
}

