<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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
            'maintenance_type' => 'required|in:routine,repair,inspection,upgrade',
            'description' => 'required|string|max:1000',
            'maintenance_date' => 'required|date',
            'cost' => 'nullable|numeric|min:0'
        ]);

        Maintenance::create($validated);

        return redirect()->route('admin.maintenances.index')
            ->with('success', 'Maintenance record created successfully.');
    }

    public function show(Maintenance $maintenance): View
    {
        return view('admin.maintenances.show', compact('maintenance'));
    }

    public function edit(Maintenance $maintenance): View
    {
        $equipment = Equipment::all();
        return view('admin.maintenances.edit', compact('maintenance', 'equipment'));
    }

    public function update(Request $request, Maintenance $maintenance): RedirectResponse
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'maintenance_date' => 'required|date',
            'performed_by' => 'required|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:scheduled,in-progress,completed',
            'notes' => 'nullable|string'
        ]);

        $maintenance->update($validated);

        return redirect()->route('admin.maintenances.index')
            ->with('success', 'Maintenance record updated successfully.');
    }

    public function destroy(Maintenance $maintenance): RedirectResponse
    {
        $maintenance->delete();

        return redirect()->route('admin.maintenances.index')
            ->with('success', 'Maintenance record deleted successfully.');
    }
} 