@extends('layouts.admin')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .quick-action {
        transition: all 0.3s ease;
    }
    .quick-action:hover {
        transform: translateY(-3px);
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg shadow-lg p-8 mb-8">
        <h1 class="text-3xl font-bold text-white">Admin Dashboard</h1>
        <p class="text-blue-100">Manage your equipment system efficiently</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Equipment -->
        <a href="{{ route('admin.equipment.index') }}" class="transition-all duration-300 hover:scale-105">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white cursor-pointer">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-blue-100">Total Equipment</p>
                    <h2 class="text-3xl font-bold">{{ $totalEquipment }}</h2>
                </div>
                <div class="bg-blue-400/30 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </div>
            </div>
        </div>
        </a>

        <!-- Available Equipment -->
        <a href="{{ route('admin.equipment.index', ['status' => 'available']) }}" class="transition-all duration-300 hover:scale-105">
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white cursor-pointer">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-emerald-100">Available Equipment</p>
                    <h2 class="text-3xl font-bold">{{ $availableEquipment }}</h2>
                </div>
                <div class="bg-emerald-400/30 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>
        </a>

        <!-- Pending Bookings -->
        <a href="{{ route('admin.bookings.pending') }}" class="transition-all duration-300 hover:scale-105">
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white cursor-pointer">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-amber-100">Pending Bookings</p>
                    <h2 class="text-3xl font-bold">{{ $pendingBookings }}</h2>
                </div>
                <div class="bg-amber-400/30 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        </a>

        <!-- Total Users -->
        <a href="{{ route('admin.users.index') }}" class="transition-all duration-300 hover:scale-105">
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white cursor-pointer">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-purple-100">Total Users</p>
                    <h2 class="text-3xl font-bold">{{ $totalUsers }}</h2>
                </div>
                <div class="bg-purple-400/30 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>
        </a>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Add New Equipment -->
        <a href="{{ route('admin.equipment.create') }}" 
           class="bg-white hover:bg-blue-50 rounded-xl shadow-md hover:shadow-xl p-6 border-l-4 border-blue-500 transition-all duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Add New Equipment</h3>
                    <p class="text-gray-600">Add new equipment to the system</p>
                </div>
            </div>
        </a>

        <!-- View Reports -->
        <a href="{{ route('admin.reports.index') }}" 
           class="bg-white hover:bg-blue-50 rounded-xl shadow-md hover:shadow-xl p-6 border-l-4 border-indigo-500 transition-all duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">View Reports</h3>
                    <p class="text-gray-600">Access detailed system reports</p>
                </div>
            </div>
        </a>

        <!-- Maintenance Logs -->
        <a href="{{ route('admin.maintenance.index') }}" 
           class="bg-white hover:bg-blue-50 rounded-xl shadow-md hover:shadow-xl p-6 border-l-4 border-purple-500 transition-all duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Maintenance Logs</h3>
                    <p class="text-gray-600">View and manage equipment maintenance</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Recent Bookings -->
    <div class="rounded-xl shadow-lg overflow-hidden animate-fade-in dark-mode-transition" 
         :class="{ 'bg-white': !darkMode, 'bg-gray-800': darkMode }"
         style="animation-delay: 0.7s">
        <div class="p-6 border-b dark-mode-transition" :class="{ 'border-gray-200': !darkMode, 'border-gray-700': darkMode }">
            <h3 class="text-xl font-semibold dark-mode-transition" :class="{ 'text-gray-900': !darkMode, 'text-white': darkMode }">Recent Bookings</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y dark-mode-transition" :class="{ 'divide-gray-200': !darkMode, 'divide-gray-700': darkMode }">
                <thead :class="{ 'bg-gray-50': !darkMode, 'bg-gray-900': darkMode }">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider dark-mode-transition"
                            :class="{ 'text-gray-500': !darkMode, 'text-gray-400': darkMode }">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider dark-mode-transition"
                            :class="{ 'text-gray-500': !darkMode, 'text-gray-400': darkMode }">Equipment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider dark-mode-transition"
                            :class="{ 'text-gray-500': !darkMode, 'text-gray-400': darkMode }">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider dark-mode-transition"
                            :class="{ 'text-gray-500': !darkMode, 'text-gray-400': darkMode }">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider dark-mode-transition"
                            :class="{ 'text-gray-500': !darkMode, 'text-gray-400': darkMode }">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark-mode-transition" 
                       :class="{ 'divide-gray-200': !darkMode, 'divide-gray-700': darkMode }">
                    @foreach($recentBookings as $booking)
                    <tr class="transition-colors dark-mode-transition"
                        :class="{ 'hover:bg-gray-50': !darkMode, 'hover:bg-gray-700': darkMode }">
                        <td class="px-6 py-4 whitespace-nowrap dark-mode-transition"
                            :class="{ 'text-gray-900': !darkMode, 'text-gray-300': darkMode }">
                            {{ $booking->user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap dark-mode-transition"
                            :class="{ 'text-gray-900': !darkMode, 'text-gray-300': darkMode }">
                            {{ $booking->equipment->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full dark-mode-transition"
                                :class="{
                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': '{{ $booking->status }}' === 'approved',
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': '{{ $booking->status }}' === 'pending',
                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': '{{ $booking->status }}' === 'rejected'
                                }">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap dark-mode-transition"
                            :class="{ 'text-gray-900': !darkMode, 'text-gray-300': darkMode }">
                            {{ $booking->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.bookings.show', $booking) }}" 
                               class="transition-colors dark-mode-transition"
                               :class="{ 'text-indigo-600 hover:text-indigo-900': !darkMode, 'text-indigo-400 hover:text-indigo-300': darkMode }">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 