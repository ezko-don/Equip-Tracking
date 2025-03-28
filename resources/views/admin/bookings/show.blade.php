@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Booking Details</h1>
        <a href="{{ route('admin.bookings.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            Back to List
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Equipment Information</h2>
                <div class="space-y-3">
                    <p><span class="font-medium">Name:</span> {{ $booking->equipment->name }}</p>
                    <p><span class="font-medium">Category:</span> {{ $booking->equipment->category->name }}</p>
                    <p><span class="font-medium">Status:</span> {{ $booking->equipment->status }}</p>
                    <p><span class="font-medium">Condition:</span> {{ $booking->equipment->condition }}</p>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">User Information</h2>
                <div class="space-y-3">
                    <p><span class="font-medium">Name:</span> {{ $booking->user->name }}</p>
                    <p><span class="font-medium">Email:</span> {{ $booking->user->email }}</p>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Booking Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <p><span class="font-medium">Start Date:</span> {{ $booking->start_date->format('Y-m-d H:i') }}</p>
                    <p><span class="font-medium">End Date:</span> {{ $booking->end_date->format('Y-m-d H:i') }}</p>
                    <p><span class="font-medium">Status:</span> 
                        <span class="px-2 py-1 text-sm rounded {{ $booking->status_badge_class }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </p>
                </div>
                <div class="space-y-3">
                    <p><span class="font-medium">Purpose:</span></p>
                    <p class="text-gray-700">{{ $booking->purpose }}</p>
                    @if($booking->notes)
                        <p><span class="font-medium">Notes:</span></p>
                        <p class="text-gray-700">{{ $booking->notes }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-8 flex gap-4">
            @if($booking->status === 'pending')
                <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        Approve Booking
                    </button>
                </form>

                <form action="{{ route('admin.bookings.reject', $booking) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                        Reject Booking
                    </button>
                </form>
            @endif

            @if($booking->status !== 'cancelled')
                <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" 
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded"
                            onclick="return confirm('Are you sure you want to cancel this booking?')">
                        Cancel Booking
                    </button>
                </form>
            @endif

            <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" class="inline" 
                  onsubmit="return confirm('Are you sure you want to delete this booking?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    Delete Booking
                </button>
            </form>
        </div>
    </div>
</div>
@endsection 