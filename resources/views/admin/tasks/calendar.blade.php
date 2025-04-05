@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
<style>
    .fc-event {
        cursor: pointer;
    }
    .priority-high {
        border-left: 5px solid #ef4444 !important;
    }
    .priority-medium {
        border-left: 5px solid #f59e0b !important;
    }
    .priority-low {
        border-left: 5px solid #3b82f6 !important;
    }
    .status-completed {
        opacity: 0.6;
    }
    .task-details {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        width: 90%;
    }
    .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Task Calendar</h1>
        <a href="{{ route('admin.tasks.index') }}" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
            List View
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-4">
        <div id="calendar"></div>
    </div>
</div>

<div class="overlay" id="overlay"></div>
<div class="task-details" id="taskDetails">
    <div class="flex justify-between items-start mb-4">
        <h2 class="text-xl font-bold" id="taskTitle"></h2>
        <button id="closeDetails" class="text-gray-500 hover:text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    <div class="space-y-3">
        <p><strong>Description:</strong> <span id="taskDescription"></span></p>
        <p><strong>Due Date:</strong> <span id="taskDueDate"></span></p>
        <p><strong>Priority:</strong> <span id="taskPriority"></span></p>
        <p><strong>Status:</strong> <span id="taskStatus"></span></p>
        <p><strong>Assigned To:</strong> <span id="taskAssignedTo"></span></p>
        <p><strong>Created By:</strong> <span id="taskCreatedBy"></span></p>
        <p><strong>Equipment:</strong> <span id="taskEquipment"></span></p>
        <p><strong>Location:</strong> <span id="taskLocation"></span></p>
    </div>
    <div class="mt-6 flex space-x-3">
        <a href="#" id="editTaskLink" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</a>
        <form id="deleteTaskForm" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                    onclick="return confirm('Are you sure you want to delete this task?')">
                Delete
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            events: "{{ route('admin.tasks.calendar.data') }}",
            eventClick: function(info) {
                showTaskDetails(info.event);
            }
        });
        calendar.render();

        // Task details popup
        const overlay = document.getElementById('overlay');
        const taskDetails = document.getElementById('taskDetails');
        const closeDetails = document.getElementById('closeDetails');

        closeDetails.addEventListener('click', function() {
            overlay.style.display = 'none';
            taskDetails.style.display = 'none';
        });

        overlay.addEventListener('click', function() {
            overlay.style.display = 'none';
            taskDetails.style.display = 'none';
        });

        function showTaskDetails(event) {
            const task = event.extendedProps;
            
            document.getElementById('taskTitle').textContent = event.title;
            document.getElementById('taskDescription').textContent = task.description || 'No description provided';
            document.getElementById('taskDueDate').textContent = event.start ? event.start.toLocaleString() : 'No due date';
            document.getElementById('taskPriority').textContent = task.priority ? task.priority.charAt(0).toUpperCase() + task.priority.slice(1) : '';
            document.getElementById('taskStatus').textContent = task.status ? task.status.replace('_', ' ').charAt(0).toUpperCase() + task.status.replace('_', ' ').slice(1) : '';
            document.getElementById('taskAssignedTo').textContent = task.assigned_to_name || 'Not assigned';
            document.getElementById('taskCreatedBy').textContent = task.created_by_name || 'Unknown';
            document.getElementById('taskEquipment').textContent = task.equipment_name || 'None';
            document.getElementById('taskLocation').textContent = task.location || 'Not specified';
            
            document.getElementById('editTaskLink').href = task.edit_url;
            document.getElementById('deleteTaskForm').action = task.delete_url;
            
            overlay.style.display = 'block';
            taskDetails.style.display = 'block';
        }
    });
</script>
@endsection 