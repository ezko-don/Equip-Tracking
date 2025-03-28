<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Tasks') }}
            </h2>
            <button @click="$dispatch('open-modal', 'add-task')" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                {{ __('Add Task') }}
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Calendar -->
                    <div id="calendar" class="bg-white dark:bg-gray-800 rounded-lg shadow"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <x-modal name="add-task" :show="false">
        <form id="add-task-form" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Add New Task') }}
            </h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="title" :value="__('Event Name')" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 dark:focus:border-purple-600 focus:ring-purple-500 dark:focus:ring-purple-600 shadow-sm" rows="3"></textarea>
                </div>

                <div>
                    <x-input-label for="due_date" :value="__('Date')" />
                    <x-text-input id="due_date" name="due_date" type="datetime-local" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="priority" :value="__('Priority')" />
                    <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 dark:focus:border-purple-600 focus:ring-purple-500 dark:focus:ring-purple-600 shadow-sm">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 dark:focus:border-purple-600 focus:ring-purple-500 dark:focus:ring-purple-600 shadow-sm">
                        <option value="pending" selected>Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="equipment" :value="__('Equipment Needed')" />
                    <select id="equipment" name="equipment[]" multiple class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 dark:focus:border-purple-600 focus:ring-purple-500 dark:focus:ring-purple-600 shadow-sm">
                        @foreach($equipment as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Hold Ctrl/Cmd to select multiple items</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3" type="submit">
                    {{ __('Add Task') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Edit Task Modal -->
    <x-modal name="edit-task" :show="false">
        <form id="edit-task-form" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Edit Task') }}
            </h2>

            <input type="hidden" id="edit_task_id" name="task_id">

            <div class="space-y-4">
                <div>
                    <x-input-label for="edit_title" :value="__('Title')" />
                    <x-text-input id="edit_title" name="title" type="text" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="edit_description" :value="__('Description')" />
                    <textarea id="edit_description" name="description" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 dark:focus:border-purple-600 focus:ring-purple-500 dark:focus:ring-purple-600 shadow-sm" rows="3"></textarea>
                </div>

                <div>
                    <x-input-label for="edit_due_date" :value="__('Due Date')" />
                    <x-text-input id="edit_due_date" name="due_date" type="datetime-local" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="edit_priority" :value="__('Priority')" />
                    <select id="edit_priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 dark:focus:border-purple-600 focus:ring-purple-500 dark:focus:ring-purple-600 shadow-sm">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="edit_status" :value="__('Status')" />
                    <select id="edit_status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 dark:focus:border-purple-600 focus:ring-purple-500 dark:focus:ring-purple-600 shadow-sm">
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-danger-button class="mr-auto" type="button" id="delete-task-btn">
                    {{ __('Delete') }}
                </x-danger-button>

                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3" type="submit">
                    {{ __('Save Changes') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    @push('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    @endpush

    @push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '/tasks/calendar',
                editable: true,
                eventContent: function(arg) {
                    let content = document.createElement('div');
                    content.classList.add('flex', 'flex-col', 'w-full', 'p-1');
                    
                    // Event title
                    let titleEl = document.createElement('div');
                    titleEl.classList.add('font-semibold', 'mb-1');
                    titleEl.innerHTML = arg.event.title;
                    content.appendChild(titleEl);
                    
                    // Buttons container
                    let buttonsDiv = document.createElement('div');
                    buttonsDiv.classList.add('flex', 'gap-2', 'mt-1');
                    
                    // Edit button
                    let editBtn = document.createElement('button');
                    editBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>';
                    editBtn.classList.add('p-1', 'rounded', 'bg-blue-500', 'hover:bg-blue-600', 'text-white');
                    editBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        openEditModal(arg.event);
                    });
                    
                    // Delete button
                    let deleteBtn = document.createElement('button');
                    deleteBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>';
                    deleteBtn.classList.add('p-1', 'rounded', 'bg-red-500', 'hover:bg-red-600', 'text-white');
                    deleteBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        if (confirm('Are you sure you want to delete this task?')) {
                            deleteTask(arg.event);
                        }
                    });
                    
                    buttonsDiv.appendChild(editBtn);
                    buttonsDiv.appendChild(deleteBtn);
                    content.appendChild(buttonsDiv);
                    
                    return { domNodes: [content] };
                },
                eventClick: function(info) {
                    openEditModal(info.event);
                }
            });
            calendar.render();

            // Helper function to open edit modal
            function openEditModal(event) {
                document.getElementById('edit_task_id').value = event.id;
                document.getElementById('edit_title').value = event.title;
                document.getElementById('edit_description').value = event.extendedProps.description || '';
                document.getElementById('edit_due_date').value = event.start.toISOString().slice(0, 16);
                document.getElementById('edit_priority').value = event.extendedProps.priority;
                document.getElementById('edit_status').value = event.extendedProps.status;
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-task' }));
            }

            // Helper function to delete task
            function deleteTask(event) {
                fetch(`/tasks/${event.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to delete task');
                    }
                    event.remove();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete task. Please try again.');
                });
            }

            // Add Task Form Handler
            document.getElementById('add-task-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const data = {};
                formData.forEach((value, key) => {
                    if (key === 'equipment[]') {
                        if (!data.equipment) data.equipment = [];
                        data.equipment.push(value);
                    } else {
                        data[key] = value;
                    }
                });

                fetch('/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(task => {
                    calendar.addEvent({
                        id: task.id,
                        title: task.title,
                        start: task.start,
                        description: task.description,
                        priority: task.priority,
                        status: task.status
                    });
                    
                    this.reset();
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: 'add-task' }));
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to create task. Please try again.');
                });
            });

            // Edit Task Form Handler
            document.getElementById('edit-task-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const taskId = document.getElementById('edit_task_id').value;
                const formData = new FormData(this);
                fetch(`/tasks/${taskId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                })
                .then(response => response.json())
                .then(task => {
                    const event = calendar.getEventById(taskId);
                    event.setProp('title', task.title);
                    event.setStart(task.due_date);
                    event.setExtendedProp('description', task.description);
                    event.setExtendedProp('status', task.status);
                    event.setExtendedProp('priority', task.priority);
                    window.dispatchEvent(new CustomEvent('close'));
                });
            });
        });
    </script>
    @endpush
</x-app-layout> 