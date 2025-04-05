<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Overview Card -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
                <div class="relative h-48 bg-gradient-to-r from-purple-500 to-indigo-600">
                    <div class="absolute -bottom-16 left-8">
                        <div class="relative">
                            @if(auth()->user()->profile_photo_path)
                                <img src="{{ auth()->user()->profile_photo_url }}" 
                                     alt="Profile photo" 
                                     class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-lg">
                            @else
                                <div class="w-32 h-32 rounded-full bg-purple-600 border-4 border-white dark:border-gray-800 shadow-lg flex items-center justify-center text-white text-4xl">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <form id="profile-photo-form" method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('patch')
                                <input type="file" 
                                       id="photo" 
                                       name="photo" 
                                       class="hidden" 
                                       accept="image/jpeg,image/png,image/gif,image/webp,image/bmp,image/tiff">
                                <label for="photo" 
                                       class="absolute bottom-0 right-0 bg-white dark:bg-gray-700 rounded-full p-2 cursor-pointer shadow-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-300"
                                       title="Accepted formats: JPEG, PNG, GIF, WebP, BMP, TIFF">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </label>
                            </form>
                            @error('photo')
                                <div class="mt-2 text-red-600">{{ $message }}</div>
                            @enderror
                            @if(session('status') === 'profile-photo-updated')
                                <div class="mt-2 text-green-600">Profile photo updated successfully!</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pt-20 pb-6 px-8">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ auth()->user()->email }}</p>
                            <p class="mt-2 text-sm text-purple-600 dark:text-purple-400">Member since {{ auth()->user()->created_at->format('F Y') }}</p>
                        </div>
                        @if(auth()->user()->profile_photo_path)
                            <button type="button" id="remove-photo-button" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                                {{ __('Remove Photo') }}
                            </button>
                            <form id="remove-photo-form" action="{{ route('profile.photo.destroy') }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Profile Information') }}
                        </h3>
                    </div>

                    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                        @csrf
                        @method('patch')

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

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>

                            @if (session('status') === 'profile-updated')
                                <p x-data="{ show: true }"
                                   x-show="show"
                                   x-transition
                                   x-init="setTimeout(() => show = false, 2000)"
                                   class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('Saved.') }}
                                </p>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activity Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Bookings -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            {{ __('Recent Bookings') }}
                        </h3>
                        <div class="space-y-4">
                            @forelse(auth()->user()->bookings()->latest()->take(3)->get() as $booking)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->equipment->name }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $booking->start_time->format('M d, Y H:i') }}</p>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full 
                                        @if($booking->status === 'approved') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                        @else bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-gray-600 dark:text-gray-400 text-center py-4">No recent bookings found.</p>
                            @endforelse
                            <a href="{{ route('bookings.index') }}" class="block text-center text-sm text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 mt-4">
                                View All Bookings →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Tasks -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Upcoming Tasks') }}
                            </h3>
                            <button type="button" onclick="openTaskModal()" class="inline-flex items-center px-3 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                {{ __('Add Task') }}
                            </button>
                        </div>
                        <div class="space-y-4">
                            @php
                                $tasks = auth()->user()->tasks()
                                    ->orWhere('created_by', auth()->id())
                                    ->where('due_date', '>=', now())
                                    ->orderBy('due_date')
                                    ->take(3)
                                    ->get();
                            @endphp
                            @forelse($tasks as $task)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $task->title }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Due: {{ $task->due_date->format('M d, Y H:i') }}</p>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        @if($task->priority === 'high') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                        @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                        @else bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 @endif">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-gray-600 dark:text-gray-400 text-center py-4">No upcoming tasks found.</p>
                            @endforelse
                            <a href="{{ route('tasks.index') }}" class="block text-center text-sm text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 mt-4">
                                View All Tasks →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Modal -->
    <div id="taskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" x-data="{ open: false }">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Add New Task</h3>
                <form id="addTaskForm" method="POST" action="{{ route('tasks.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required />
                    </div>

                    <div>
                        <x-input-label for="location" :value="__('Location')" />
                        <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" placeholder="Enter location" required />
                    </div>

                    <div>
                        <x-input-label for="due_date" :value="__('Due Date')" />
                        <x-text-input id="due_date" name="due_date" type="datetime-local" class="mt-1 block w-full" required />
                    </div>

                    <div>
                        <x-input-label for="priority" :value="__('Priority')" />
                        <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="equipment_id" :value="__('Equipment (Optional)')" />
                        <select id="equipment_id" name="equipment_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="">No Equipment</option>
                            @foreach(\App\Models\Equipment::where('status', 'available')->get() as $equipment)
                                <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" onclick="closeTaskModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            Add Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const removeButton = document.getElementById('remove-photo-button');
            const removeForm = document.getElementById('remove-photo-form');
            const photoInput = document.getElementById('photo');
            const photoForm = document.getElementById('profile-photo-form');

            if (removeButton && removeForm) {
                removeButton.addEventListener('click', function() {
                    if (confirm('Are you sure you want to remove your profile photo?')) {
                        removeForm.submit();
                    }
                });
            }

            if (photoInput && photoForm) {
                photoInput.addEventListener('change', async function(e) {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        
                        // Validate file size (5MB max)
                        if (file.size > 5 * 1024 * 1024) {
                            alert('File size must be less than 5MB');
                            return;
                        }

                        // Validate file type
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp', 'image/tiff'];
                        if (!allowedTypes.includes(file.type)) {
                            alert('File type not supported. Please use: JPEG, PNG, GIF, WebP, BMP, or TIFF');
                            return;
                        }

                        try {
                            const formData = new FormData(photoForm);
                            
                            // Show loading state
                            const label = this.nextElementSibling;
                            const originalContent = label.innerHTML;
                            label.innerHTML = '<svg class="w-6 h-6 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                            
                            const response = await fetch(photoForm.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                credentials: 'same-origin'
                            });

                            const data = await response.json();

                            if (!response.ok) {
                                throw new Error(data.message || 'Failed to upload photo');
                            }

                            // Show success message
                            const successDiv = document.createElement('div');
                            successDiv.className = 'mt-2 text-green-600';
                            successDiv.textContent = 'Profile photo updated successfully!';
                            photoForm.parentNode.appendChild(successDiv);

                            // Reload the page after a short delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } catch (error) {
                            console.error('Upload error:', error);
                            
                            // Show error message
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'mt-2 text-red-600';
                            errorDiv.textContent = error.message || 'Failed to upload photo';
                            photoForm.parentNode.appendChild(errorDiv);
                            
                            // Reset the camera icon
                            label.innerHTML = originalContent;
                        }
                    }
                });
            }

            const taskModal = document.getElementById('taskModal');
            const addTaskForm = document.getElementById('addTaskForm');

            addTaskForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });

                    if (response.ok) {
                        closeTaskModal();
                        window.location.reload();
                    } else {
                        const data = await response.json();
                        alert(data.message || 'An error occurred while adding the task.');
                    }
                } catch (error) {
                    alert('An error occurred while adding the task.');
                }
            });
        });

        function openTaskModal() {
            const taskModal = document.getElementById('taskModal');
            taskModal.classList.remove('hidden');
        }

        function closeTaskModal() {
            const taskModal = document.getElementById('taskModal');
            taskModal.classList.add('hidden');
            document.getElementById('addTaskForm').reset();
        }

        window.onclick = function(event) {
            const taskModal = document.getElementById('taskModal');
            if (event.target === taskModal) {
                closeTaskModal();
            }
        }
    </script>
    @endpush
</x-app-layout> 