<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Equipment;
use App\Models\User;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'due_date' => 'required|date',
            'priority' => 'required|in:low,medium,high',
            'equipment_id' => 'nullable|exists:equipment,id',
        ]);

        $task = Task::create([
            ...$validated,
            'status' => 'pending',
            'created_by' => Auth::id(),
            'assigned_to' => Auth::id(), // Assign to self by default
        ]);

        return redirect()->back()->with('success', 'Task created successfully');
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        // Check if this is an admin context or user context
        if (request()->is('admin*')) {
            $users = User::all();
            $equipment = Equipment::all();
            return view('admin.tasks.edit', compact('task', 'users', 'equipment'));
        } else {
            $equipment = Equipment::where('status', 'available')->get();
            $taskEquipment = $task->equipment->pluck('id')->toArray();
            
            return view('tasks.edit', [
                'task' => $task,
                'equipment' => $equipment,
                'taskEquipment' => $taskEquipment
            ]);
        }
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        // Check if this is an admin context or user context
        if (request()->is('admin*')) {
            // Admin update - validate all fields
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'required|date',
                'priority' => 'required|in:low,medium,high',
                'status' => 'required|in:pending,in_progress,completed',
                'equipment_id' => 'nullable|exists:equipment,id',
                'assigned_to' => 'nullable|exists:users,id',
            ]);

            $task->update($validated);
            return redirect()->route('admin.tasks.index')->with('success', 'Task updated successfully');
        } else {
            // User update - only allow status update
            $validated = $request->validate([
                'status' => 'required|in:pending,in_progress,completed',
            ]);

            $task->update($validated);
            return redirect()->back()->with('success', 'Task status updated successfully');
        }
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back()->with('success', 'Task deleted successfully');
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
                $className = $this->getEventClassName($task->priority, $task->status);
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'start' => $task->due_date->format('Y-m-d\TH:i:s'),
                    'end' => $task->due_date->format('Y-m-d\TH:i:s'),
                    'allDay' => false,
                    'className' => $className,
                    'extendedProps' => [
                        'description' => $task->description,
                        'location' => $task->location,
                        'priority' => $task->priority,
                        'status' => $task->status,
                        'equipment' => $task->equipment ? $task->equipment->name : null
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
    
    /**
     * Display a listing of all tasks for admin view.
     */
    public function adminIndex()
    {
        $tasks = Task::with(['assignedUser', 'creator', 'equipment'])
                     ->latest()
                     ->paginate(10);
                     
        return view('admin.tasks.index', compact('tasks'));
    }
    
    public function adminCalendar()
    {
        return view('admin.tasks.calendar');
    }
    
    public function calendarData()
    {
        $tasks = Task::with(['assignedUser', 'creator', 'equipment'])->get();
        $events = [];
        
        foreach ($tasks as $task) {
            $events[] = [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->due_date ? $task->due_date->format('Y-m-d\TH:i:s') : null,
                'allDay' => false,
                'className' => $this->getEventClassName($task->priority, $task->status),
                'description' => $task->description,
                'priority' => $task->priority,
                'status' => $task->status,
                'location' => $task->location,
                'assigned_to_name' => $task->assignedUser ? $task->assignedUser->name : null,
                'created_by_name' => $task->creator ? $task->creator->name : null,
                'equipment_name' => $task->equipment ? $task->equipment->name : null,
                'edit_url' => route('admin.tasks.edit', $task),
                'delete_url' => route('admin.tasks.destroy', $task),
            ];
        }
        
        return response()->json($events);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return view('admin.tasks.show', compact('task'));
    }
} 