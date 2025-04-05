@if(isset($adminView) && $adminView)
    <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Messages') }}
        </h2>
    </x-slot>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Message Center</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Send messages to users from here</p>
                </div>
                
                <div x-data="messageSystem()" class="flex h-[600px]">
                    <!-- Users List for Admin -->
                    <div class="w-1/3 border-r border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                        <div class="p-4">
                            <div class="relative">
                                <input 
                                    type="text" 
                                    x-model="searchQuery" 
                                    @input="searchUsers"
                                    placeholder="Search users..." 
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600"
                                >
                            </div>
                        </div>
                        
                        <!-- Users List -->
                        <div class="overflow-y-auto h-[calc(100%-80px)]">
                            <!-- Show predefined user list for admin -->
                            @foreach($allUsers as $user)
                                <div 
                                    class="p-4 border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer transition duration-150 ease-in-out"
                                    @click="selectUser({id: '{{ $user->id }}', name: '{{ $user->name }}', email: '{{ $user->email }}'})"
                                >
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full bg-purple-500 flex items-center justify-center">
                                                <span class="text-lg font-semibold text-white">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium text-gray-900 dark:text-gray-200">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Chat Area -->
                    <div class="w-2/3 flex flex-col bg-white dark:bg-gray-800">
                        <template x-if="selectedUser">
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-purple-500 flex items-center justify-center">
                                        <span x-text="selectedUser.name.charAt(0).toUpperCase()" class="text-lg font-semibold text-white"></span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium text-gray-900 dark:text-gray-200" x-text="selectedUser.name"></div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedUser.email"></div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="flex-1 overflow-y-auto p-4" id="chat-messages">
                            <template x-if="!selectedUser">
                                <div class="flex items-center justify-center h-full">
                                    <p class="text-gray-500 dark:text-gray-400">Select a user to start messaging</p>
                                </div>
                            </template>
                            
                            <template x-for="message in messages" :key="message.id">
                                <div :class="{'flex justify-end': message.sender_id === {{ auth()->id() }}, 'flex justify-start': message.sender_id !== {{ auth()->id() }}}">
                                    <div 
                                        :class="{'bg-purple-500 text-white': message.sender_id === {{ auth()->id() }}, 'bg-gray-200 dark:bg-gray-600 dark:text-gray-200': message.sender_id !== {{ auth()->id() }}}"
                                        class="max-w-[70%] rounded-lg px-4 py-2 my-2"
                                    >
                                        <div x-text="message.content"></div>
                                        <div 
                                            :class="{'text-purple-200': message.sender_id === {{ auth()->id() }}, 'text-gray-500 dark:text-gray-400': message.sender_id !== {{ auth()->id() }}}"
                                            class="text-xs mt-1"
                                            x-text="formatDate(message.created_at)"
                                        ></div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Message Input -->
                        <div class="p-4 border-t border-gray-200 dark:border-gray-600">
                            <form @submit.prevent="sendMessage" x-show="selectedUser" class="flex gap-2">
                                <input 
                                    type="text" 
                                    x-model="newMessage" 
                                    placeholder="Type your message..." 
                                    class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600"
                                    :disabled="!selectedUser"
                                >
                                <button 
                                    type="submit" 
                                    class="px-6 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-500 transition duration-150 ease-in-out"
                                    :disabled="!newMessage.trim()"
                                >
                                    Send
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function messageSystem() {
        return {
            users: [],
            messages: [],
            selectedUser: null,
            newMessage: '',
            searchQuery: '',
            error: null,
            
            init() {
                console.log('Initializing message system...');
                this.fetchUsers();
                this.pollMessages();
            },

            async fetchUsers() {
                try {
                    console.log('Fetching users...');
                    const response = await fetch('/api/users/search');
                    
                    if (!response.ok) {
                        console.error('Failed to fetch users, status:', response.status);
                        throw new Error('Failed to fetch users');
                    }
                    
                    const data = await response.json();
                    console.log('Users fetched:', data);
                    this.users = data;
                } catch (error) {
                    console.error('Error fetching users:', error);
                    this.error = 'Failed to load users. Please refresh the page.';
                    // Dispatch error event for the notification
                    window.dispatchEvent(new CustomEvent('error-message', { 
                        detail: 'Failed to load users. Please refresh the page.' 
                    }));
                }
            },

            async searchUsers() {
                try {
                    console.log('Searching users with query:', this.searchQuery);
                    const response = await fetch(`/api/users/search?query=${encodeURIComponent(this.searchQuery)}`);
                    
                    if (!response.ok) {
                        console.error('Failed to search users, status:', response.status);
                        throw new Error('Failed to search users');
                    }
                    
                    const data = await response.json();
                    console.log('Search results:', data);
                    this.users = data;
                } catch (error) {
                    console.error('Error searching users:', error);
                    this.error = 'Failed to search users. Please try again.';
                    // Dispatch error event for the notification
                    window.dispatchEvent(new CustomEvent('error-message', { 
                        detail: 'Failed to search users. Please try again.' 
                    }));
                }
            },

            selectUser(user) {
                console.log('Selecting user:', user);
                this.selectedUser = user;
                this.fetchMessages();
                this.scrollToBottom();
            },

            async fetchMessages() {
                if (!this.selectedUser) {
                    console.log('No user selected, skipping message fetch');
                    return;
                }

                try {
                    console.log('Fetching messages for user ID:', this.selectedUser.id);
                    const response = await fetch(`/api/messages?user_id=${this.selectedUser.id}`);
                    
                    if (!response.ok) {
                        console.error('Failed to fetch messages, status:', response.status);
                        throw new Error('Failed to fetch messages');
                    }
                    
                    const data = await response.json();
                    console.log('Messages fetched:', data);
                    this.messages = data;
                    this.scrollToBottom();
                } catch (error) {
                    console.error('Error fetching messages:', error);
                    this.error = 'Failed to load messages. Please refresh the page.';
                    // Dispatch error event for the notification
                    window.dispatchEvent(new CustomEvent('error-message', { 
                        detail: 'Failed to load messages. Please refresh the page.' 
                    }));
                }
            },

            async sendMessage() {
                if (!this.newMessage.trim() || !this.selectedUser) {
                    console.log('No message to send or no user selected');
                    return;
                }

                try {
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    if (!token) {
                        console.error('CSRF token not found');
                        throw new Error('CSRF token not found');
                    }

                    console.log('Sending message to user ID:', this.selectedUser.id);
                    const response = await fetch('/api/messages', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            receiver_id: this.selectedUser.id,
                            content: this.newMessage
                        })
                    });

                    if (!response.ok) {
                        console.error('Failed to send message, status:', response.status);
                        const data = await response.json();
                        throw new Error(data.message || 'Failed to send message');
                    }

                    const data = await response.json();
                    console.log('Message sent:', data);
                    this.messages.push(data);
                    this.newMessage = '';
                    this.scrollToBottom();
                } catch (error) {
                    console.error('Error sending message:', error);
                    this.error = error.message || 'Failed to send message. Please try again.';
                    // Dispatch error event for the notification
                    window.dispatchEvent(new CustomEvent('error-message', { 
                        detail: error.message || 'Failed to send message. Please try again.' 
                    }));
                }
            },

            pollMessages() {
                console.log('Setting up message polling...');
                setInterval(() => {
                    if (this.selectedUser) {
                        this.fetchMessages();
                    }
                }, 5000); // Poll every 5 seconds instead of 3
            },

            scrollToBottom() {
                setTimeout(() => {
                    const chatMessages = document.getElementById('chat-messages');
                    if (chatMessages) {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                }, 100);
            },

            formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleString();
            }
        }
    }
    </script>
    @endpush

    <!-- Add error notification -->
    <div x-data="{ show: false, message: '' }"
         x-show="show" 
         x-cloak 
         @error-message.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)"
         class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"
         role="alert">
        <span class="block sm:inline" x-text="message"></span>
    </div>
    </x-app-layout>
@else
    <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Your Messages</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">View and respond to messages from administrators</p>
                </div>
                
                <!-- Regular user message view content here -->
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <p>If you have any questions or need assistance, administrators will contact you here.</p>
                </div>
            </div>
        </div>
    </div>
    </x-app-layout>
@endif 