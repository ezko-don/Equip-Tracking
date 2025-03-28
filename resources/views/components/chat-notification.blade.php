<!-- Chat and Notification Floating Icons -->
<div class="fixed bottom-6 right-6 flex flex-col space-y-4 z-50">
    <!-- Notifications Icon -->
    <div x-data="{ unreadCount: 0 }" 
         x-init="
            fetch('/admin/notifications/unread-count')
                .then(response => response.json())
                .then(data => unreadCount = data.count);
            
            Echo.private('notifications.' + {{ auth()->id() }})
                .listen('NewNotification', (e) => {
                    unreadCount++;
                });
         "
         @click="window.location.href = '{{ route('admin.notifications.index') }}'"
         class="relative p-3 bg-white dark:bg-gray-800 rounded-full shadow-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
        <div x-show="unreadCount > 0"
             class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
            <span x-text="unreadCount"></span>
        </div>
        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
    </div>

    <!-- Chat Icon -->
    <div x-data="{ unreadMessages: 0 }" 
         x-init="
            fetch('/api/messages/unread-count')
                .then(response => response.json())
                .then(data => unreadMessages = data.count);
            
            Echo.private('messages.' + {{ auth()->id() }})
                .listen('NewMessage', (e) => {
                    unreadMessages++;
                });
         "
         @click="window.location.href = '{{ route('messages.index') }}'"
         class="relative p-3 bg-white dark:bg-gray-800 rounded-full shadow-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
        <div x-show="unreadMessages > 0"
             class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
            <span x-text="unreadMessages"></span>
        </div>
        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
    </div>
</div>

<!-- Notification Panel -->
<div id="notificationPanel" class="hidden fixed bottom-24 right-6 w-80 bg-white rounded-lg shadow-xl">
    <div class="p-4 border-b">
        <h3 class="text-lg font-semibold">Notifications</h3>
    </div>
    <div class="max-h-96 overflow-y-auto p-4">
        @if(Schema::hasTable('notifications') && Schema::hasColumns('notifications', ['notifiable_type', 'notifiable_id']))
            @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                <div class="mb-4 p-3 bg-gray-50 rounded-lg {{ $notification->read_at ? '' : 'border-l-4 border-blue-500' }}">
                    <p class="text-sm">{{ $notification->data['message'] ?? 'No message' }}</p>
                    <small class="text-gray-500">{{ $notification->created_at->diffForHumans() }}</small>
                </div>
            @empty
                <p class="text-gray-500 text-center">No notifications</p>
            @endforelse
        @else
            <p class="text-gray-500 text-center">Notifications system is being set up</p>
        @endif
    </div>
</div>

<!-- Chat Panel -->
<div id="chatPanel" class="hidden fixed bottom-24 right-6 w-96 bg-white rounded-lg shadow-xl">
    <div class="p-4 border-b flex justify-between items-center">
        <h3 class="text-lg font-semibold">Messages</h3>
        <button onclick="showNewChat()" class="text-blue-600 hover:text-blue-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
        </button>
    </div>

    <!-- User Selection Panel -->
    <div id="newChatPanel" class="hidden">
        <div class="p-4 border-b">
            <input type="text" 
                id="userSearch" 
                class="w-full px-3 py-2 border rounded-lg" 
                placeholder="Search users..."
                onkeyup="searchUsers(this.value)"
            >
        </div>
        <div id="userList" class="max-h-60 overflow-y-auto p-2">
            <!-- Users will be loaded here -->
        </div>
    </div>

    <!-- Chat Messages -->
    <div id="chatMessages" class="max-h-96 overflow-y-auto p-4 space-y-4">
        <!-- Messages will be loaded here -->
    </div>

    <!-- Message Input -->
    <div class="p-4 border-t">
        <div class="flex space-x-2">
            <input type="text" 
                id="messageInput" 
                class="flex-1 rounded-lg border border-gray-300 px-4 py-2" 
                placeholder="Type a message..."
            >
            <button onclick="sendMessage()" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Send</button>
        </div>
    </div>
</div>

<!-- Add Pusher and Laravel Echo for real-time updates -->
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ config('broadcasting.connections.pusher.key') }}',
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        encrypted: true
    });
</script>

<script>
let selectedUserId = null;

function toggleNotifications() {
    const panel = document.getElementById('notificationPanel');
    const chatPanel = document.getElementById('chatPanel');
    chatPanel.classList.add('hidden');
    panel.classList.toggle('hidden');
}

function toggleChat() {
    const panel = document.getElementById('chatPanel');
    const notificationPanel = document.getElementById('notificationPanel');
    notificationPanel.classList.add('hidden');
    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) {
        loadMessages();
    }
}

function showNewChat() {
    const newChatPanel = document.getElementById('newChatPanel');
    const chatMessages = document.getElementById('chatMessages');
    newChatPanel.classList.toggle('hidden');
    chatMessages.classList.toggle('hidden');
    loadUsers();
}

function searchUsers(query) {
    fetch(`/api/users/search?q=${query}`)
        .then(response => response.json())
        .then(users => {
            const userList = document.getElementById('userList');
            userList.innerHTML = users.map(user => `
                <div class="user-item p-2 hover:bg-gray-100 cursor-pointer rounded-lg flex items-center"
                     onclick="selectUser(${user.id}, '${user.name}')">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white">
                        ${user.name.charAt(0)}
                    </div>
                    <span class="ml-2">${user.name}</span>
                </div>
            `).join('');
        });
}

function selectUser(userId, userName) {
    selectedUserId = userId;
    const newChatPanel = document.getElementById('newChatPanel');
    const chatMessages = document.getElementById('chatMessages');
    newChatPanel.classList.add('hidden');
    chatMessages.classList.remove('hidden');
    loadMessages(userId);
}

function loadMessages(userId = selectedUserId) {
    if (!userId) return;
    
    fetch(`/api/messages/${userId}`)
        .then(response => response.json())
        .then(messages => {
            const chatMessages = document.getElementById('chatMessages');
            chatMessages.innerHTML = messages.map(message => `
                <div class="message ${message.sender_id === {{ auth()->id() }} ? 'ml-auto' : ''} max-w-[75%]">
                    <div class="bg-${message.sender_id === {{ auth()->id() }} ? 'blue-500 text-white' : 'gray-100'} rounded-lg p-3">
                        <p class="text-sm">${message.message}</p>
                        <small class="text-${message.sender_id === {{ auth()->id() }} ? 'blue-100' : 'gray-500'}">${message.created_at}</small>
                    </div>
                </div>
            `).join('');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });
}

function sendMessage() {
    if (!selectedUserId) {
        alert('Please select a user to chat with');
        return;
    }

    const input = document.getElementById('messageInput');
    const content = input.value.trim();
    if (!content) return;

    fetch('/api/messages', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ 
            receiver_id: selectedUserId,
            message: content 
        })
    })
    .then(response => response.json())
    .then(() => {
        input.value = '';
        loadMessages();
    });
}
</script> 