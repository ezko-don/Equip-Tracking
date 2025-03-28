<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $equipment->name }}
            </h2>
            <div class="flex space-x-4">
                @can('update', $equipment)
                <a href="{{ route('equipment.edit', $equipment) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Edit Equipment
                </a>
                @endcan
                <a href="{{ route('equipment.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Equipment
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Equipment Details -->
                    <div class="mb-8">
                        <div class="flex items-start space-x-6 mb-6">
                            <x-equipment-image :equipment="$equipment" size="xl" />
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $equipment->name }}</h1>
                                <div class="flex space-x-4 mb-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $equipment->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $equipment->is_available ? 'Available' : 'Unavailable' }}
                                    </span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($equipment->condition === 'good') bg-green-100 text-green-800
                                        @elseif($equipment->condition === 'damaged') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($equipment->condition) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500">Category: {{ $equipment->category->name }}</p>
                                
                                <!-- Add Book Now Button -->
                                @if($equipment->is_available)
                                    <a href="{{ route('bookings.create', ['equipment' => $equipment->id]) }}" 
                                       class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transform hover:scale-105 transition-all duration-200 ease-in-out">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Book Now
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Description</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $equipment->description }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Location</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $equipment->location }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Section -->
                    @if($equipment->is_available)
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Book this Equipment</h3>
                            <form action="{{ route('bookings.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                                        <input type="datetime-local" name="start_time" id="start_time" required
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    
                                    <div>
                                        <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                                        <input type="datetime-local" name="end_time" id="end_time" required
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>

                                <div>
                                    <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose</label>
                                    <textarea name="purpose" id="purpose" rows="3" required
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>

                                <div>
                                    <label for="location" class="block text-sm font-medium text-gray-700">Usage Location</label>
                                    <input type="text" name="location" id="location" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors duration-200">
                                        Submit Booking Request
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- Equipment History -->
                    @if(isset($equipment->bookings) && $equipment->bookings->isNotEmpty())
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Booking History</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($equipment->bookings as $booking)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $booking->created_at->format('M d, Y H:i') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $booking->user->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        @if($booking->status === 'approved') bg-green-100 text-green-800
                                                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                                        @else bg-red-100 text-red-800
                                                        @endif">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $booking->location }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 