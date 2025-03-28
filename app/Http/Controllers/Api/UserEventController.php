<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;

class UserEventController extends Controller
{
    /**
     * Get the user's calendar events.
     */
    public function index(Request $request)
    {
        $bookings = Booking::where('user_id', $request->user()->id)
            ->with('equipment')
            ->get();

        return $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'title' => $booking->equipment->name,
                'start' => Carbon::parse($booking->start_date)->format('Y-m-d'),
                'end' => Carbon::parse($booking->end_date)->format('Y-m-d'),
                'backgroundColor' => $this->getStatusColor($booking->status),
                'borderColor' => $this->getStatusColor($booking->status),
            ];
        });
    }

    /**
     * Get the color for a booking status.
     */
    private function getStatusColor($status): string
    {
        return match ($status) {
            'pending' => '#FFA500',   // Orange
            'approved' => '#28A745',  // Green
            'rejected' => '#DC3545',  // Red
            'completed' => '#17A2B8', // Blue
            'cancelled' => '#6C757D', // Gray
            default => '#C41E3A',     // Strathmore Red
        };
    }
} 