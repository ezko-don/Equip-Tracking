<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class EquipmentController extends Controller
{
    public function index(): View
    {
        $equipment = Equipment::with('category')->latest()->paginate(10);
        return view('admin.equipment.index', compact('equipment'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('admin.equipment.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:available,unavailable',
            'condition' => 'required|in:new,good,fair,poor',
            'quantity' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('equipment', 'public');
            $validated['image'] = $path;
        }

        Equipment::create($validated);

        return redirect()->route('admin.equipment.index')
            ->with('success', 'Equipment created successfully.');
    }

    public function show(Equipment $equipment): View
    {
        return view('admin.equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment): View
    {
        $categories = Category::all();
        return view('admin.equipment.edit', compact('equipment', 'categories'));
    }

    public function update(Request $request, Equipment $equipment): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:available,unavailable',
            'condition' => 'required|in:new,good,fair,poor',
            'quantity' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048'
        ]);

        // Remove image from validated data if no new image is uploaded
        if (!$request->hasFile('image')) {
            unset($validated['image']);
        } else {
            // Delete old image if it exists
            if ($equipment->image) {
                Storage::disk('public')->delete($equipment->image);
            }
            // Store new image
            $path = $request->file('image')->store('equipment', 'public');
            $validated['image'] = $path;
        }

        $equipment->update($validated);

        return redirect()->route('admin.equipment.index')
            ->with('success', 'Equipment updated successfully.');
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        // Delete the image if it exists
        if ($equipment->image) {
            Storage::disk('public')->delete($equipment->image);
        }
        
        $equipment->delete();
        return redirect()->route('admin.equipment.index')
            ->with('success', 'Equipment deleted successfully.');
    }

    public function updateStatus(Request $request, Equipment $equipment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:available,unavailable'
        ]);

        $equipment->update($validated);

        return back()->with('success', 'Equipment status updated successfully.');
    }

    public function updateCondition(Request $request, Equipment $equipment): RedirectResponse
    {
        $validated = $request->validate([
            'condition' => 'required|in:new,good,fair,poor'
        ]);

        $equipment->update($validated);

        return back()->with('success', 'Equipment condition updated successfully.');
    }
} 