<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChatNotificationComponent extends Component
{
    public $unreadMessages;
    public $unreadNotifications;

    public function __construct()
    {
        $user = auth()->user();
        
        try {
            // Check if user_chats table exists and is accessible
            if (Schema::hasTable('user_chats') && $user) {
                $this->unreadMessages = DB::table('user_chats')
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->count();
            } else {
                $this->unreadMessages = 0;
            }
            
            // Check if notifications table exists and is accessible
            if (Schema::hasTable('notifications') && $user) {
                $this->unreadNotifications = $user->unreadNotifications()->count();
            } else {
                $this->unreadNotifications = 0;
            }
        } catch (\Exception $e) {
            // Fallback to zero if any database errors occur
            $this->unreadMessages = 0;
            $this->unreadNotifications = 0;
        }
    }

    public function render()
    {
        return view('components.chat-notification');
    }
} 