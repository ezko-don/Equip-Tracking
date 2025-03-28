<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Equipment;
use App\Models\Booking;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard with summary statistics.
     */
    public function dashboard(): View
    {
        // Get total counts
        $totalUsers = User::where('role', '!=', 'admin')->count();
        $totalEquipment = Equipment::count();
        $totalBookings = Booking::count();
        $activeBookings = Booking::where('status', 'active')->count();
        
        // Get equipment status counts
        $damagedEquipment = Equipment::where('condition', 'damaged')->count();
        $underRepairEquipment = Equipment::where('condition', 'under_repair')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        
        // Get recent bookings
        $recentBookings = Booking::with(['user', 'equipment'])
            ->latest()
            ->take(5)
            ->get();

        // Get recent users
        $recentUsers = User::latest()
            ->take(5)
            ->get();
            
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalEquipment',
            'totalBookings',
            'activeBookings',
            'damagedEquipment',
            'underRepairEquipment',
            'pendingBookings',
            'recentBookings',
            'recentUsers'
        ));
    }

    /**
     * Show the reports page with various statistics.
     */
    public function reports(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_equipment' => Equipment::count(),
            'total_bookings' => Booking::count(),
            'active_bookings' => Booking::where('status', 'active')->count(),
        ];

        $recent_bookings = Booking::with(['user', 'equipment'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.reports', compact('stats', 'recent_bookings'));
    }

    /**
     * Show the user management page.
     */
    public function users(): View
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Display admin management page
     */
    public function admins(): View
    {
        $admins = User::where('role', 'admin')
            ->latest()
            ->paginate(10);
        return view('admin.users.admins', compact('admins'));
    }

    /**
     * Show form to create new admin
     */
    public function createAdmin(): View
    {
        return view('admin.users.create-admin');
    }

    /**
     * Store new admin user
     */
    public function storeAdmin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['role'] = 'admin';
        $user = User::create($validated);

        return redirect()->route('admin.users.admins')
            ->with('success', 'Admin user created successfully.');
    }

    /**
     * Remove admin privileges
     */
    public function removeAdmin(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot remove your own admin privileges.');
        }

        $user->update(['role' => 'user']);

        return back()->with('success', 'Admin privileges removed successfully.');
    }

    /**
     * Update user role.
     */
    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'is_admin' => 'required|boolean',
        ]);

        $user->update([
            'is_admin' => $request->is_admin,
        ]);

        return back()->with('success', 'User role updated successfully.');
    }

    /**
     * Show equipment returns pending approval.
     */
    public function returns(): View
    {
        $pendingReturns = Booking::with(['user', 'equipment'])
            ->where('status', 'pending_return')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.returns', compact('pendingReturns'));
    }

    /**
     * Approve equipment return.
     */
    public function approveReturn(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'condition' => ['required', 'string', 'in:good,damaged,under_repair'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $booking->equipment->update([
            'condition' => $validated['condition'],
            'is_active' => $validated['condition'] === 'good',
        ]);

        $booking->update([
            'status' => 'completed',
            'actual_return_date' => now(),
            'return_notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Equipment return approved successfully.');
    }

    /**
     * Reject equipment return.
     */
    public function rejectReturn(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:500'],
        ]);

        $booking->update([
            'status' => 'active',
            'return_notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Equipment return rejected.');
    }
} 