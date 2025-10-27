<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 

use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Http\Request;

class SystemNotificationController extends Controller
{
    public function index()
    {
        $notifications = SystemNotification::with('user')->orderBy('created_at', 'desc')->get();
        return view('system-notifications.index', compact('notifications'));
    }

    public function create()
    {
        $users = User::all();
        return view('system-notifications.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,success,error',
            'expires_at' => 'nullable|date'
        ]);

        $validated['is_read'] = false;
        SystemNotification::create($validated);
        
        return redirect()->route('system-notifications.index')
            ->with('success', 'Notification created successfully');
    }

    public function show(SystemNotification $systemNotification)
    {
        return view('system-notifications.show', compact('systemNotification'));
    }

    public function edit(SystemNotification $systemNotification)
    {
        $users = User::all();
        return view('system-notifications.edit', compact('systemNotification', 'users'));
    }

    public function update(Request $request, SystemNotification $systemNotification)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,success,error',
            'expires_at' => 'nullable|date'
        ]);

        $systemNotification->update($validated);
        return redirect()->route('system-notifications.index')
            ->with('success', 'Notification updated successfully');
    }

    public function destroy(SystemNotification $systemNotification)
    {
        $systemNotification->delete();
        return redirect()->route('system-notifications.index')
            ->with('success', 'Notification deleted successfully');
    }

    public function markAsRead(SystemNotification $systemNotification)
    {
        $systemNotification->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return redirect()->back()->with('success', 'Notification marked as read');
    }
}
