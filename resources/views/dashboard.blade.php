<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <img src="{{ asset('images/strathmore-logo.png') }}" 
                 alt="Strathmore Logo" 
                 class="h-8 w-auto mr-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Dashboard
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section with Animation -->
            <div class="mb-8 transform hover:scale-[1.01] transition-transform duration-300">
                <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg shadow-xl overflow-hidden">
                    <div class="px-6 py-8 md:p-8 text-white">
                        <h2 class="text-3xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h2>
                        <p class="text-purple-100">Manage your equipment bookings and tasks all in one place.</p>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Active Bookings -->
                <a href="{{ route('bookings.index', ['status' => 'approved']) }}" class="block">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transform hover:scale-[1.02] transition-all duration-300">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="bg-blue-100 dark:bg-blue-900 rounded-full p-3">
                                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Active Bookings</h2>
                                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $activeBookings }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Pending Bookings -->
                <a href="{{ route('bookings.index', ['status' => 'pending']) }}" class="block">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transform hover:scale-[1.02] transition-all duration-300">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="bg-yellow-100 dark:bg-yellow-900 rounded-full p-3">
                                    <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Pending Bookings</h2>
                                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingBookings }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Total Bookings -->
                <a href="{{ route('bookings.index') }}" class="block">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transform hover:scale-[1.02] transition-all duration-300">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="bg-green-100 dark:bg-green-900 rounded-full p-3">
                                    <svg class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Total Bookings</h2>
                                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $totalBookings }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Book Equipment -->
                <a href="{{ route('equipment.index') }}" 
                   class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 transform hover:scale-[1.02]">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-purple-100 dark:bg-purple-900 rounded-full p-3">
                                <svg class="h-6 w-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Book Equipment</h2>
                                <p class="text-gray-600 dark:text-gray-400">{{ $availableEquipment }} equipment available</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- View My Bookings -->
                <a href="{{ route('bookings.index') }}" 
                   class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 transform hover:scale-[1.02]">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-indigo-100 dark:bg-indigo-900 rounded-full p-3">
                                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">My Bookings</h2>
                                <p class="text-gray-600 dark:text-gray-400">View and manage your bookings</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Recent Bookings -->
            @if($recentBookings->count() > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transform hover:scale-[1.01] transition-all duration-300">
                <div class="p-6">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 mb-4">Recent Bookings</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Equipment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($recentBookings as $booking)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                        {{ $booking->equipment->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $booking->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 
                                               ($booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : 
                                               'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300') }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $booking->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout> 