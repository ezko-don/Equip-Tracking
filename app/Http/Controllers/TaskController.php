<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(): View
    {
        $equipment = Equipment::all();
        return view('tasks.index', compact('equipment'));
    }

    public function create(): View
    {
        $equipment = Equipment::where('status', 'available')->get();
        return view('tasks.create', compact('equipment'));
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'equipment' => 'nullable|array',
            'equipment.*' => 'exists:equipment,id'
        ]);

        $task = Auth::user()->tasks()->create([
            'name' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'],
            'priority' => $validated['priority'],
            'status' => $validated['status']
        ]);

        if (isset($validated['equipment'])) {
            $task->equipment()->attach($validated['equipment']);
        }

        $task->load('equipment');

        return response()->json([
            'id' => $task->id,
            'title' => $task->name,
            'description' => $task->description,
            'start' => $task->due_date->toISOString(),
            'priority' => $task->priority,
            'status' => $task->status,
            'equipment' => $task->equipment
        ]);
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $equipment = Equipment::where('status', 'available')->get();
        $taskEquipment = $task->equipment->pluck('id')->toArray();
        
        return view('tasks.edit', [
            'task' => $task,
            'equipment' => $equipment,
            'taskEquipment' => $taskEquipment
        ]);
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'equipment' => 'nullable|array',
            'equipment.*' => 'exists:equipment,id'
        ]);

        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'],
            'priority' => $validated['priority'],
            'status' => $validated['status']
        ]);

        // Sync equipment
        if (isset($validated['equipment'])) {
            $task->equipment()->sync($validated['equipment']);
        } else {
            $task->equipment()->detach();
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        $task->equipment()->detach();
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        $task->update($validated);

        return back()->with('success', 'Task status updated successfully.');
    }

    public function calendar()
    {
        $tasks = Auth::user()->tasks()
            ->with('equipment')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->name,
                    'start' => $task->due_date,
                    'end' => $task->due_date,
                    'extendedProps' => [
                        'equipment' => $task->equipment->pluck('id')->toArray()
                    ]
                ];
            });

        return response()->json($tasks);
    }

    private function getEventClassName($priority, $status)
    {
        $classes = [];

        // Priority classes
        $priorityClasses = [
            'low' => 'bg-blue-500',
            'medium' => 'bg-yellow-500',
            'high' => 'bg-red-500'
        ];

        // Status classes
        $statusClasses = [
            'pending' => 'opacity-90',
            'in_progress' => 'opacity-75',
            'completed' => 'opacity-50'
        ];

        $classes[] = $priorityClasses[$priority] ?? 'bg-gray-500';
        $classes[] = $statusClasses[$status] ?? '';
        $classes[] = 'text-white rounded-lg p-2';

        return implode(' ', $classes);
    }
} 