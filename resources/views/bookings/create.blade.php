<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Book Equipment') }}
            </h2>
            @if($equipment)
                <a href="{{ route('equipment.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Equipment List
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($equipment)
                        <!-- Equipment Info -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Equipment Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Name</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $equipment->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Category</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $equipment->category->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Status</p>
                                    <p class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $equipment->status === 'available' ? 'bg-green-100 text-green-800' : 
                                               ($equipment->status === 'in_use' ? 'bg-blue-100 text-blue-800' : 
                                               ($equipment->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 
                                               'bg-red-100 text-red-800')) }}">
                                            {{ ucfirst($equipment->status) }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Condition</p>
                                    <p class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $equipment->condition === 'new' ? 'bg-green-100 text-green-800' : 
                                               ($equipment->condition === 'good' ? 'bg-blue-100 text-blue-800' : 
                                               ($equipment->condition === 'fair' ? 'bg-yellow-100 text-yellow-800' : 
                                               'bg-red-100 text-red-800')) }}">
                                            {{ ucfirst($equipment->condition) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Equipment Selection -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Select Equipment</h3>
                            <select name="equipment_id" id="equipment_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select equipment...</option>
                                @foreach($equipmentList as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Booking Form -->
                    <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                        @csrf
                        @if($equipment)
                            <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="event_name" class="block text-sm font-medium text-gray-700">Event Name</label>
                                <input type="text" name="event_name" id="event_name" value="{{ old('event_name') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('event_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                                <input type="text" name="location" id="location" value="{{ old('location') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('location')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                                <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('start_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                                <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('end_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Availability Check Result -->
                        <div id="availabilityResult" class="mt-4 hidden">
                            <div class="rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800" id="availabilityMessage"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-4">
                            <a href="{{ route('equipment.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Submit Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function checkAvailability() {
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;
            const resultDiv = document.getElementById('availabilityResult');
            const messageElement = document.getElementById('availabilityMessage');
            const equipmentId = document.getElementById('equipment_id')?.value || '{{ $equipment->id ?? "" }}';

            if (!startTime || !endTime || !equipmentId) return;

            fetch(`/bookings/${equipmentId}/check-availability`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    start_time: startTime,
                    end_time: endTime
                })
            })
            .then(response => response.json())
            .then(data => {
                resultDiv.classList.remove('hidden');
                messageElement.textContent = data.message;
                resultDiv.firstElementChild.classList.remove('bg-red-50', 'bg-green-50');
                resultDiv.firstElementChild.classList.add(data.available ? 'bg-green-50' : 'bg-red-50');
                messageElement.classList.remove('text-red-800', 'text-green-800');
                messageElement.classList.add(data.available ? 'text-green-800' : 'text-red-800');
            })
            .catch(error => {
                console.error('Error:', error);
                messageElement.textContent = 'Error checking availability. Please try again.';
                resultDiv.classList.remove('hidden');
                resultDiv.firstElementChild.classList.add('bg-red-50');
                messageElement.classList.add('text-red-800');
            });
        }

        // Add event listeners
        document.getElementById('start_time').addEventListener('change', checkAvailability);
        document.getElementById('end_time').addEventListener('change', checkAvailability);
        
        // Add event listener for equipment selection if it exists
        const equipmentSelect = document.getElementById('equipment_id');
        if (equipmentSelect) {
            equipmentSelect.addEventListener('change', checkAvailability);
        }
    </script>
    @endpush
</x-app-layout> 