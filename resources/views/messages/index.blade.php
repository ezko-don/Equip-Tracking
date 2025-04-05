<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div x-data="messageSystem()" class="space-y-6">
                        <!-- User Selection Section -->
                        <div>
                            <h3 class="text-lg font-medium mb-4">Select Recipients</h3>
                            <div class="flex flex-col space-y-4">
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        x-model="searchQuery" 
                                        @input="searchUsers"
                                        placeholder="Search users..." 
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600"
                                    >
                                </div>
                                
                                <!-- Selected Users Pills -->
                                <div class="flex flex-wrap gap-2 mb-4" x-show="selectedUsers.length > 0">
                                    <template x-for="user in selectedUsers" :key="user.id">
                                        <div class="flex items-center bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full px-3 py-1">
                                            <span x-text="user.name" class="mr-1"></span>
                                            <button @click="removeUser(user)" class="text-blue-500 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                
                                <!-- User List -->
                                <div class="border dark:border-gray-600 rounded-lg max-h-48 overflow-y-auto" x-show="searchResults.length > 0">
                                    <template x-for="user in searchResults" :key="user.id">
                                        <div 
                                            class="p-3 border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer"
                                            @click="toggleUser(user)"
                                        >
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 rounded-full bg-purple-500 flex items-center justify-center">
                                                        <span class="text-sm font-semibold text-white" x-text="user.name.charAt(0).toUpperCase()"></span>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="font-medium" x-text="user.name"></div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400" x-text="user.email"></div>
                                                </div>
                                                <div class="ml-auto">
                                                    <input 
                                                        type="checkbox" 
                                                        :checked="isUserSelected(user)"
                                                        @click.stop="toggleUser(user)"
                                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Message Composition - Now using a form -->
                        <form action="{{ route('messages.send') }}" method="POST" id="messageForm">
                            @csrf
                            
                            <!-- Hidden input for selected users -->
                            <template x-for="user in selectedUsers" :key="user.id">
                                <input type="hidden" name="receiver_ids[]" :value="user.id">
                            </template>
                            
                            <div class="flex justify-between mb-6">
                                <h3 class="text-lg font-semibold">Compose New Message</h3>
                                <div>
                                    <a href="{{ route('messages.inbox') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">View Inbox</a>
                                </div>
                            </div>
                            
                            <div>
                                <div class="space-y-4">
                                    <div>
                                        <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                                        <input 
                                            type="text" 
                                            id="subject" 
                                            name="subject"
                                            x-model="subject"
                                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600"
                                            placeholder="Enter message subject..."
                                            required
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Message</label>
                                        <textarea 
                                            id="message" 
                                            name="content"
                                            x-model="messageContent"
                                            rows="5"
                                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600"
                                            placeholder="Type your message here..."
                                            required
                                        ></textarea>
                                    </div>
                                    
                                    <div class="flex justify-end">
                                        <button 
                                            type="submit"
                                            :disabled="!canSendMessage"
                                            :class="{'bg-blue-500 hover:bg-blue-600': canSendMessage, 'bg-gray-400 cursor-not-allowed': !canSendMessage}"
                                            class="px-4 py-2 text-white rounded-lg transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                            Send Message
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Status Messages -->
                        @if(session('success'))
                            <div class="p-4 rounded-lg bg-green-100 text-green-800">
                                <p>{{ session('success') }}</p>
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="p-4 rounded-lg bg-red-100 text-red-800">
                                <p>{{ session('error') }}</p>
                            </div>
                        @endif
                        
                        <div x-show="statusMessage" class="p-4 rounded-lg" :class="{'bg-green-100 text-green-800': isSuccess, 'bg-red-100 text-red-800': !isSuccess}">
                            <p x-text="statusMessage"></p>
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
            searchQuery: '',
            searchResults: [],
            selectedUsers: [],
            subject: '',
            messageContent: '',
            statusMessage: '',
            isSuccess: true,
            
            init() {
                this.fetchUsers();
                
                // Check for form submission via JavaScript as well (backup)
                document.getElementById('messageForm').addEventListener('submit', (e) => {
                    if (!this.canSendMessage) {
                        e.preventDefault();
                        this.showError('Please select at least one recipient and complete all fields');
                    }
                });
            },

            get canSendMessage() {
                return this.selectedUsers.length > 0 && 
                       this.subject.trim() !== '' && 
                       this.messageContent.trim() !== '';
            },

            async fetchUsers() {
                try {
                    // Get CSRF token from the meta tag
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    const response = await fetch('/api/users/search', {
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin' // Include cookies for session authentication
                    });
                    
                    if (response.status === 401) {
                        this.showError('You need to be logged in. Please refresh the page or log in again.');
                        return;
                    }
                    
                    if (!response.ok) {
                        throw new Error('Failed to fetch users');
                    }
                    
                    const data = await response.json();
                    this.searchResults = data;
                } catch (error) {
                    console.error('Error fetching users:', error);
                    this.showError('Failed to load users. Please refresh the page.');
                }
            },

            async searchUsers() {
                if (this.searchQuery.trim() === '') {
                    this.fetchUsers();
                    return;
                }
                
                try {
                    // Get CSRF token from the meta tag
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    const response = await fetch(`/api/users/search?query=${encodeURIComponent(this.searchQuery)}`, {
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (response.status === 401) {
                        this.showError('You need to be logged in. Please refresh the page or log in again.');
                        return;
                    }
                    
                    if (!response.ok) {
                        throw new Error('Failed to search users');
                    }
                    
                    const data = await response.json();
                    this.searchResults = data;
                } catch (error) {
                    console.error('Error searching users:', error);
                    this.showError('Failed to search users. Please try again.');
                }
            },

            toggleUser(user) {
                const index = this.selectedUsers.findIndex(u => u.id === user.id);
                
                if (index === -1) {
                    this.selectedUsers.push(user);
                } else {
                    this.selectedUsers.splice(index, 1);
                }
            },

            removeUser(user) {
                const index = this.selectedUsers.findIndex(u => u.id === user.id);
                if (index !== -1) {
                    this.selectedUsers.splice(index, 1);
                }
            },

            isUserSelected(user) {
                return this.selectedUsers.some(u => u.id === user.id);
            },

            showSuccess(message) {
                this.statusMessage = message;
                this.isSuccess = true;
                this.clearStatusAfterDelay();
            },

            showError(message) {
                this.statusMessage = message;
                this.isSuccess = false;
                this.clearStatusAfterDelay();
            },

            clearStatusAfterDelay() {
                setTimeout(() => {
                    this.statusMessage = '';
                }, 5000);
            }
        }
    }
    </script>
    @endpush
</x-app-layout> 