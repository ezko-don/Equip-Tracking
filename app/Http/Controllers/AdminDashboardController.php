<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        // Get total counts
        $totalEquipment = Equipment::count();
        $availableEquipment = Equipment::where('is_active', true)->count();
        $maintenanceEquipment = Equipment::where('condition', 'under_repair')->count();
        
        // Get recent equipment
        $recentEquipment = Equipment::with('category')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalEquipment',
            'availableEquipment',
            'maintenanceEquipment',
            'recentEquipment'
        ));
    }
} 