<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
        
        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function create()
    {
        abort_unless(Auth::user()->hasPermission('send-notifications'), 403);

        $users = User::orderBy('name')->get();

        return view('notifications.create', compact('users'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->hasPermission('send-notifications'), 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'send_to_all' => 'sometimes|boolean',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $sendToAll = $request->boolean('send_to_all');

        if ($sendToAll) {
            User::chunk(200, function ($users) use ($validated) {
                foreach ($users as $user) {
                    Notification::createNotification(
                        $user->id,
                        'in-app',
                        $validated['title'], 
                        $validated['message']
                    );
                }
            });
        } else {
            if (empty($validated['user_id'])) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['user_id' => 'Select a user or choose Send to all.']);
            }

            Notification::createNotification(
                $validated['user_id'],
                'in-app',
                $validated['title'],
                $validated['message']
            );
        }

        return redirect()->route('notifications.index')->with('success', 'Notification sent successfully.');
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();
        
        if ($notification) {
            $notification->update(['is_read' => true]);
        }
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }
}