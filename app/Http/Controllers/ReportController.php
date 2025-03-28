<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view-reports');
    }

    public function index()
    {
        return view('reports.index');
    }

    public function equipmentUtilization(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $equipment = Equipment::with(['bookings' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_time', [$startDate, $endDate])
                  ->where('status', 'completed');
        }])->get();

        $utilization = $equipment->map(function ($item) use ($startDate, $endDate) {
            $totalDays = $startDate->diffInDays($endDate) + 1;
            $bookedDays = $item->bookings->sum(function ($booking) {
                return $booking->start_time->diffInDays($booking->end_time) + 1;
            });
            $utilizationRate = ($bookedDays / $totalDays) * 100;

            return [
                'name' => $item->name,
                'category' => $item->category->name,
                'total_days' => $totalDays,
                'booked_days' => $bookedDays,
                'utilization_rate' => round($utilizationRate, 2),
                'bookings_count' => $item->bookings->count(),
            ];
        });

        return view('reports.equipment-utilization', compact('utilization', 'startDate', 'endDate'));
    }

    public function bookingStatistics(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $statistics = Booking::whereBetween('start_time', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(*) as total_bookings'),
                DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_bookings'),
                DB::raw('COUNT(CASE WHEN status = "cancelled" THEN 1 END) as cancelled_bookings'),
                DB::raw('COUNT(CASE WHEN status = "rejected" THEN 1 END) as rejected_bookings'),
                DB::raw('COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_bookings')
            )
            ->first();

        $monthlyBookings = Booking::whereBetween('start_time', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(start_time, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed'),
                DB::raw('COUNT(CASE WHEN status = "cancelled" THEN 1 END) as cancelled')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('reports.booking-statistics', compact('statistics', 'monthlyBookings', 'startDate', 'endDate'));
    }

    public function equipmentCondition(Request $request)
    {
        $equipment = Equipment::with('category')
            ->select(
                'condition',
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(CASE WHEN status = "available" THEN 1 END) as available'),
                DB::raw('COUNT(CASE WHEN status = "in_use" THEN 1 END) as in_use'),
                DB::raw('COUNT(CASE WHEN status = "maintenance" THEN 1 END) as maintenance'),
                DB::raw('COUNT(CASE WHEN status = "unavailable" THEN 1 END) as unavailable')
            )
            ->groupBy('condition')
            ->get();

        $categoryStats = Equipment::with('category')
            ->select(
                'category_id',
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(CASE WHEN condition = "new" THEN 1 END) as new'),
                DB::raw('COUNT(CASE WHEN condition = "good" THEN 1 END) as good'),
                DB::raw('COUNT(CASE WHEN condition = "fair" THEN 1 END) as fair'),
                DB::raw('COUNT(CASE WHEN condition = "poor" THEN 1 END) as poor')
            )
            ->groupBy('category_id')
            ->get();

        return view('reports.equipment-condition', compact('equipment', 'categoryStats'));
    }

    public function exportEquipmentUtilization(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $utilization = Equipment::with(['bookings' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_time', [$startDate, $endDate])
                  ->where('status', 'completed');
        }])->get()
        ->map(function ($item) use ($startDate, $endDate) {
            $totalDays = $startDate->diffInDays($endDate) + 1;
            $bookedDays = $item->bookings->sum(function ($booking) {
                return $booking->start_time->diffInDays($booking->end_time) + 1;
            });
            $utilizationRate = ($bookedDays / $totalDays) * 100;

            return [
                'Equipment Name' => $item->name,
                'Category' => $item->category->name,
                'Total Days' => $totalDays,
                'Booked Days' => $bookedDays,
                'Utilization Rate (%)' => round($utilizationRate, 2),
                'Number of Bookings' => $item->bookings->count(),
            ];
        });

        return response()->json($utilization);
    }

    public function exportBookingStatistics(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $statistics = Booking::whereBetween('start_time', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(start_time, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total_bookings'),
                DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_bookings'),
                DB::raw('COUNT(CASE WHEN status = "cancelled" THEN 1 END) as cancelled_bookings'),
                DB::raw('COUNT(CASE WHEN status = "rejected" THEN 1 END) as rejected_bookings'),
                DB::raw('COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_bookings')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($statistics);
    }

    public function exportEquipmentCondition()
    {
        $equipment = Equipment::with('category')
            ->select(
                'condition',
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(CASE WHEN status = "available" THEN 1 END) as available'),
                DB::raw('COUNT(CASE WHEN status = "in_use" THEN 1 END) as in_use'),
                DB::raw('COUNT(CASE WHEN status = "maintenance" THEN 1 END) as maintenance'),
                DB::raw('COUNT(CASE WHEN status = "unavailable" THEN 1 END) as unavailable')
            )
            ->groupBy('condition')
            ->get();

        return response()->json($equipment);
    }
} 