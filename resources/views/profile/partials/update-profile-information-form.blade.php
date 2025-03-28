<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <!-- Profile Photo -->
    <div class="mt-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Photo') }}
        </h3>

        <form id="profile-photo-form" method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" class="mt-4">
            @csrf
            @method('patch')

            <div class="flex items-center gap-4">
                <div class="relative">
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" 
                             alt="Profile photo" 
                             class="w-32 h-32 rounded-full object-cover border-4 border-purple-500">
                    @else
                        <div class="w-32 h-32 rounded-full bg-purple-600 flex items-center justify-center text-white text-4xl border-4 border-purple-500">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    
                    <label for="photo" class="absolute bottom-2 right-2 bg-white dark:bg-gray-800 rounded-full p-2 cursor-pointer shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <input type="file" id="photo" name="photo" class="hidden" accept="image/*">
                    </label>
                </div>

                <div class="flex-1">
                    <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                    
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Click the camera icon to choose a new photo. Maximum file size: 1MB.') }}
                    </div>

                    @if(auth()->user()->profile_photo_path)
                        <div class="mt-4">
                            <x-danger-button type="button" id="remove-photo-button" class="text-sm">
                                {{ __('Remove Photo') }}
                            </x-danger-button>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Tasks Section -->
    <div class="mt-8">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('My Tasks') }}
        </h3>

        <div class="mt-4">
            <button type="button" onclick="window.location.href='{{ route('tasks.index') }}'" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                {{ __('Add New Task') }}
            </button>
        </div>

        <div class="mt-4 bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden">
            @forelse(auth()->user()->tasks()->latest()->take(5)->get() as $task)
                <div class="p-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $task->title }}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Due: {{ $task->due_date->format('M d, Y') }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="window.location.href='{{ route('tasks.edit', $task) }}'" class="p-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('Are you sure you want to delete this task?')">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-600 dark:text-gray-400">
                    {{ __('No tasks found. Click the button above to create one.') }}
                </div>
            @endforelse
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
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

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

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