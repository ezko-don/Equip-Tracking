<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Booking Details') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('bookings.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Bookings
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Booking Status -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Booking #{{ $booking->id }}</h3>
                                <p class="mt-1 text-sm text-gray-500">Created on {{ $booking->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($booking->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                   ($booking->status === 'rejected' ? 'bg-red-100 text-red-800' : 
                                   ($booking->status === 'completed' ? 'bg-blue-100 text-blue-800' : 
                                   'bg-gray-100 text-gray-800'))) }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Equipment</h4>
                            <div class="mt-1">
                                <p class="text-sm text-gray-900">{{ $booking->equipment->name }}</p>
                                <p class="text-sm text-gray-500">{{ $booking->equipment->category->name }}</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Booked By</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $booking->user->name }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Event</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $booking->event_name }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Location</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $booking->location }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Start Time</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $booking->start_time->format('M d, Y H:i') }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">End Time</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $booking->end_time->format('M d, Y H:i') }}</p>
                        </div>
                    </div>

                    @if($booking->notes)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-500">Notes</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $booking->notes }}</p>
                    </div>
                    @endif

                    <!-- Status Management -->
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-4">Status Management</h4>
                        <div class="flex space-x-4">
                            @if($booking->isPending())
                                @can('approve', $booking)
                                <form action="{{ route('bookings.approve', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Approve Booking
                                    </button>
                                </form>
                                @endcan
                                
                                @can('approve', $booking)
                                <form action="{{ route('bookings.reject', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Reject Booking
                                    </button>
                                </form>
                                @endcan
                            @endif

                            @if($booking->isApproved())
                                @can('complete', $booking)
                                <form action="{{ route('bookings.complete', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Mark as Completed
                                    </button>
                                </form>
                                @endcan
                            @endif

                            @if($booking->isPending() || $booking->isApproved())
                                @can('cancel', $booking)
                                <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                            onclick="return confirm('Are you sure you want to cancel this booking?')">
                                        Cancel Booking
                                    </button>
                                </form>
                                @endcan
                            @endif

                            @if($booking->isApproved() && !$booking->isPendingReturn() && !$booking->isCompleted() && !$booking->isCancelled())
                                <button type="button" 
                                        onclick="openReturnModal()"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Return Equipment
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Equipment Status -->
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-4">Equipment Status</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Current Status</p>
                                <p class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $booking->equipment->status === 'available' ? 'bg-green-100 text-green-800' : 
                                           ($booking->equipment->status === 'in_use' ? 'bg-blue-100 text-blue-800' : 
                                           ($booking->equipment->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 
                                           'bg-red-100 text-red-800')) }}">
                                        {{ ucfirst($booking->equipment->status) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Condition</p>
                                <p class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $booking->equipment->condition === 'new' ? 'bg-green-100 text-green-800' : 
                                           ($booking->equipment->condition === 'good' ? 'bg-blue-100 text-blue-800' : 
                                           ($booking->equipment->condition === 'fair' ? 'bg-yellow-100 text-yellow-800' : 
                                           'bg-red-100 text-red-800')) }}">
                                        {{ ucfirst($booking->equipment->condition) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Return Equipment Modal -->
    <div id="returnModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Return Equipment</h3>
                <form action="{{ route('bookings.return', $booking) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="equipment_condition" class="block text-sm font-medium text-gray-700 mb-2">Equipment Condition</label>
                        <select name="equipment_condition" id="equipment_condition" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select Condition</option>
                            <option value="good">Good - Ready for next booking</option>
                            <option value="damaged">Damaged - Needs repair</option>
                            <option value="needs_maintenance">Needs Maintenance</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="return_notes" class="block text-sm font-medium text-gray-700 mb-2">Return Notes</label>
                        <textarea name="return_notes" id="return_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter any notes about the equipment's condition"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeReturnModal()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Process Return</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openReturnModal() {
            const modal = document.getElementById('returnModal');
            modal.classList.remove('hidden');
        }

        function closeReturnModal() {
            const modal = document.getElementById('returnModal');
            modal.classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('returnModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReturnModal();
            }
        });
    </script>
    @endpush
</x-app-layout> 