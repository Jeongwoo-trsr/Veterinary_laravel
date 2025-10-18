<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        $notification->markAsRead();
        
        // Redirect to appointment if exists
        if ($notification->appointment_id) {
            return redirect()->route('appointments.show', $notification->appointment_id);
        }
        
        return back();
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        return back()->with('success', 'All notifications marked as read');
    }

    public function getUnreadCount()
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications()->count()
        ]);
    }
}