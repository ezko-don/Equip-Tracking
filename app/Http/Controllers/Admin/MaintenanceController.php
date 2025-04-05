<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    public function index(): View
    {
        $maintenances = Maintenance::with(['equipment'])
            ->latest()
            ->paginate(10);
            
        return view('admin.maintenances.index', compact('maintenances'));
    }

    public function create(): View
    {
        $equipment = Equipment::all();
        return view('admin.maintenances.create', compact('equipment'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'maintenance_type' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'maintenance_date' => 'required|date',
            'cost' => 'nullable|numeric|min:0',
            'receipt' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120' // 5MB max
        ]);

        $maintenanceData = [
            'equipment_id' => $validated['equipment_id'],
            'type' => $validated['maintenance_type'],
            'status' => 'completed', // Default status
            'description' => $validated['description'],
            'scheduled_date' => $validated['maintenance_date'],
            'completion_date' => $validated['maintenance_date'],
            'cost' => $validated['cost'] ?? 0,
            'performed_by' => auth()->id()
        ];

        // Handle receipt file upload
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('maintenance_receipts', $fileName, 'public');
            $maintenanceData['receipt_path'] = $filePath;
        }

        Maintenance::create($maintenanceData);

        return redirect()->route('admin.maintenances.index')
            ->with('success', 'Maintenance record created successfully.');
    }

    public function show(Maintenance $maintenance): View
    {
        $maintenance->load(['equipment', 'performer']);
        return view('admin.maintenances.show', compact('maintenance'));
    }

    public function edit(Maintenance $maintenance): View
    {
        $equipment = Equipment::all();
        $maintenance->load('performer');
        return view('admin.maintenances.edit', compact('maintenance', 'equipment'));
    }

    public function update(Request $request, Maintenance $maintenance): RedirectResponse
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'type' => 'required|string|max:255',
            'status' => 'required|in:scheduled,in-progress,completed',
            'description' => 'required|string|max:1000',
            'cost' => 'nullable|numeric|min:0',
            'maintenance_date' => 'required|date',
            'performed_by' => 'nullable|string',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120' // 5MB max
        ]);

        $maintenanceData = [
            'equipment_id' => $validated['equipment_id'],
            'type' => $validated['type'],
            'status' => $validated['status'],
            'description' => $validated['description'],
            'cost' => $validated['cost'] ?? 0,
            'scheduled_date' => $validated['maintenance_date'],
            'performed_by' => $validated['performed_by'] ?? auth()->id(),
            'notes' => $validated['notes'] ?? null,
        ];

        // Handle receipt file upload
        if ($request->hasFile('receipt')) {
            // Delete old receipt if exists
            if ($maintenance->receipt_path && \Storage::disk('public')->exists($maintenance->receipt_path)) {
                \Storage::disk('public')->delete($maintenance->receipt_path);
            }
            
            $file = $request->file('receipt');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('maintenance_receipts', $fileName, 'public');
            $maintenanceData['receipt_path'] = $filePath;
        }

        $maintenance->update($maintenanceData);

        return redirect()->route('admin.maintenances.index')
            ->with('success', 'Maintenance record updated successfully.');
    }

    public function destroy(Maintenance $maintenance): RedirectResponse
    {
        $maintenance->delete();

        return redirect()->route('admin.maintenances.index')
            ->with('success', 'Maintenance record deleted successfully.');
    }
    
    /**
     * Display maintenance history for a specific equipment.
     *
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\View\View
     */
    public function history(Equipment $equipment): View
    {
        $maintenanceRecords = Maintenance::where('equipment_id', $equipment->id)
            ->orderBy('scheduled_date', 'desc')
            ->paginate(10);
            
        return view('admin.maintenance.history', [
            'equipment' => $equipment,
            'maintenanceRecords' => $maintenanceRecords
        ]);
    }
} 