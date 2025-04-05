<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Messages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between mb-6">
                        <h3 class="text-lg font-semibold">Inbox</h3>
                        <div>
                            <a href="{{ route('messages.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Compose New Message</a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-3 px-4 border-b text-left">From</th>
                                    <th class="py-3 px-4 border-b text-left">Subject</th>
                                    <th class="py-3 px-4 border-b text-left">Date</th>
                                    <th class="py-3 px-4 border-b text-left">Status</th>
                                    <th class="py-3 px-4 border-b text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($messages as $message)
                                    <tr class="{{ !$message->read_at ? 'bg-gray-50 font-semibold' : '' }}">
                                        <td class="py-3 px-4 border-b">{{ $message->sender->name }}</td>
                                        <td class="py-3 px-4 border-b">{{ $message->subject ?? 'No Subject' }}</td>
                                        <td class="py-3 px-4 border-b">{{ $message->created_at->format('M d, Y H:i') }}</td>
                                        <td class="py-3 px-4 border-b">
                                            @if($message->read_at)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Read</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Unread</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <a href="{{ route('messages.show', $message->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-3 px-4 border-b text-center">No messages found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 