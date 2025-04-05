<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the user's notifications.
     */
    public function index(): View|JsonResponse
    {
        // Check if notifications table exists and has required columns
        if (!Schema::hasTable('notifications') || 
            !Schema::hasColumn('notifications', 'notifiable_type') || 
            !Schema::hasColumn('notifications', 'notifiable_id')) {
            
            // If this is an API request, return empty array as JSON
            if (request()->expectsJson()) {
                return response()->json([]);
            }
            
            return view('notifications.index', ['notifications' => collect([])]);
        }
        
        $notifications = auth()->user()->notifications()->paginate(10);
        
        // If this is an API request, return the notifications as JSON
        if (request()->expectsJson()) {
            // Transform notifications into a more frontend-friendly format
            $formattedNotifications = $notifications->getCollection()->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => isset($notification->data['title']) 
                        ? $notification->data['title'] 
                        : (isset($notification->data['equipment_name']) 
                            ? 'Booking Update: ' . $notification->data['equipment_name']
                            : 'Notification'),
                    'message' => isset($notification->data['message']) 
                        ? $notification->data['message'] 
                        : (isset($notification->data['status']) 
                            ? 'Your booking has been ' . $notification->data['status']
                            : 'You have a new notification'),
                    'time' => $notification->created_at->diffForHumans(),
                    'read_at' => $notification->read_at,
                    'url' => isset($notification->data['url']) ? $notification->data['url'] : null,
                ];
            });
            
            return response()->json($formattedNotifications);
        }

        return view('notifications.index', [
            'notifications' => $notifications
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $id): RedirectResponse|JsonResponse
    {
        auth()->user()->notifications()->findOrFail($id)->markAsRead();
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse|JsonResponse
    {
        auth()->user()->unreadNotifications->markAsRead();
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'All notifications marked as read');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse|JsonResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back();
    }

    public function destroyAll(): RedirectResponse|JsonResponse
    {
        auth()->user()->notifications()->delete();
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back();
    }
    
    /**
     * Get unread notification count
     */
    public function getUnreadCount(): JsonResponse
    {
        $count = auth()->user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }
}
