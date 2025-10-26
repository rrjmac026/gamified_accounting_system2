<?php

namespace App\Http\Controllers;

use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SystemNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show all notifications for authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 15);

        $query = SystemNotification::where('user_id', $user->id)
            ->where(function($q) {
                $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
            })
            ->orderBy('created_at', 'desc');

        if ($request->boolean('unread_only')) {
            $query->where('is_read', false);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Always paginate - this ensures we get a paginated result
        $notifications = $query->paginate($perPage);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead(SystemNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->systemNotifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification
     */
    public function destroy(SystemNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $notification->delete();

        return redirect()->back()->with('success', 'Notification deleted.');
    }

    /**
     * (Optional) Admin: send notification to all or role
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Admin only');
        }

        $request->validate([
            'user_id'   => 'nullable|exists:users,id',
            'user_role' => 'nullable|in:admin,instructor,student',
            'title'     => 'required|string|max:255',
            'message'   => 'required|string',
            'type'      => 'required|in:info,success,warning,error,announcement',
            'expires_at'=> 'nullable|date|after:now'
        ]);

        // If specific user
        if ($request->user_id) {
            SystemNotification::create([
                'user_id' => $request->user_id,
                'title'   => $request->title,
                'message' => $request->message,
                'type'    => $request->type,
                'expires_at' => $request->expires_at,
                'is_read' => false,
            ]);
        }
        // If role
        elseif ($request->user_role) {
            $users = User::where('role', $request->user_role)->pluck('id');
            foreach ($users as $id) {
                SystemNotification::create([
                    'user_id' => $id,
                    'title'   => $request->title,
                    'message' => $request->message,
                    'type'    => $request->type,
                    'expires_at' => $request->expires_at,
                    'is_read' => false,
                ]);
            }
        }
        // If no filter, send to all
        else {
            $users = User::pluck('id');
            foreach ($users as $id) {
                SystemNotification::create([
                    'user_id' => $id,
                    'title'   => $request->title,
                    'message' => $request->message,
                    'type'    => $request->type,
                    'expires_at' => $request->expires_at,
                    'is_read' => false,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Notification(s) sent successfully.');
    }
}