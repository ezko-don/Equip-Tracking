<form action="{{ route('bookings.clear-all') }}" method="POST" class="inline">
    @csrf
    @method('DELETE')
    <button type="submit" 
            {{ $attributes->merge(['class' => 'bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded']) }}
            onclick="return confirm('Are you sure you want to delete all your bookings? This action cannot be undone.')">
        Clear All My Bookings
    </button>
</form> 