@props(['action', 'booking', 'color', 'label'])

<form action="{{ route('bookings.' . $action, $booking) }}" method="POST" class="inline">
    @csrf
    @method('PATCH')
    <button type="submit" 
            {{ $attributes->merge(['class' => "text-$color-600 hover:text-$color-900"]) }}
            onclick="return confirm('Are you sure you want to {{ strtolower($label) }} this booking?')">
        {{ $label }}
    </button>
</form> 