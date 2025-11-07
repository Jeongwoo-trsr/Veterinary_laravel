<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display inbox messages
     */
    public function inbox(Request $request)
    {
        $query = Message::with('sender')
            ->where('receiver_id', Auth::id())
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('sender', function($senderQuery) use ($search) {
                      $senderQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $messages = $query->paginate(15);
        $unreadCount = Message::where('receiver_id', Auth::id())->unread()->count();

        return view('messages.inbox', compact('messages', 'unreadCount'));
    }

    /**
     * Display sent messages
     */
    public function sent()
    {
        $messages = Message::with('receiver')
            ->where('sender_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('messages.sent', compact('messages'));
    }

    /**
     * Show form to create new message
     */
    public function create()
    {
        $users = User::where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();

        return view('messages.create', compact('users'));
    }

    /**
     * Store a new message
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return redirect()->route('messages.sent')
            ->with('success', 'Message sent successfully!');
    }

    /**
     * Display a specific message
     */
    public function show(Message $message)
    {
        // Check if user is sender or receiver
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Mark as read if user is receiver
        if ($message->receiver_id === Auth::id()) {
            $message->markAsRead();
        }

        return view('messages.show', compact('message'));
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request)
    {
        $messageIds = $request->input('message_ids', []);
        
        Message::whereIn('id', $messageIds)
            ->where('receiver_id', Auth::id())
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'Messages marked as read');
    }

    /**
     * Mark messages as unread
     */
    public function markAsUnread(Request $request)
    {
        $messageIds = $request->input('message_ids', []);
        
        Message::whereIn('id', $messageIds)
            ->where('receiver_id', Auth::id())
            ->update([
                'is_read' => false,
                'read_at' => null,
            ]);

        return back()->with('success', 'Messages marked as unread');
    }

    /**
     * Delete messages
     */
    public function destroy(Request $request)
    {
        $messageIds = $request->input('message_ids', []);
        
        Message::whereIn('id', $messageIds)
            ->where(function($query) {
                $query->where('receiver_id', Auth::id())
                      ->orWhere('sender_id', Auth::id());
            })
            ->delete();

        return back()->with('success', 'Messages deleted successfully');
    }

    /**
     * Get unread message count (API endpoint)
     */
    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())->unread()->count();
        return response()->json(['count' => $count]);
    }
}
