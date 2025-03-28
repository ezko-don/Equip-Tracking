<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="p-4 bg-gray-50 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">Messages</h2>
                </div>
                
                <div x-data="messageSystem()" class="flex h-[600px]">
                    <!-- Users List -->
                    <div class="w-1/3 border-r bg-gray-50">
                        <div class="p-4">
                            <div class="relative">
                                <input 
                                    type="text" 
                                    x-model="searchQuery" 
                                    @input="searchUsers"
                                    placeholder="Search users..." 
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                            </div>
                        </div>
                        
                        <!-- Users List -->
                        <div class="overflow-y-auto h-[calc(100%-80px)]">
                            <template x-for="user in users" :key="user.id">
                                <div 
                                    @click="selectUser(user)"
                                    :class="{'bg-blue-50': selectedUser && selectedUser.id === user.id}"
                                    class="p-4 border-b hover:bg-gray-100 cursor-pointer transition duration-150 ease-in-out"
                                >
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                <span x-text="user.name.charAt(0).toUpperCase()" class="text-lg font-semibold text-white"></span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium text-gray-900" x-text="user.name"></div>
                                            <div class="text-sm text-gray-500" x-text="user.email"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Chat Area -->
                    <div class="w-2/3 flex flex-col">
                        <template x-if="selectedUser">
                            <div class="p-4 bg-gray-50 border-b">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
                                        <span x-text="selectedUser.name.charAt(0).toUpperCase()" class="text-lg font-semibold text-white"></span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium text-gray-900" x-text="selectedUser.name"></div>
                                        <div class="text-sm text-gray-500" x-text="selectedUser.email"></div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="flex-1 overflow-y-auto p-4" id="chat-messages">
                            <template x-for="message in messages" :key="message.id">
                                <div :class="{'flex justify-end': message.sender_id === {{ auth()->id() }}, 'flex justify-start': message.sender_id !== {{ auth()->id() }}}">
                                    <div 
                                        :class="{'bg-blue-500 text-white': message.sender_id === {{ auth()->id() }}, 'bg-gray-200': message.sender_id !== {{ auth()->id() }}}"
                                        class="max-w-[70%] rounded-lg px-4 py-2 my-2"
                                    >
                                        <div x-text="message.content"></div>
                                        <div 
                                            :class="{'text-blue-200': message.sender_id === {{ auth()->id() }}, 'text-gray-500': message.sender_id !== {{ auth()->id() }}}"
                                            class="text-xs mt-1"
                                            x-text="formatDate(message.created_at)"
                                        ></div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Message Input -->
                        <div class="p-4 border-t">
                            <form @submit.prevent="sendMessage" x-show="selectedUser" class="flex gap-2">
                                <input 
                                    type="text" 
                                    x-model="newMessage" 
                                    placeholder="Type your message..." 
                                    class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :disabled="!selectedUser"
                                >
                                <button 
                                    type="submit" 
                                    class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150 ease-in-out"
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
                this.fetchUsers();
                this.pollMessages();
            },

            async fetchUsers() {
                try {
                    const response = await fetch('/api/users/search');
                    if (!response.ok) throw new Error('Failed to fetch users');
                    const data = await response.json();
                    this.users = data;
                } catch (error) {
                    console.error('Error fetching users:', error);
                    this.error = 'Failed to load users. Please refresh the page.';
                }
            },

            async searchUsers() {
                try {
                    const response = await fetch(`/api/users/search?query=${encodeURIComponent(this.searchQuery)}`);
                    if (!response.ok) throw new Error('Failed to search users');
                    const data = await response.json();
                    this.users = data;
                } catch (error) {
                    console.error('Error searching users:', error);
                    this.error = 'Failed to search users. Please try again.';
                }
            },

            async selectUser(user) {
                this.selectedUser = user;
                await this.fetchMessages();
                this.scrollToBottom();
            },

            async fetchMessages() {
                if (!this.selectedUser) return;

                try {
                    const response = await fetch(`/api/messages?user_id=${this.selectedUser.id}`);
                    if (!response.ok) throw new Error('Failed to fetch messages');
                    const data = await response.json();
                    this.messages = data;
                    this.scrollToBottom();
                } catch (error) {
                    console.error('Error fetching messages:', error);
                    this.error = 'Failed to load messages. Please refresh the page.';
                }
            },

            async sendMessage() {
                if (!this.newMessage.trim() || !this.selectedUser) return;

                try {
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    if (!token) throw new Error('CSRF token not found');

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
                        const data = await response.json();
                        throw new Error(data.message || 'Failed to send message');
                    }

                    const data = await response.json();
                    this.messages.push(data);
                    this.newMessage = '';
                    this.scrollToBottom();
                } catch (error) {
                    console.error('Error sending message:', error);
                    this.error = error.message || 'Failed to send message. Please try again.';
                    setTimeout(() => this.error = null, 3000);
                }
            },

            pollMessages() {
                setInterval(async () => {
                    if (this.selectedUser) {
                        await this.fetchMessages();
                    }
                }, 3000);
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
                const date = new Date(dateString);
                return date.toLocaleString();
            }
        }
    }
    </script>
    @endpush

    <!-- Add error notification -->
    <div x-show="error" 
         x-cloak 
         class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"
         role="alert">
        <span class="block sm:inline" x-text="error"></span>
    </div>
</x-app-layout> 