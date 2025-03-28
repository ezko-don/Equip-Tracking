<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($notifications->count() > 0)
                        <div class="mb-4">
                            <form action="{{ route('notifications.mark-all-as-read') }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                    Mark All as Read
                                </button>
                            </form>
                        </div>

                        <div class="space-y-4">
                            @foreach($notifications as $notification)
                                <div class="p-4 {{ $notification->read_at ? 'bg-gray-50' : 'bg-blue-50' }} rounded-lg">
                                    <div class="flex justify-between">
                                        <div>
                                            <p class="text-sm text-gray-600">{{ $notification->created_at->diffForHumans() }}</p>
                                            <p class="mt-1">
                                                @if(isset($notification->data['equipment_name']) && isset($notification->data['status']))
                                                    Booking for {{ $notification->data['equipment_name'] }} 
                                                    has been {{ $notification->data['status'] }}
                                                @else
                                                    Notification received
                                                @endif
                                            </p>
                                            @if(isset($notification->data['event_name']))
                                                <p class="text-sm text-gray-600">
                                                    Event: {{ $notification->data['event_name'] }}
                                                </p>
                                            @endif
                                        </div>
                                        @unless($notification->read_at)
                                            <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-blue-600 hover:text-blue-800">
                                                    Mark as Read
                                                </button>
                                            </form>
                                        @endunless
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{ $notifications->links() }}
                    @else
                        <p class="text-gray-500 text-center">No notifications found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 