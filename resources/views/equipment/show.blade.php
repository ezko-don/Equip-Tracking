<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Equipment Details') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="md:flex">
                <div class="md:w-1/3">
                    @if($equipment->photo)
                        <img src="{{ Storage::url($equipment->photo) }}" alt="{{ $equipment->name }}" class="w-full h-64 object-cover">
                    @else
                        <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400">No image available</span>
                        </div>
                    @endif
                </div>
                
                <div class="md:w-2/3 p-6">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $equipment->name }}</h1>
                    <p class="text-gray-600 mb-4">{{ $equipment->description }}</p>
                    
                    <div class="mb-4">
                        <span class="px-3 py-1 text-sm rounded-full {{ $equipment->status_badge_class }}">
                            {{ $equipment->display_status }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700">Category</h3>
                            <p class="text-gray-600">{{ $equipment->category->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700">Condition</h3>
                            <p class="text-gray-600">{{ ucfirst($equipment->condition) }}</p>
                        </div>
                    </div>

                    @if(!$equipment->is_available)
                        @if($equipment->current_booking)
                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-gray-700">Currently Booked Until</h3>
                                <p class="text-gray-600">{{ $equipment->current_booking->end_time->format('M d, Y H:i') }}</p>
                            </div>
                        @endif
                        @if($equipment->next_available_date)
                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-gray-700">Next Available Date</h3>
                                <p class="text-gray-600">{{ $equipment->next_available_date }}</p>
                            </div>
                        @endif
                    @endif

                    <div class="mt-6">
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('equipment.edit', $equipment->id) }}" 
                               class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                                Edit Equipment
                            </a>
                        @endif
                        @if($equipment->is_available)
                            <a href="{{ route('bookings.create', ['equipment' => $equipment->id]) }}" 
                               class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                                Book Now
                            </a>
                        @endif
                        <a href="{{ route('equipment.index') }}" 
                           class="inline-block bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 ml-2">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>

            @if($equipment->upcoming_bookings->isNotEmpty())
            <div class="border-t border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Bookings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($equipment->upcoming_bookings as $booking)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-600">
                            <div class="font-semibold">Start:</div>
                            <div class="mb-2">{{ $booking->start_time->format('M d, Y H:i') }}</div>
                            <div class="font-semibold">End:</div>
                            <div>{{ $booking->end_time->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout> 