<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EquipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Only apply admin middleware to admin-specific actions
        $this->middleware('is_admin')->only([
            'index', // admin index
            'create', 
            'store', 
            'edit', 
            'update', 
            'destroy', 
            'updateStatus', 
            'updateCondition'
        ]);
    }

    /**
     * Display a listing of the equipment for admin.
     */
    public function index(): View
    {
        $equipment = Equipment::with(['category', 'bookings'])
            ->orderBy('name')
            ->paginate(10);
            
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        return view('admin.equipment.index', compact('equipment', 'categories'));
    }

    /**
     * Display a listing of the equipment for users.
     */
    public function userIndex(): View
    {
        $equipment = Equipment::with('category')
            ->where('status', 'available')
            ->orderBy('created_at', 'desc')
            ->paginate(9);
            
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        return view('equipment.index', compact('equipment', 'categories'));
    }

    /**
     * Show the form for creating new equipment.
     */
    public function create(): View
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        return view('admin.equipment.create', compact('categories'));
    }

    /**
     * Store a newly created equipment in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|in:good,damaged,under_repair',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('equipment', 'public');
        }

        Equipment::create($validated);

        return redirect()->route('admin.equipment')
            ->with('success', 'Equipment created successfully.');
    }

    /**
     * Display the specified equipment.
     */
    public function show(Equipment $equipment): View
    {
        return view('equipment.show', compact('equipment'));
    }

    /**
     * Display the specified equipment for users.
     */
    public function userShow(Equipment $equipment): View
    {
        if (!$equipment->is_active || $equipment->condition !== 'good') {
            return redirect()->route('equipment.index')
                ->with('error', 'This equipment is not available.');
        }

        $equipment->load(['category', 'bookings' => function ($query) {
            $query->where('status', 'active')
                  ->orWhere('status', 'pending');
        }]);

        return view('equipment.show', compact('equipment'));
    }

    /**
     * Show the form for editing the specified equipment.
     */
    public function edit(Equipment $equipment): View
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        return view('admin.equipment.edit', compact('equipment', 'categories'));
    }

    /**
     * Update the specified equipment in storage.
     */
    public function update(Request $request, Equipment $equipment): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|in:good,damaged,under_repair',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($equipment->image) {
                Storage::disk('public')->delete($equipment->image);
            }
            $validated['image'] = $request->file('image')->store('equipment', 'public');
        }

        $equipment->update($validated);

        return redirect()->route('admin.equipment')
            ->with('success', 'Equipment updated successfully.');
    }

    /**
     * Remove the specified equipment from storage.
     */
    public function destroy(Equipment $equipment): RedirectResponse
    {
        // Check if equipment has active bookings
        if ($equipment->bookings()->where('status', 'active')->exists()) {
            return back()->with('error', 'Cannot delete equipment with active bookings.');
        }

        // Delete image if exists
        if ($equipment->image) {
            Storage::disk('public')->delete($equipment->image);
        }

        $equipment->delete();

        return redirect()->route('admin.equipment')
            ->with('success', 'Equipment deleted successfully.');
    }

    /**
     * Update equipment status.
     */
    public function updateStatus(Request $request, Equipment $equipment): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $equipment->update($validated);

        return back()->with('success', 'Equipment status updated successfully.');
    }

    /**
     * Update equipment condition.
     */
    public function updateCondition(Request $request, Equipment $equipment): RedirectResponse
    {
        $validated = $request->validate([
            'condition' => 'required|in:good,damaged,under_repair',
        ]);

        $equipment->update($validated);

        return back()->with('success', 'Equipment condition updated successfully.');
    }
}
