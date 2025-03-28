<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Equipment;
use App\Models\Booking;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the dashboard.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        // If user is admin, redirect to admin dashboard
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // For regular users, show the user dashboard
        $user = Auth::user();
        
        return view('dashboard', [
            'bookings' => $user->bookings()->latest()->take(5)->get(),
            'pendingBookings' => $user->bookings()->where('status', 'pending')->count(),
            'activeBookings' => $user->bookings()->where('status', 'approved')->count(),
            'approvedBookings' => $user->bookings()->where('status', 'approved')->count(),
            'completedBookings' => $user->bookings()->where('status', 'completed')->count(),
            'totalBookings' => $user->bookings()->count(),
            'availableEquipment' => Equipment::where('status', 'available')->count(),
            'recentBookings' => $user->bookings()->with('equipment')->latest()->take(5)->get()
        ]);
    }
} 