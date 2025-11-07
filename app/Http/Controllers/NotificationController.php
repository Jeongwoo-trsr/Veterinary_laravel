<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for the authenticated user
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read and redirect to related content
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        $notification->markAsRead();
        
        // Redirect to appointment if exists
        if ($notification->appointment_id) {
            $user = Auth::user();
            
            // Redirect based on user role
            if ($user->role === 'admin') {
                return redirect()->route('admin.appointments');
            } elseif ($user->role === 'doctor') {
                return redirect()->route('doctor.appointments');
            } else {
                return redirect()->route('pet-owner.appointments');
            }
        }
        
        return back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read for the authenticated user
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        
        return back()->with('success', 'All notifications marked as read');
    }

    /**
     * Get the count of unread notifications for the authenticated user
     */
    public function getUnreadCount()
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications()->count()
        ]);
    }
}