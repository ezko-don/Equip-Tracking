<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('View Message') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between mb-6">
                        <h3 class="text-lg font-semibold">{{ $message->subject ?? 'No Subject' }}</h3>
                        <div>
                            <a href="{{ route('messages.inbox') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Back to Inbox</a>
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

                    <div class="bg-gray-50 p-4 rounded mb-4">
                        <div class="flex justify-between mb-2">
                            <div>
                                <span class="font-semibold">From:</span> {{ $message->sender->name }}
                            </div>
                            <div>
                                <span class="font-semibold">Date:</span> {{ $message->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="font-semibold">To:</span> {{ $message->receiver->name }}
                        </div>
                        <div class="mb-2">
                            <span class="font-semibold">Subject:</span> {{ $message->subject ?? 'No Subject' }}
                        </div>
                        <div class="border-t pt-4 mt-4 whitespace-pre-line">
                            {{ $message->content }}
                        </div>
                    </div>

                    @if(auth()->id() == $message->receiver_id)
                        <div class="mt-6">
                            <h4 class="text-lg font-semibold mb-2">Reply</h4>
                            <form action="{{ route('messages.reply', $message->id) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                                    <textarea name="content" id="content" rows="5" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old('content') }}</textarea>
                                    @error('content')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Send Reply</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 