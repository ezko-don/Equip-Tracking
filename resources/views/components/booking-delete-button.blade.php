@props(['booking'])

<form action="{{ route('bookings.destroy', ['booking' => $booking->id]) }}" method="POST" class="inline">
    @csrf
    @method('DELETE')
    <button type="submit" 
            {{ $attributes->merge(['class' => 'text-red-600 hover:text-red-900']) }}
            onclick="return confirm('Are you sure you want to delete this {{ strtolower($booking->status) }} booking? This action cannot be undone.')">
        Delete
    </button>
</form> 