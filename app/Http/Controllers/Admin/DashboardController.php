<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\Booking;
use App\Models\User;
use App\Models\Category;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'is_admin']);
    }

    public function index(): View
    {
        // Get statistics
        $totalEquipment = Equipment::count();
        $availableEquipment = Equipment::where('status', 'available')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $totalUsers = User::where('role', '!=', 'admin')->count();

        // Get recent bookings
        $recentBookings = Booking::with(['user', 'equipment'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalEquipment',
            'availableEquipment',
            'pendingBookings',
            'totalUsers',
            'recentBookings'
        ));
    }
} 