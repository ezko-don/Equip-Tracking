<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Profile Settings') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Photo Card -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                <div class="max-w-xl">
                    <section>
                        <header class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Profile Photo') }}
                                </h2>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('Update your profile picture. A clear photo helps others recognize you.') }}
                                </p>
                            </div>
                        </header>

                        <form method="post" action="{{ route('profile.photo.update') }}" class="mt-6" enctype="multipart/form-data">
                            @csrf
                            @method('patch')

                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                                <div class="relative group">
                                    @if(auth()->user()->profile_photo)
                                        <img src="{{ auth()->user()->profile_photo_url }}" 
                                             alt="{{ auth()->user()->name }}" 
                                             class="rounded-full h-24 w-24 object-cover ring-4 ring-purple-100 dark:ring-purple-900">
                                    @else
                                        <div class="rounded-full h-24 w-24 bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                    
                                    <label for="photo" class="absolute -bottom-2 -right-2 bg-purple-600 text-white rounded-full p-2 cursor-pointer hover:bg-purple-700 transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </label>
                                    <input type="file" 
                                           name="photo" 
                                           id="photo"
                                           accept="image/*"
                                           class="hidden">
                                </div>

                                <div class="flex-1">
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        {{ __('Choose a photo to upload (max 1MB).') }}
                                    </div>
                                    @error('photo')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    <x-primary-button>
                                        {{ __('Update Photo') }}
                                    </x-primary-button>
                                </div>
                            </div>

                            @if (session('success'))
                                <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/50 text-green-600 dark:text-green-400 rounded-lg">
                                    {{ session('success') }}
                                </div>
                            @endif
                        </form>
                    </section>
                </div>
            </div>

            <!-- Profile Information Card -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header class="mb-6">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Profile Information') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __("Update your account's profile information and email address.") }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                            @csrf
                            @method('patch')

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="name" :value="__('Name')" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save Changes') }}</x-primary-button>

                                @if (session('status') === 'profile-updated')
                                    <div class="text-sm text-green-600 dark:text-green-400">
                                        {{ __('Saved successfully.') }}
                                    </div>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <!-- Update Password Card -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header class="mb-6">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Update Password') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Ensure your account is using a long, random password to stay secure.') }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                            @csrf
                            @method('put')

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="current_password" :value="__('Current Password')" />
                                    <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password" :value="__('New Password')" />
                                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save Password') }}</x-primary-button>

                                @if (session('status') === 'password-updated')
                                    <div class="text-sm text-green-600 dark:text-green-400">
                                        {{ __('Password updated successfully.') }}
                                    </div>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <!-- Task Calendar Card -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full">
                    <section>
                        <header class="mb-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ __('Task Calendar') }}
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Manage your tasks and schedule.') }}
                                    </p>
                                </div>
                                <x-primary-button
                                    x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'add-task')"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    {{ __('Add Task') }}
                                </x-primary-button>
                            </div>
                        </header>

                        <!-- Calendar -->
                        <div id="calendar" class="bg-white dark:bg-gray-800 rounded-lg shadow"></div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <x-modal name="add-task">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Add New Task') }}
            </h2>

            <form id="add-task-form" class="space-y-6">
                @csrf
                <div>
                    <x-input-label for="name" :value="__('Event Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="due_date" :value="__('Date')" />
                    <x-text-input id="due_date" name="due_date" type="datetime-local" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="equipment" :value="__('Equipment Needed')" />
                    <select id="equipment" name="equipment[]" multiple class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 dark:focus:border-purple-600 focus:ring-purple-500 dark:focus:ring-purple-600 shadow-sm">
                        @foreach($equipment as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Hold Ctrl/Cmd to select multiple items</p>
                </div>

                <div class="mt-6 flex justify-end gap-4">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button type="submit">
                        {{ __('Add Task') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Edit Task Modal -->
    <x-modal name="edit-task">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Edit Task') }}
            </h2>

            <form id="edit-task-form" class="space-y-6">
                @csrf
                @method('PATCH')
                <input type="hidden" id="edit_task_id" name="task_id">

                <div>
                    <x-input-label for="edit_name" :value="__('Event Name')" />
                    <x-text-input id="edit_name" name="name" type="text" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="edit_due_date" :value="__('Date')" />
                    <x-text-input id="edit_due_date" name="due_date" type="datetime-local" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="edit_equipment" :value="__('Equipment Needed')" />
                    <select id="edit_equipment" name="equipment[]" multiple class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 dark:focus:border-purple-600 focus:ring-purple-500 dark:focus:ring-purple-600 shadow-sm">
                        @foreach($equipment as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Hold Ctrl/Cmd to select multiple items</p>
                </div>

                <div class="mt-6 flex items-center justify-between gap-4">
                    <x-danger-button type="button" id="delete-task-btn">
                        {{ __('Delete Task') }}
                    </x-danger-button>

                    <div class="flex gap-4">
                        <x-secondary-button x-on:click="$dispatch('close')">
                            {{ __('Cancel') }}
                        </x-secondary-button>

                        <x-primary-button type="submit">
                            {{ __('Save Changes') }}
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    @push('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <style>
        .fc-event {
            cursor: pointer;
        }
        .fc-daygrid-event {
            white-space: normal !important;
            align-items: normal !important;
        }
        .dark .fc-theme-standard td, 
        .dark .fc-theme-standard th,
        .dark .fc-theme-standard .fc-scrollgrid {
            border-color: rgb(75, 85, 99) !important;
        }
        .dark .fc-theme-standard .fc-scrollgrid {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .dark .fc-theme-standard .fc-col-header-cell {
            background-color: rgb(55, 65, 81);
            color: rgb(209, 213, 219);
        }
        .dark .fc-theme-standard .fc-daygrid-day-number {
            color: rgb(209, 213, 219);
        }
        .dark .fc-button-primary {
            background-color: rgb(79, 70, 229) !important;
            border-color: rgb(67, 56, 202) !important;
        }
        .dark .fc-button-primary:hover {
            background-color: rgb(67, 56, 202) !important;
        }
        .dark .fc-button-primary:disabled {
            background-color: rgb(99, 102, 241) !important;
        }
    </style>
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
                eventClick: function(info) {
                    const event = info.event;
                    document.getElementById('edit_task_id').value = event.id;
                    document.getElementById('edit_name').value = event.title;
                    document.getElementById('edit_due_date').value = event.start.toISOString().slice(0, 16);
                    
                    // Set selected equipment
                    const equipmentSelect = document.getElementById('edit_equipment');
                    const selectedEquipment = event.extendedProps.equipment || [];
                    Array.from(equipmentSelect.options).forEach(option => {
                        option.selected = selectedEquipment.includes(parseInt(option.value));
                    });

                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-task' }));
                },
                eventDrop: function(info) {
                    const event = info.event;
                    fetch(`/tasks/${event.id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            due_date: event.start.toISOString()
                        })
                    });
                }
            });
            calendar.render();

            // Add Task Form Handler
            document.getElementById('add-task-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = {
                    name: formData.get('name'),
                    due_date: formData.get('due_date'),
                    equipment: Array.from(formData.getAll('equipment[]')).map(Number)
                };

                fetch('/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(task => {
                    calendar.addEvent({
                        id: task.id,
                        title: task.name,
                        start: task.due_date,
                        extendedProps: {
                            equipment: task.equipment.map(e => e.id)
                        }
                    });
                    this.reset();
                    window.dispatchEvent(new CustomEvent('close'));
                });
            });

            // Edit Task Form Handler
            document.getElementById('edit-task-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const taskId = document.getElementById('edit_task_id').value;
                const formData = new FormData(this);
                const data = {
                    name: formData.get('name'),
                    due_date: formData.get('due_date'),
                    equipment: Array.from(formData.getAll('equipment[]')).map(Number)
                };

                fetch(`/tasks/${taskId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(task => {
                    const event = calendar.getEventById(taskId);
                    event.setProp('title', task.name);
                    event.setStart(task.due_date);
                    event.setExtendedProp('equipment', task.equipment.map(e => e.id));
                    window.dispatchEvent(new CustomEvent('close'));
                });
            });

            // Delete Task Handler
            document.getElementById('delete-task-btn').addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this task?')) {
                    const taskId = document.getElementById('edit_task_id').value;
                    fetch(`/tasks/${taskId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(() => {
                        calendar.getEventById(taskId).remove();
                        window.dispatchEvent(new CustomEvent('close'));
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 