<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Booking;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use PDF;

class ReportController extends Controller
{
    public function index(): View
    {
        $equipmentCount = Equipment::count();
        $bookingCount = Booking::count();
        $maintenanceCount = Maintenance::count();

        return view('admin.reports.index', compact('equipmentCount', 'bookingCount', 'maintenanceCount'));
    }

    public function equipmentUsage(): View
    {
        $equipment = Equipment::withCount('bookings')
            ->with(['category', 'maintenances'])
            ->get();

        return view('admin.reports.equipment-usage', compact('equipment'));
    }

    public function conditionHistory(): View
    {
        $equipment = Equipment::with(['maintenances'])
            ->get();

        return view('admin.reports.condition-history', compact('equipment'));
    }

    public function availability(): View
    {
        $equipment = Equipment::with(['bookings' => function ($query) {
            $query->where('status', 'approved');
        }])->get();

        return view('admin.reports.availability', compact('equipment'));
    }

    public function bookingStatistics(): View
    {
        $bookings = Booking::with(['equipment', 'user'])
            ->get();

        $statistics = [
            'total' => $bookings->count(),
            'pending' => $bookings->where('status', 'pending')->count(),
            'approved' => $bookings->where('status', 'approved')->count(),
            'rejected' => $bookings->where('status', 'rejected')->count(),
            'completed' => $bookings->where('status', 'completed')->count(),
        ];

        return view('admin.reports.booking-statistics', compact('statistics', 'bookings'));
    }

    public function generate(Request $request): RedirectResponse
    {
        $request->validate([
            'report_type' => 'required|in:equipment-usage,condition-history,availability,booking-statistics',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel'
        ]);

        $type = $request->report_type;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $format = $request->format;

        // Store report generation parameters in session
        session(['report_params' => [
            'type' => $type,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'format' => $format
        ]]);

        return redirect()->route('admin.reports.download', $type)
            ->with('success', 'Report generated successfully.');
    }

    public function download(string $type): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $params = session('report_params');
        
        if (!$params) {
            return back()->with('error', 'No report parameters found.');
        }

        $startDate = $params['start_date'];
        $endDate = $params['end_date'];
        $format = $params['format'];

        $data = $this->getReportData($type, $startDate, $endDate);
        $filename = "{$type}-report-" . now()->format('Y-m-d') . ".{$format}";

        if ($format === 'pdf') {
            $pdf = PDF::loadView("admin.reports.{$type}-pdf", $data);
            return $pdf->download($filename);
        } else {
            // Handle Excel export
            // You'll need to implement Excel export logic here
            return back()->with('error', 'Excel export not implemented yet.');
        }
    }

    private function getReportData(string $type, string $startDate, string $endDate): array
    {
        switch ($type) {
            case 'equipment-usage':
                return [
                    'equipment' => Equipment::withCount(['bookings' => function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('start_date', [$startDate, $endDate]);
                    }])->with(['category', 'maintenances'])->get(),
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ];
            case 'condition-history':
                return [
                    'equipment' => Equipment::with(['maintenances' => function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('maintenance_date', [$startDate, $endDate]);
                    }])->get(),
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ];
            case 'availability':
                return [
                    'equipment' => Equipment::with(['bookings' => function ($query) use ($startDate, $endDate) {
                        $query->where('status', 'approved')
                            ->whereBetween('start_date', [$startDate, $endDate]);
                    }])->get(),
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ];
            case 'booking-statistics':
                return [
                    'bookings' => Booking::with(['equipment', 'user'])
                        ->whereBetween('start_date', [$startDate, $endDate])
                        ->get(),
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ];
            default:
                return [];
        }
    }
} 