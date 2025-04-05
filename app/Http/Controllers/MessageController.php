<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index()
    {
        // Check if user is admin and show admin view if necessary
        if (Auth::user()->isAdmin()) {
            $users = User::where('role', '!=', 'admin')->orderBy('name')->get();
            return view('messages.index', ['adminView' => true, 'allUsers' => $users]);
        }
        
        return view('messages.index');
    }

    public function showConversation(User $user)
    {
        // Only admins can directly access user conversations
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        // Get all messages between the admin and this user
        $messages = Message::where(function ($query) use ($user) {
                $query->where('sender_id', Auth::id())
                      ->where('receiver_id', $user->id);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();
            
        // Mark messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return view('messages.conversation', [
            'user' => $user,
            'messages' => $messages
        ]);
    }

    public function searchUsers(Request $request)
    {
        $query = $request->input('query');
        $users = User::where('id', '!=', Auth::id())
            ->when($query, function ($q) use ($query) {
                return $q->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return response()->json($users);
    }

    public function getMessages(Request $request)
    {
        $userId = $request->input('user_id');
        
        $messages = Message::where(function ($query) use ($userId) {
                $query->where('sender_id', Auth::id())
                      ->where('receiver_id', $userId);
            })
            ->orWhere(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'content' => 'required|string|max:1000',
            ]);

            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'content' => $request->content,
            ]);

            // Load the relationships for the response
            $message->load(['sender', 'receiver']);

            return response()->json($message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->count();
        
        return response()->json(['count' => $count]);
    }

    public function markAsRead(Request $request)
    {
        $messages = Message::where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Send messages to multiple recipients (web form version)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        $request->validate([
            'receiver_ids' => 'required|array',
            'receiver_ids.*' => 'exists:users,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->receiver_ids as $receiverId) {
                Message::create([
                    'sender_id' => auth()->id(),
                    'receiver_id' => $receiverId,
                    'subject' => $request->subject,
                    'content' => $request->content,
                    'read' => false
                ]);
            }

            DB::commit();

            return redirect()->route('messages.index')
                ->with('success', 'Message sent successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('messages.index')
                ->with('error', 'Failed to send message. Please try again.');
        }
    }

    /**
     * Show user's received messages (inbox)
     *
     * @return \Illuminate\Http\Response
     */
    public function inbox()
    {
        $messages = Message::where('receiver_id', auth()->id())
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('messages.inbox', compact('messages'));
    }
    
    /**
     * Show a single message and allow reply
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        // Check if user is authorized to view this message
        if ($message->receiver_id != auth()->id() && $message->sender_id != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Mark as read if user is the receiver
        if ($message->receiver_id == auth()->id() && !$message->read_at) {
            $message->read_at = now();
            $message->save();
        }
        
        return view('messages.show', compact('message'));
    }
    
    /**
     * Reply to a message
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function reply(Request $request, Message $message)
    {
        // Check if user is authorized to reply to this message
        if ($message->receiver_id != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        
        // Create the reply message
        $reply = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $message->sender_id,
            'subject' => 'Re: ' . $message->subject,
            'content' => $request->content,
            'read' => false
        ]);
        
        return redirect()->route('messages.show', $reply->id)
            ->with('success', 'Reply sent successfully.');
    }
} 