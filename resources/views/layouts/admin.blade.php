<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" 
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    <style>
        /* Dark mode transitions */
        .dark-mode-transition {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: theme('colors.gray.100');
            border-radius: 4px;
        }
        .dark ::-webkit-scrollbar-track {
            background: theme('colors.gray.800');
        }
        ::-webkit-scrollbar-thumb {
            background: theme('colors.purple.500');
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: theme('colors.purple.600');
        }
    </style>
</head>
<body class="font-sans antialiased dark-mode-transition" :class="{ 'bg-gray-100 text-gray-900': !darkMode, 'bg-gray-900 text-gray-100': darkMode }">
    <div class="min-h-screen dark-mode-transition" :class="{ 'bg-gray-100': !darkMode, 'bg-gray-900': darkMode }">
        <!-- Navigation -->
        <nav x-data="{ open: false }" class="bg-gradient-to-r from-blue-900 to-indigo-800 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('admin.dashboard') }}">
                                <x-application-logo />
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-4 sm:-my-px sm:ml-10 sm:flex">
                            @foreach([
                                'Dashboard' => ['admin.dashboard', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                                'Equipment' => ['admin.equipment.index', 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z'],
                                'Categories' => ['admin.categories.index', 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
                                'Bookings' => ['admin.bookings.index', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                                'Users' => ['admin.users.index', 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                                'Reports' => ['admin.reports.index', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z']
                            ] as $name => $route)
                                <a href="{{ route($route[0]) }}" 
                                   class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150 ease-in-out group
                                          {{ request()->routeIs($route[0]) 
                                            ? 'bg-white text-blue-900 shadow-md' 
                                            : 'text-white hover:bg-blue-800' }}">
                                    <svg class="w-5 h-5 mr-2 {{ request()->routeIs($route[0]) ? 'text-blue-900' : 'text-white' }}" 
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $route[1] }}"/>
                                    </svg>
                                    {{ $name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center text-white hover:text-gray-200 transition duration-150 ease-in-out">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-blue-900 font-bold mr-2">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                        <div>{{ Auth::user()->name }}</div>
                                    </div>
                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();"
                                            class="flex items-center text-red-600 hover:text-red-700 hover:bg-red-50">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-4">
            @if(session('error'))
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded relative dark-mode-transition">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Only include chat-notification for admin -->
        @if(auth()->user()->isAdmin())
            <x-chat-notification />
        @endif
    </div>

    <!-- Add this script to ensure CSRF token is available for AJAX requests -->
    <script>
        window.csrf_token = "{{ csrf_token() }}";
    </script>

    @stack('scripts')
</body>
</html> 