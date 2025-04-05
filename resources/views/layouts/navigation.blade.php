<nav x-data="{ open: false, activeDropdown: null }" class="bg-gradient-to-r from-blue-800 to-indigo-900 border-b border-gray-200 shadow-lg relative z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <!-- Left Side -->
            <div class="flex items-center">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-16 w-auto" />
                    </a>
                </div>

                <!-- Primary Navigation -->
                <div class="hidden lg:flex lg:space-x-4 lg:ml-10">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <x-heroicon-o-home class="w-5 h-5"/>
                        <span>Dashboard</span>
                    </a>

                    <!-- Equipment Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                                class="nav-link group">
                            <x-heroicon-o-computer-desktop class="w-5 h-5"/>
                            <span>Equipment</span>
                            <x-heroicon-o-chevron-down class="w-4 h-4 ml-1 group-hover:transform group-hover:rotate-180 transition-transform"/>
                        </button>
                        <div x-show="open" x-cloak
                             class="dropdown-menu">
                            <a href="{{ route('admin.equipment.index') }}" class="dropdown-item">
                                <x-heroicon-o-view-grid class="w-5 h-5 mr-2"/>
                                All Equipment
                            </a>
                            <a href="{{ route('admin.categories.index') }}" class="dropdown-item">
                                <x-heroicon-o-tag class="w-5 h-5 mr-2"/>
                                Categories
                            </a>
                            <a href="{{ route('admin.maintenance.index') }}" class="dropdown-item">
                                <x-heroicon-o-wrench class="w-5 h-5 mr-2"/>
                                Maintenance
                            </a>
                        </div>
                    </div>

                    <!-- Notifications Dropdown -->
                    <div class="relative" 
                         x-data="notificationSystem" 
                         @notification-received.window="handleNewNotification($event.detail)">
                        <button @click="open = !open" 
                                class="nav-link group relative">
                            <x-heroicon-o-bell class="w-5 h-5"/>
                            <span class="ml-2">Notifications</span>
                            <!-- Notification Badge -->
                            <span x-show="unreadCount > 0"
                                  x-text="unreadCount"
                                  class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            </span>
                        </button>
                        
                        <div x-show="open" 
                             x-cloak
                             @click.away="open = false"
                             class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50">
                            <div class="px-4 py-2 border-b border-gray-200">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-sm font-semibold text-gray-700">Notifications</h3>
                                    <button x-show="notifications.length > 0"
                                            @click="markAllAsRead"
                                            class="text-xs text-blue-600 hover:text-blue-800">
                                        Mark all as read
                                    </button>
                                </div>
                            </div>
                            
                            <div class="max-h-64 overflow-y-auto">
                                <template x-if="notifications.length === 0">
                                    <div class="px-4 py-3 text-sm text-gray-500 text-center">
                                        No new notifications
                                    </div>
                                </template>
                                
                                <template x-for="notification in notifications" :key="notification.id">
                                    <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-200 cursor-pointer"
                                         @click="goToNotification(notification)">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                                <p class="text-sm text-gray-600 mt-1" x-text="notification.message"></p>
                                                <p class="text-xs text-gray-400 mt-1" x-text="notification.time"></p>
                                            </div>
                                            <button @click.stop="markAsRead(notification.id)"
                                                    class="text-xs text-blue-600 hover:text-blue-800">
                                                Mark as read
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Bookings -->
                    <a href="{{ route('admin.bookings.index') }}" 
                       class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                        <x-heroicon-o-calendar class="w-5 h-5"/>
                        <span>Bookings</span>
                    </a>

                    <!-- Users -->
                    <a href="{{ route('admin.users.index') }}" 
                       class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <x-heroicon-o-users class="w-5 h-5"/>
                        <span>Users</span>
                    </a>

                    <!-- Reports -->
                    <a href="{{ route('admin.reports.index') }}" 
                       class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <x-heroicon-o-document-report class="w-5 h-5"/>
                        <span>Reports</span>
                    </a>

                    <!-- Messages -->
                    <x-nav-link :href="route('messages.inbox')" :active="request()->routeIs('messages.*')">
                        {{ __('Messages') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Side -->
            <div class="hidden lg:flex lg:items-center lg:ml-6">
                <div class="flex items-center">
                    <!-- User Dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-white hover:text-gray-200 transition duration-150 ease-in-out">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-blue-900 font-bold">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <span class="ml-2">{{ Auth::user()->name }}</span>
                                    <x-heroicon-o-chevron-down class="w-4 h-4 ml-1"/>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')" class="dropdown-item">
                                <x-heroicon-o-user-circle class="w-5 h-5 mr-2"/>
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form action="{{ route('logout') }}" method="POST" id="nav-logout-form">
                                @csrf
                                <button type="submit" class="flex items-center px-4 py-2 w-full text-left text-sm text-red-600 hover:text-red-700 hover:bg-red-50">
                                    <x-heroicon-o-logout class="w-5 h-5 mr-2"/>
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center lg:hidden">
                <button @click="open = !open" class="mobile-menu-button">
                    <x-heroicon-o-menu class="w-6 h-6 text-white"/>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="open" x-cloak class="lg:hidden absolute w-full bg-blue-900 z-50">
        <div class="pt-2 pb-3 space-y-1">
            <!-- Mobile navigation items -->
            <a href="{{ route('admin.dashboard') }}" class="mobile-nav-item">
                <x-heroicon-o-home class="w-5 h-5 mr-2"/>
                Dashboard
            </a>
            
            <!-- Equipment Section -->
            <div x-data="{ equipmentOpen: false }">
                <button @click="equipmentOpen = !equipmentOpen" class="mobile-nav-item w-full flex justify-between items-center">
                    <div class="flex items-center">
                        <x-heroicon-o-computer-desktop class="w-5 h-5 mr-2"/>
                        Equipment
                    </div>
                    <x-heroicon-o-chevron-down class="w-4 h-4 transform" :class="{'rotate-180': equipmentOpen}"/>
                </button>
                
                <div x-show="equipmentOpen" class="pl-4">
                    <a href="{{ route('admin.equipment.index') }}" class="mobile-nav-item">
                        <x-heroicon-o-view-grid class="w-5 h-5 mr-2"/>
                        All Equipment
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="mobile-nav-item">
                        <x-heroicon-o-tag class="w-5 h-5 mr-2"/>
                        Categories
                    </a>
                    <a href="{{ route('admin.maintenance.index') }}" class="mobile-nav-item">
                        <x-heroicon-o-wrench class="w-5 h-5 mr-2"/>
                        Maintenance
                    </a>
                </div>
            </div>

            <!-- Notifications Dropdown -->
            <div class="relative" x-data="{ open: false, notifications: [] }" @click.away="open = false">
                <button @click="open = !open" 
                        class="mobile-nav-item w-full flex justify-between items-center">
                    <div class="flex items-center">
                        <x-heroicon-o-bell class="w-5 h-5 mr-2"/>
                        Notifications
                    </div>
                    <x-heroicon-o-chevron-down class="w-4 h-4 transform" :class="{'rotate-180': open}"/>
                </button>
                
                <div x-show="open" 
                     x-cloak
                     class="mobile-dropdown-menu">
                    <div class="p-3 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700">Notifications</h3>
                    </div>
                    
                    <div class="max-h-64 overflow-y-auto">
                        <template x-if="notifications.length === 0">
                            <div class="p-4 text-sm text-gray-500 text-center">
                                No new notifications
                            </div>
                        </template>
                        
                        <template x-for="notification in notifications" :key="notification.id">
                            <div class="p-4 border-b border-gray-200 hover:bg-gray-50">
                                <p class="text-sm text-gray-600" x-text="notification.message"></p>
                                <span class="text-xs text-gray-400" x-text="notification.time"></span>
                            </div>
                        </template>
                    </div>
                    
                    <div class="p-2 border-t border-gray-200">
                        <button @click="notifications = []" 
                                class="w-full text-center text-sm text-blue-600 hover:text-blue-800">
                            Mark all as read
                        </button>
                    </div>
                </div>
            </div>

            <!-- Other mobile menu items -->
            <a href="{{ route('admin.bookings.index') }}" class="mobile-nav-item">
                <x-heroicon-o-calendar class="w-5 h-5 mr-2"/>
                Bookings
            </a>
            
            <!-- Add other mobile menu items -->
        </div>
    </div>
</nav>

<style>
    .nav-link {
        @apply flex items-center px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-blue-800 transition-all duration-150 ease-in-out space-x-2;
    }

    .nav-link.active {
        @apply bg-white text-blue-900;
    }

    .dropdown-menu {
        @apply absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1 transform opacity-0 scale-95 transition-all duration-100 ease-out;
    }

    .dropdown-menu[x-show] {
        @apply transform opacity-100 scale-100 transition-all duration-100 ease-in;
    }

    .dropdown-item {
        @apply flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150 ease-in-out;
    }

    .mobile-menu-button {
        @apply inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-200 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white transition duration-150 ease-in-out;
    }

    .mobile-nav-item {
        @apply flex items-center w-full px-4 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-colors duration-150 ease-in-out;
    }

    .mobile-nav-item.active {
        @apply bg-blue-50 text-blue-700;
    }

    .mobile-dropdown-menu {
        @apply absolute z-50 mt-2 w-full rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1 transform opacity-0 scale-95 transition-all duration-100 ease-out;
    }

    .mobile-dropdown-menu[x-show] {
        @apply transform opacity-100 scale-100 transition-all duration-100 ease-in;
        display: block !important;
    }
    
    /* Fix for mobile menu display */
    .mobile-dropdown-menu {
        position: relative !important;
        z-index: 60 !important;
    }
    
    /* Ensure mobile menu is visible */
    .lg\:hidden[x-show] {
        display: block !important;
    }

    [x-cloak] {
        display: none !important;
    }
</style>