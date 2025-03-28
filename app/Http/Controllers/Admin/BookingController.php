<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Notifications\BookingStatusChanged;

class BookingController extends Controller
{
    public function index(): View
    {
        $bookings = Booking::with(['equipment', 'user'])
            ->latest()
            ->paginate(10);
            
        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        return view('admin.bookings.show', compact('booking'));
    }

    public function pending(): View
    {
        $bookings = Booking::with(['user', 'equipment'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('admin.bookings.pending', compact('bookings'));
    }

    public function approve(Booking $booking): RedirectResponse
    {
        if (!$booking->equipment->isAvailableForPeriod($booking->start_date, $booking->end_date, $booking->id)) {
            return back()->with('error', 'Equipment is not available for this period.');
        }

        $booking->update(['status' => 'approved']);
        
        $booking->user->notify(new BookingStatusChanged($booking));
        
        return back()->with('success', 'Booking approved successfully.');
    }

    public function reject(Booking $booking): RedirectResponse
    {
        $booking->update(['status' => 'rejected']);
        
        $booking->user->notify(new BookingStatusChanged($booking));
        
        return back()->with('success', 'Booking rejected successfully.');
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        $booking->update(['status' => 'cancelled']);
        return back()->with('success', 'Booking has been cancelled.');
    }

    public function complete(Booking $booking): RedirectResponse
    {
        $booking->update(['status' => 'completed']);
        return back()->with('success', 'Booking has been marked as completed.');
    }

    public function return(Booking $booking): RedirectResponse
    {
        $booking->update([
            'status' => 'returned',
            'returned_at' => now()
        ]);
        
        // Make the equipment available again
        $booking->equipment->update(['status' => 'available']);

        return back()->with('success', 'Equipment has been returned successfully.');
    }

    public function history()
    {
        $bookings = Booking::latest()->paginate(10);
        return view('admin.bookings.history', compact('bookings'));
    }

    public function create()
    {
        return view('admin.bookings.create');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();
        
        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }

    public function approveReturn(Booking $booking)
    {
        $booking->update(['status' => 'completed']);
        
        // Update equipment status
        $booking->equipment->update([
            'status' => 'available',
            'condition' => $booking->return_condition
        ]);

        // Notify user
        $booking->user->notify(new BookingStatusChanged($booking));

        return back()->with('success', 'Return request approved successfully.');
    }
} 