<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $notifications = auth()->user()
            ->unreadNotifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $this->getNotificationTitle($notification),
                    'message' => $this->getNotificationMessage($notification),
                    'time' => $notification->created_at->diffForHumans(),
                    'url' => $this->getNotificationUrl($notification),
                ];
            });

        return response()->json($notifications);
    }

    public function markAsRead(string $id): JsonResponse
    {
        auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first()
            ?->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(): JsonResponse
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $count = auth()->user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }

    private function getNotificationTitle($notification): string
    {
        return match ($notification->type) {
            'App\Notifications\NewBookingRequest' => 'New Booking Request',
            'App\Notifications\BookingStatusChanged' => 'Booking Status Update',
            default => 'Notification'
        };
    }

    private function getNotificationMessage($notification): string
    {
        $data = $notification->data;
        return match ($notification->type) {
            'App\Notifications\NewBookingRequest' => 
                "New booking request for {$data['equipment_name']} by {$data['user_name']}",
            'App\Notifications\BookingStatusChanged' => 
                "Booking for {$data['equipment_name']} has been {$data['status']}",
            default => $data['message'] ?? 'You have a new notification'
        };
    }

    private function getNotificationUrl($notification): string
    {
        $data = $notification->data;
        return match ($notification->type) {
            'App\Notifications\NewBookingRequest', 
            'App\Notifications\BookingStatusChanged' => 
                route('admin.bookings.show', $data['booking_id']),
            default => '#'
        };
    }
} 