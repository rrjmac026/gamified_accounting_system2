<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Traits\Loggable;

class BadgeController extends Controller
{
    use Loggable;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $badges = Badge::paginate(10); 
        return view('admin.badges.index', compact('badges'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.badges.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon_type' => 'required|in:preset,upload',
            'preset_icon' => 'required_if:icon_type,preset|nullable|string',
            'icon' => 'required_if:icon_type,upload|nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'xp_threshold' => 'required|integer|min:0',
            'criteria' => 'required|in:achievement,skill,participation,milestone',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        
        // Handle icon based on selection type
        if ($request->input('icon_type') === 'preset' && $request->input('preset_icon')) {
            // Use preset icon path
            $validated['icon_path'] = 'badges/presets/' . $request->input('preset_icon');
        } elseif ($request->hasFile('icon')) {
            // Upload custom icon
            $path = $request->file('icon')->store('badges/custom', 'public');
            $validated['icon_path'] = $path;
        }

        // Remove temporary fields not in database
        unset($validated['icon_type'], $validated['preset_icon'], $validated['icon']);

        $badge = Badge::create($validated);

        $this->logActivity(
            "Created Badge",
            "Badge",
            $badge->id,
            [
                'name' => $badge->name,
                'type' => $badge->criteria,
                'xp_threshold' => $badge->xp_threshold
            ]
        );

        return redirect()
            ->route('admin.badges.index')
            ->with('success', 'Badge created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Badge $badge)
    {
        return view('admin.badges.show', compact('badge'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Badge $badge)
    {
        return view('admin.badges.edit', compact('badge'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Badge $badge)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon_type' => 'required|in:preset,upload',
            'preset_icon' => 'required_if:icon_type,preset|nullable|string',
            'icon' => 'required_if:icon_type,upload|nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'xp_threshold' => 'required|integer|min:0',
            'criteria' => 'required|in:achievement,skill,participation,milestone',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $originalData = $badge->toArray();
        
        // Handle icon based on selection type
        if ($request->input('icon_type') === 'preset' && $request->input('preset_icon')) {
            // Delete old custom icon if it exists and is not a preset
            if ($badge->icon_path && 
                !str_contains($badge->icon_path, 'badges/presets/') && 
                Storage::disk('public')->exists($badge->icon_path)) {
                Storage::disk('public')->delete($badge->icon_path);
            }
            // Use preset icon path
            $validated['icon_path'] = 'badges/presets/' . $request->input('preset_icon');
        } elseif ($request->hasFile('icon')) {
            // Delete old icon if exists and is not a preset
            if ($badge->icon_path && 
                !str_contains($badge->icon_path, 'badges/presets/') && 
                Storage::disk('public')->exists($badge->icon_path)) {
                Storage::disk('public')->delete($badge->icon_path);
            }
            
            // Store new custom icon
            $validated['icon_path'] = $request->file('icon')->store('badges/custom', 'public');
        }

        // Remove temporary fields not in database
        unset($validated['icon_type'], $validated['preset_icon'], $validated['icon']);

        $badge->update($validated);

        $this->logActivity(
            "Updated Badge",
            "Badge",
            $badge->id,
            [
                'original' => $originalData,
                'changes' => $badge->getChanges()
            ]
        );

        return redirect()
            ->route('admin.badges.index')
            ->with('success', 'Badge updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Badge $badge)
    {
        $badgeData = $badge->toArray();
        
        // Delete custom icon file if exists (don't delete presets)
        if ($badge->icon_path && 
            !str_contains($badge->icon_path, 'badges/presets/') && 
            Storage::disk('public')->exists($badge->icon_path)) {
            Storage::disk('public')->delete($badge->icon_path);
        }
        
        $badge->delete();

        $this->logActivity(
            "Deleted Badge",
            "Badge",
            $badge->id,
            [
                'name' => $badgeData['name'],
                'type' => $badgeData['criteria'],
                'xp_threshold' => $badgeData['xp_threshold']
            ]
        );

        return redirect()
            ->route('admin.badges.index')
            ->with('success', 'Badge deleted successfully!');
    }
}