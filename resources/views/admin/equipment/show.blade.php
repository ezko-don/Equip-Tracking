@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('admin.equipment.index') }}" 
               class="inline-flex items-center text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Equipment List
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="md:flex">
                <!-- Equipment Image Section -->
                <div class="md:w-1/2">
                    <div class="relative h-[500px] group">
                        @if($equipment->image)
                            <img src="{{ asset('storage/' . $equipment->image) }}" 
                                 alt="{{ $equipment->name }}" 
                                 class="w-full h-full object-contain bg-gray-50 dark:bg-gray-900 p-4 group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity duration-300"></div>
                        @else
                            <div class="w-full h-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <svg class="w-32 h-32 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Equipment Details Section -->
                <div class="md:w-1/2 p-8">
                    <div class="flex justify-between items-start mb-6">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $equipment->name }}</h1>
                        <div class="flex space-x-2">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full
                                {{ $equipment->status === 'available' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                {{ ucfirst($equipment->status) }}
                            </span>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full
                                {{ $equipment->condition === 'good' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 
                                   ($equipment->condition === 'fair' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100') }}">
                                {{ ucfirst($equipment->condition) }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</h2>
                            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $equipment->category->name }}</p>
                        </div>

                        <div>
                            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</h2>
                            <p class="mt-1 text-gray-600 dark:text-gray-300">{{ $equipment->description }}</p>
                        </div>

                        <div>
                            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Quantity</h2>
                            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $equipment->quantity }}</p>
                        </div>

                        <div>
                            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</h2>
                            <p class="mt-1 text-gray-600 dark:text-gray-300">{{ $equipment->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex space-x-4">
                            <a href="{{ route('admin.equipment.edit', $equipment) }}" 
                               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition-colors duration-200">
                                Edit Equipment
                            </a>
                            <form action="{{ route('admin.equipment.destroy', $equipment) }}" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete this equipment?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition-colors duration-200">
                                    Delete Equipment
                                </button>
                            </form>
                        </div>

                        <form action="{{ route('admin.equipment.update-status', $equipment) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <select name="status" 
                                    onchange="this.form.submit()"
                                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="available" {{ $equipment->status === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="unavailable" {{ $equipment->status === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Booking History Section -->
            <div class="p-8 border-t border-gray-200 dark:border-gray-700">
                <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Booking History</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Start Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">End Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($equipment->bookings as $booking)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">{{ $booking->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">{{ $booking->start_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">{{ $booking->end_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                                            {{ $booking->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 
                                               ($booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100') }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No booking history found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 