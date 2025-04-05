<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Send messages to multiple recipients
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendBulk(Request $request)
    {
        // Debug: Log authentication status and request
        \Log::info('Message sendBulk called', [
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);
        
        $request->validate([
            'receiver_ids' => 'required|array|min:1',
            'receiver_ids.*' => 'exists:users,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        
        $senderId = Auth::id();
        $receiverIds = $request->input('receiver_ids');
        $subject = $request->input('subject');
        $content = $request->input('content');
        
        // Begin transaction to ensure all messages are sent
        DB::beginTransaction();
        
        try {
            foreach ($receiverIds as $receiverId) {
                Message::create([
                    'sender_id' => $senderId,
                    'receiver_id' => $receiverId,
                    'subject' => $subject,
                    'content' => $content,
                    'read_at' => null,
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Messages sent successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send messages: ' . $e->getMessage()
            ], 500);
        }
    }
} 