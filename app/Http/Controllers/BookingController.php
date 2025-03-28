<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\BookingStatusChanged;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewBookingRequest;
use App\Notifications\BookingCreated;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = auth()->user()->bookings()
            ->with('equipment')
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(?Equipment $equipment = null): View
    {
        $equipmentList = $equipment ? collect([$equipment]) : Equipment::where('status', '=', Equipment::STATUS_AVAILABLE)->get();
        $selectedEquipment = $equipment ?? ($equipmentList->first() ?? null);
        
        return view('bookings.create', [
            'equipment' => $selectedEquipment,
            'equipmentList' => $equipmentList
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Debug log the raw request
            \Log::info('Raw booking request:', $request->all());

            $validated = $request->validate([
                'equipment_id' => 'required|exists:equipment,id',
                'event_name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'start_time' => 'required|date_format:Y-m-d H:i',
                'end_time' => 'required|date_format:Y-m-d H:i|after:start_time',
                'purpose' => 'required|string|max:1000'
            ]);

            // Check if equipment exists and is available
            $equipment = Equipment::findOrFail($validated['equipment_id']);
            
            // Parse dates
            $startTime = Carbon::createFromFormat('Y-m-d H:i', $validated['start_time']);
            $endTime = Carbon::createFromFormat('Y-m-d H:i', $validated['end_time']);

            // Check for conflicting bookings
            $conflicting = Booking::where('equipment_id', $equipment->id)
                ->where('status', '!=', 'cancelled')
                ->where(function($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime])
                        ->orWhere(function($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<=', $startTime)
                              ->where('end_time', '>=', $endTime);
                        });
                })->exists();

            if ($conflicting) {
                throw ValidationException::withMessages([
                    'start_time' => ['The equipment is not available during this time period']
                ]);
            }

            // Create the booking
            $booking = DB::transaction(function () use ($validated, $startTime, $endTime, $equipment) {
                $booking = Booking::create([
                    'user_id' => auth()->id(),
                    'equipment_id' => $validated['equipment_id'],
                    'event_name' => $validated['event_name'],
                    'location' => $validated['location'],
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'purpose' => $validated['purpose'],
                    'status' => 'pending'
                ]);

                // Notify the user
                $booking->user->notify(new BookingCreated($booking));

                // Notify admins
                User::where('role', 'admin')->get()->each(function ($admin) use ($booking) {
                    $admin->notify(new NewBookingRequest($booking));
                });

                return $booking;
            });

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Booking created successfully',
                    'redirect' => route('equipment.index')
                ]);
            }

            return redirect()->route('equipment.index')
                ->with('success', 'Booking request submitted successfully.');

        } catch (ValidationException $e) {
            \Log::error('Validation error: ' . json_encode($e->errors()));
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Booking creation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create booking. Please try again.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        // Ensure users can only view their own bookings
        if ($booking->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        $this->authorize('update', $booking);
        $equipment = Equipment::all();
        return view('bookings.edit', compact('booking', 'equipment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'location' => 'required|string|max:255',
            'event_name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'notes' => 'nullable|string'
        ]);

        $booking->update($validated);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        try {
            $this->authorize('delete', $booking);
            
            DB::transaction(function () use ($booking) {
                // If the booking was approved, make the equipment available again
                if ($booking->isApproved()) {
                    $booking->equipment->update(['status' => 'available']);
                }
                
                // Delete the booking
                $booking->delete();
            });

            return redirect()->route('bookings.index')
                ->with('success', 'Booking deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete booking. Please try again.');
        }
    }

    public function approve(Booking $booking)
    {
        // Only admin can approve bookings
        if (!Auth::user()->isAdmin()) {
            return back()->with('error', 'You are not authorized to approve bookings.');
        }

        $booking->approve();

        // Create notification for user
        $admin = User::where('role', 'admin')->first();
        $admin->notify(new BookingStatusChanged($booking));

        return back()->with('success', 'Booking approved successfully.');
    }

    public function reject(Booking $booking)
    {
        // Only admin can reject bookings
        if (!Auth::user()->isAdmin()) {
            return back()->with('error', 'You are not authorized to reject bookings.');
        }

        $booking->reject();

        // Create notification for user
        $admin = User::where('role', 'admin')->first();
        $admin->notify(new BookingStatusChanged($booking));

        return back()->with('success', 'Booking rejected successfully.');
    }

    public function complete(Booking $booking)
    {
        // Only admin can mark bookings as complete
        if (!Auth::user()->isAdmin()) {
            return back()->with('error', 'You are not authorized to complete bookings.');
        }

        $booking->complete();

        return back()->with('success', 'Booking has been marked as completed successfully.');
    }

    /**
     * Check equipment availability for given time period
     */
    public function checkAvailability(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ]);

        $conflictingBookings = Booking::where('equipment_id', $equipment->id)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhere(function($query) use ($validated) {
                        $query->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                    });
            })->exists();

        return response()->json([
            'available' => !$conflictingBookings,
            'message' => !$conflictingBookings 
                ? 'Equipment is available for the selected time period.'
                : 'Equipment is not available for the selected time period.'
        ]);
    }

    public function cancel(Booking $booking)
    {
        try {
            if (!auth()->user()->can('cancel', $booking)) {
                return back()->with('error', 'You are not authorized to cancel this booking.');
            }

            DB::transaction(function () use ($booking) {
                $booking->cancel();
                
                // Notify the user if an admin is cancelling their booking
                if (auth()->user()->isAdmin() && auth()->id() !== $booking->user_id) {
                    $booking->user->notify(new BookingStatusChanged($booking));
                }
            });

            if (request()->wantsJson()) {
                return response()->json(['message' => 'Booking cancelled successfully']);
            }

            return back()->with('success', 'Booking has been cancelled successfully.');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Failed to cancel booking'], 500);
            }
            return back()->with('error', 'Failed to cancel booking. Please try again.');
        }
    }

    public function return(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'return_time' => 'required|date',
            'condition' => 'required|in:good,damaged',
            'notes' => 'nullable|string'
        ]);

        $booking = Booking::where('user_id', auth()->id())
            ->where('equipment_id', $validated['equipment_id'])
            ->where('status', 'approved')
            ->firstOrFail();

        $booking->update([
            'status' => 'pending_return',
            'return_time' => $validated['return_time'],
            'return_condition' => $validated['condition'],
            'return_notes' => $validated['notes']
        ]);

        // Notify admin
        $admin = User::where('role', 'admin')->first();
        $admin->notify(new BookingStatusChanged($booking));

        return redirect()->back()->with('success', 'Return request submitted successfully.');
    }

    /**
     * Remove all bookings for the authenticated user.
     */
    public function clearAll()
    {
        try {
            DB::transaction(function () {
                // Get all user's bookings
                $bookings = Booking::where('user_id', auth()->id())->get();
                
                if ($bookings->isEmpty()) {
                    return redirect()->route('bookings.index')
                        ->with('info', 'No bookings found to delete.');
                }
                
                foreach ($bookings as $booking) {
                    // Check if user can delete this booking
                    if (auth()->user()->can('delete', $booking)) {
                        // If the booking was approved, make the equipment available again
                        if ($booking->isApproved()) {
                            $booking->equipment->update(['status' => 'available']);
                        }
                        
                        // Delete the booking
                        $booking->delete();
                    }
                }
            });

            return redirect()->route('bookings.index')
                ->with('success', 'All your bookings have been deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error clearing bookings: ' . $e->getMessage());
            return redirect()->route('bookings.index')
                ->with('error', 'Failed to delete all bookings. Please try again.');
        }
    }
}
