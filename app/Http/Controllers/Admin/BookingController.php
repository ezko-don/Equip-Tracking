<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Notifications\BookingStatusChanged;
use App\Models\Equipment;
use Illuminate\Support\Facades\DB;

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

    public function approve(Request $request, Booking $booking): RedirectResponse
    {
        if (!$booking->equipment->isAvailableForPeriod($booking->start_time, $booking->end_time, $booking->id)) {
            return back()->with('error', 'Equipment is not available for this period.');
        }

        $request->validate([
            'equipment_condition' => 'required|in:good,damaged,needs_maintenance',
            'equipment_notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Update booking status
            $booking->approve();
            
            // Update equipment condition and status
            $equipment = $booking->equipment;
            $equipment->condition = $request->equipment_condition;
            
            // If equipment is damaged or needs maintenance, update its status accordingly
            if ($request->equipment_condition !== 'good') {
                $equipment->status = match($request->equipment_condition) {
                    'damaged' => 'damaged',
                    'needs_maintenance' => 'maintenance',
                    default => $equipment->status
                };
                
                // Create maintenance record if needed
                if ($request->equipment_condition === 'needs_maintenance') {
                    $equipment->maintenanceLogs()->create([
                        'type' => 'maintenance',
                        'description' => 'Maintenance needed before lending. ' . $request->equipment_notes,
                        'scheduled_date' => now(),
                        'status' => 'pending'
                    ]);
                }
            }
            
            // Save equipment condition notes
            $booking->update([
                'initial_condition' => $request->equipment_condition,
                'initial_notes' => $request->equipment_notes
            ]);
            
            $equipment->save();
            
            DB::commit();
            
            $booking->user->notify(new BookingStatusChanged($booking));
            
            return back()->with('success', 'Booking approved successfully with equipment condition update.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve booking: ' . $e->getMessage());
        }
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

    public function return(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate([
            'equipment_condition' => 'required|in:good,damaged,needs_maintenance',
            'return_notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Update the booking status
            $booking->update([
                'status' => 'completed',
                'returned_at' => now(),
                'return_condition' => $request->equipment_condition,
                'return_notes' => $request->return_notes
            ]);

            // Update equipment status based on return condition
            $equipment = $booking->equipment;
            $equipment->status = match($request->equipment_condition) {
                'good' => 'available',
                'damaged' => 'damaged',
                'needs_maintenance' => 'maintenance',
                default => 'available'
            };
            $equipment->save();

            // If equipment needs maintenance, create a maintenance record
            if ($request->equipment_condition === 'needs_maintenance') {
                $equipment->maintenanceLogs()->create([
                    'type' => 'maintenance',
                    'description' => 'Maintenance needed after return. ' . $request->return_notes,
                    'scheduled_date' => now(),
                    'status' => 'pending'
                ]);
            }

            DB::commit();
            return back()->with('success', 'Equipment has been returned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process equipment return: ' . $e->getMessage());
        }
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

    public function approveReturn(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate([
            'equipment_condition' => 'required|in:good,damaged,needs_maintenance',
            'equipment_notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();
            
            // Complete the booking
            $booking->complete();
            
            // Update equipment condition and status
            $equipment = $booking->equipment;
            $equipment->condition = $request->equipment_condition;
            
            // If equipment is damaged or needs maintenance, update its status accordingly
            if ($request->equipment_condition !== 'good') {
                $equipment->status = match($request->equipment_condition) {
                    'damaged' => 'damaged',
                    'needs_maintenance' => 'maintenance',
                    default => $equipment->status
                };
                
                // Create maintenance record if needed
                if ($request->equipment_condition === 'needs_maintenance') {
                    $equipment->maintenanceLogs()->create([
                        'type' => 'maintenance',
                        'description' => 'Maintenance needed after return. ' . $request->equipment_notes,
                        'scheduled_date' => now(),
                        'status' => 'pending'
                    ]);
                }
            }
            
            // Update return info
            $booking->update([
                'return_condition' => $request->equipment_condition,
                'return_notes' => $request->equipment_notes,
                'returned_at' => now()
            ]);
            
            $equipment->save();
            
            DB::commit();
            
            return redirect()->route('admin.bookings.pending-returns')
                ->with('success', 'Equipment return processed successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process return: ' . $e->getMessage());
        }
    }

    public function pendingReturns()
    {
        $bookings = Booking::with(['user', 'equipment'])
            ->where('status', 'pending_return')
            ->latest()
            ->paginate(10);

        return view('admin.bookings.pending-returns', compact('bookings'));
    }
} 