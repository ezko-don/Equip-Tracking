<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Profile') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Main Profile Card with Photo and Info -->
            <div class="p-6 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                <div class="md:flex gap-8">
                    <!-- Profile Photo Section -->
                    <div class="md:w-1/3 mb-6 md:mb-0">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                            {{ __('Profile Photo') }}
                        </h2>
                        
                        <form method="post" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" id="photo-form">
                            @csrf
                            @method('patch')

                            <div class="flex flex-col items-center">
                                <div class="relative group mb-4">
                                    @if(auth()->user()->profile_photo_path)
                                        <img src="{{ auth()->user()->profile_photo_url }}" 
                                             alt="{{ auth()->user()->name }}" 
                                             class="rounded-full h-40 w-40 object-cover ring-4 ring-purple-100 dark:ring-purple-900">
                                    @else
                                        <div class="rounded-full h-40 w-40 bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white text-4xl font-bold">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                    
                                    <label for="photo" class="absolute bottom-0 right-0 bg-purple-600 text-white rounded-full p-3 cursor-pointer hover:bg-purple-700 transition-colors duration-200 shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </label>
                                    <input type="file" 
                                           name="photo" 
                                           id="photo"
                                           accept="image/*"
                                           class="hidden"
                                           onchange="document.getElementById('photo-form').submit()">
                                </div>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-4">
                                    {{ __('Click the camera icon to upload a new photo (max 1MB)') }}
                                </p>
                                
                                @error('photo')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                
                                @if (session('status') === 'profile-photo-updated' || session('success'))
                                    <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                                        {{ __('Photo updated successfully.') }}
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Profile Information Section -->
                    <div class="md:w-2/3">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                            {{ __('Profile Information') }}
                        </h2>

                        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                            @csrf
                            @method('patch')

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="name" :value="__('Name')" class="font-medium" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Email')" class="font-medium" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>
                                
                                <div>
                                    <x-input-label for="role" :value="__('Account Type')" class="font-medium" />
                                    <x-text-input id="role" type="text" class="mt-1 block w-full bg-gray-100 dark:bg-gray-700" :value="ucfirst($user->role)" readonly />
                                </div>
                            </div>

                            <div class="flex items-center gap-4 mt-8">
                                <x-primary-button>{{ __('Save Changes') }}</x-primary-button>

                                @if (session('status') === 'profile-updated')
                                    <div class="text-sm text-green-600 dark:text-green-400">
                                        {{ __('Saved successfully.') }}
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Task Calendar Card -->
            <div class="p-6 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full">
                    <section>
                        <header class="mb-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ __('My Schedule') }}
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Manage your tasks and equipment bookings in one place') }}
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: 'auto',
                events: [
                    // Sample event data - replace with your actual data
                    {
                        title: 'Equipment booking',
                        start: '2023-03-01',
                        color: '#4F46E5'
                    },
                    {
                        title: 'Meeting',
                        start: '2023-03-15T10:00:00',
                        end: '2023-03-15T12:00:00',
                        color: '#10B981'
                    }
                ],
                themeSystem: 'standard',
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: 'short'
                }
            });
            calendar.render();

            // Handle form submission
            document.getElementById('add-task-form').addEventListener('submit', function(e) {
                e.preventDefault();
                // Add your AJAX logic here to save the task
                alert('Task added successfully!');
                // Close modal
                window.dispatchEvent(new CustomEvent('close-modal', {
                    detail: 'add-task'
                }));
            });
        });
    </script>
    @endpush
</x-app-layout> 