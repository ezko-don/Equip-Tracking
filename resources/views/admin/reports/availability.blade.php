<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Equipment Availability Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="text-sm font-medium text-gray-500">Total Equipment</div>
                    <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $equipment->count() }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="text-sm font-medium text-gray-500">Currently Available</div>
                    <div class="mt-2 text-3xl font-semibold text-green-600">
                        {{ $equipment->where('status', 'available')->count() }}
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="text-sm font-medium text-gray-500">Currently Booked</div>
                    <div class="mt-2 text-3xl font-semibold text-blue-600">
                        {{ $equipment->where('status', 'booked')->count() }}
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="text-sm font-medium text-gray-500">Under Maintenance</div>
                    <div class="mt-2 text-3xl font-semibold text-red-600">
                        {{ $equipment->where('status', 'maintenance')->count() }}
                    </div>
                </div>
            </div>

            <!-- Detailed List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Equipment Status Details</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active Bookings</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending Bookings</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Available</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($equipment as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <x-equipment-image :equipment="$item" class="h-10 w-10 rounded-full" />
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $item->category->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $item->status === 'available' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $item->status === 'booked' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $item->status === 'maintenance' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item->active_bookings_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item->pending_bookings_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($item->status === 'available')
                                            Now
                                        @else
                                            {{ $item->next_available_date ? \Carbon\Carbon::parse($item->next_available_date)->format('M d, Y') : 'Unknown' }}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 