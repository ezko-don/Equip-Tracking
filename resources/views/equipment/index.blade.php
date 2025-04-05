<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Equipment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filter Section -->
            <div class="mb-6 flex justify-between items-center">
                <div class="flex-1 max-w-lg">
                    <input type="text" 
                           placeholder="Search equipment..." 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                @if(auth()->user()->isAdmin())
                <div class="ml-4">
                    <a href="{{ route('equipment.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create New Equipment
                    </a>
                </div>
                @endif
            </div>

            <!-- Equipment Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($equipment as $item)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-all duration-300">
                        <div class="p-6">
                            <!-- Equipment Image -->
                            <div class="aspect-w-16 aspect-h-9 mb-4">
                                @if($item->image)
                                    <img src="{{ Storage::url($item->image) }}" 
                                         alt="{{ $item->name }}" 
                                         class="w-full h-48 object-cover rounded-lg">
                                @else
                                    <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Equipment Details -->
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ $item->name }}
                            </h3>

                            <!-- Status and Category Tags -->
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $item->status_badge_class }}">
                                    {{ $item->display_status }}
                                </span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ $item->category->name }}
                                </span>
                            </div>

                            <!-- Description -->
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                {{ Str::limit($item->description, 100) }}
                            </p>
                            
                            @if(!$item->is_available && $item->hasActiveBooking())
                                <p class="text-xs text-gray-500 mb-4">
                                    <span class="font-medium">Available after:</span> 
                                    {{ $item->next_available_date }}
                                </p>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex justify-between items-center mt-4">
                                <a href="{{ route('equipment.show', $item) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View Details →
                                </a>
                                @if($item->is_available)
                                    <a href="{{ route('bookings.create', ['equipment' => $item->id]) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Book Now
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <p class="mt-2 text-gray-500 dark:text-gray-400">No equipment available at the moment.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($equipment->hasPages())
                <div class="mt-6">
                    {{ $equipment->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        /* Your styles here */
    </style>
    @endpush
</x-app-layout> 