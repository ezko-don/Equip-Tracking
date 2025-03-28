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
                                       accept="image/jpeg,image/png,image/gif,image/webp,image/bmp,image/tiff" 
                                       onchange="this.form.submit()">
                                <label for="photo" 
                                       class="absolute bottom-0 right-0 bg-white dark:bg-gray-700 rounded-full p-2 cursor-pointer shadow-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-300"
                                       title="Accepted formats: JPEG, PNG, GIF, WebP, BMP, TIFF">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </label>
                            </form>
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
                            <button onclick="window.location.href='{{ route('tasks.index') }}'" class="inline-flex items-center px-3 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                {{ __('Add Task') }}
                            </button>
                        </div>
                        <div class="space-y-4">
                            @forelse(auth()->user()->tasks()->where('due_date', '>=', now())->orderBy('due_date')->take(3)->get() as $task)
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const photoInput = document.getElementById('photo');
            const form = document.getElementById('profile-photo-form');
            const removeButton = document.getElementById('remove-photo-button');

            photoInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    form.submit();
                }
            });

            if (removeButton) {
                removeButton.addEventListener('click', function() {
                    if (confirm('Are you sure you want to remove your profile photo?')) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("profile.photo.destroy") }}';
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        
                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';
                        
                        form.appendChild(csrfToken);
                        form.appendChild(methodField);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout> 