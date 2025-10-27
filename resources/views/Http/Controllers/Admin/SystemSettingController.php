<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all();
        return view('system-settings.index', compact('settings'));
    }

    public function create()
    {
        $types = ['string', 'integer', 'boolean', 'json'];
        return view('system-settings.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:system_settings,key',
            'value' => 'required',
            'type' => 'required|in:string,integer,boolean,json',
            'description' => 'required|string',
            'is_editable' => 'required|boolean'
        ]);

        // Handle JSON type validation
        if ($validated['type'] === 'json') {
            json_decode($validated['value']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['value' => 'Invalid JSON format'])->withInput();
            }
        }

        SystemSetting::create($validated);
        return redirect()->route('system-settings.index')
            ->with('success', 'System setting created successfully');
    }

    public function show(SystemSetting $systemSetting)
    {
        return view('system-settings.show', compact('systemSetting'));
    }

    public function edit(SystemSetting $systemSetting)
    {
        if (!$systemSetting->is_editable) {
            return redirect()->route('system-settings.index')
                ->with('error', 'This setting cannot be edited');
        }
        $types = ['string', 'integer', 'boolean', 'json'];
        return view('system-settings.edit', compact('systemSetting', 'types'));
    }

    public function update(Request $request, SystemSetting $systemSetting)
    {
        if (!$systemSetting->is_editable) {
            return redirect()->route('system-settings.index')
                ->with('error', 'This setting cannot be edited');
        }

        $validated = $request->validate([
            'key' => 'required|string|unique:system_settings,key,' . $systemSetting->id,
            'value' => 'required',
            'type' => 'required|in:string,integer,boolean,json',
            'description' => 'required|string',
            'is_editable' => 'required|boolean'
        ]);

        // Handle JSON type validation
        if ($validated['type'] === 'json') {
            json_decode($validated['value']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['value' => 'Invalid JSON format'])->withInput();
            }
        }

        $systemSetting->update($validated);
        return redirect()->route('system-settings.index')
            ->with('success', 'System setting updated successfully');
    }

    public function destroy(SystemSetting $systemSetting)
    {
        if (!$systemSetting->is_editable) {
            return redirect()->route('system-settings.index')
                ->with('error', 'This setting cannot be deleted');
        }
        
        $systemSetting->delete();
        return redirect()->route('system-settings.index')
            ->with('success', 'System setting deleted successfully');
    }
}
