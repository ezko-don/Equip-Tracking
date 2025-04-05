@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Task Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.tasks.index') }}" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                Back to List
            </a>
            <a href="{{ route('admin.tasks.edit', $task) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                Edit Task
            </a>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-xl font-semibold mb-2">{{ $task->title }}</h2>
                    <div class="mt-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $task->description ?? 'No description provided' }}</p>
                    </div>
                    
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Location</h3>
                        <p class="text-gray-700">{{ $task->location ?? 'No location specified' }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Task Details</h3>
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <span class="text-gray-500">Status:</span>
                                <span class="ml-2 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                        ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500">Priority:</span>
                                <span class="ml-2 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : 
                                        ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500">Due Date:</span>
                                <span class="ml-2 text-gray-700">{{ $task->due_date ? $task->due_date->format('M d, Y H:i') : 'No due date' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Created At:</span>
                                <span class="ml-2 text-gray-700">{{ $task->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Last Updated:</span>
                                <span class="ml-2 text-gray-700">{{ $task->updated_at->format('M d, Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Assigned User</h3>
                        <div class="flex items-center">
                            @if($task->assignedUser)
                                <span class="text-gray-700">{{ $task->assignedUser->name }} ({{ $task->assignedUser->email }})</span>
                            @else
                                <span class="text-gray-500">Not assigned to any user</span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Created By</h3>
                        <div class="flex items-center">
                            @if($task->creator)
                                <span class="text-gray-700">{{ $task->creator->name }} ({{ $task->creator->email }})</span>
                            @else
                                <span class="text-gray-500">Unknown creator</span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Related Equipment</h3>
                        <div class="flex items-center">
                            @if($task->equipment)
                                <a href="{{ route('admin.equipment.show', $task->equipment) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $task->equipment->name }} ({{ $task->equipment->serial_number }})
                                </a>
                            @else
                                <span class="text-gray-500">No equipment associated</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 border-t pt-6 flex justify-end">
                <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                            onclick="return confirm('Are you sure you want to delete this task?')">
                        Delete Task
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 