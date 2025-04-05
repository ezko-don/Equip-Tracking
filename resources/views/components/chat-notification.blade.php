<!-- Chat and Notification Floating Icons -->
<div class="fixed bottom-6 right-6 flex flex-col space-y-4 z-50" x-data="{ showNotifications: false, showChat: false }">
    <!-- Notifications Icon -->
    <div x-data="{ unreadCount: 0, isAdmin: window.location.pathname.includes('/admin') }" 
         x-init="
            const endpoint = isAdmin ? '/admin/notifications/unread-count' : '/notifications/unread-count';
            fetch(endpoint)
                .then(response => response.json())
                .then(data => unreadCount = data.count);
            
            Echo.private('notifications.' + {{ auth()->id() }})
                .listen('NewNotification', (e) => {
                    unreadCount++;
                });
         "
         @click.prevent="showNotifications = !showNotifications; showChat = false; if(showNotifications) { $dispatch('load-notifications'); }"
         class="relative p-3 bg-white dark:bg-gray-800 rounded-full shadow-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
        <div x-show="unreadCount > 0"
             class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
            <span x-text="unreadCount"></span>
        </div>
        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
    </div>

    <!-- Chat Icon - Modified to redirect to messages page -->
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
         onclick="window.location.href='{{ route('messages.index') }}'"
         class="relative p-3 bg-white dark:bg-gray-800 rounded-full shadow-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
        <div x-show="unreadMessages > 0"
             class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
            <span x-text="unreadMessages"></span>
        </div>
        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
    </div>

    <!-- Notification Panel -->
    <div x-show="showNotifications"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-90"
         x-data="notificationsPanel"
         @load-notifications.window="loadNotifications()"
         @click.away="showNotifications = false"
         class="fixed bottom-24 right-6 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-xl">
        <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Notifications</h3>
            <button @click="markAllAsRead" class="text-sm text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300">
                Mark all as read
            </button>
        </div>
        <div class="max-h-96 overflow-y-auto p-4">
            <div id="notifications-list" class="space-y-4">
                <template x-if="notifications.length === 0">
                    <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                        No new notifications
                    </div>
                </template>
                <template x-for="notification in notifications" :key="notification.id">
                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg shadow-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                        <div class="flex justify-between items-start">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100" x-text="notification.title"></h4>
                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="notification.time"></span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mt-1" x-text="notification.message"></p>
                        <div class="mt-2 flex justify-between">
                            <a x-show="notification.url" :href="notification.url" class="text-xs text-purple-600 dark:text-purple-400 hover:underline">View details</a>
                            <button @click="markAsRead(notification.id)" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                Mark as read
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <div class="p-2 border-t dark:border-gray-700 text-center">
            <a href="{{ route('notifications.index') }}" class="text-sm text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300">
                View all notifications
            </a>
        </div>
    </div>

    <!-- Chat Panel - Now hidden since we redirect to messages page -->
    <div x-show="false"
         class="fixed bottom-24 right-6 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-xl">
        <!-- Hidden content - we now redirect to messages.index -->
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('notificationsPanel', () => ({
        notifications: [],
        isAdmin: window.location.pathname.includes('/admin'),
        
        init() {
            // Only load notifications when the panel is opened
            // Do NOT load automatically
        },
        
        async loadNotifications() {
            try {
                const endpoint = this.isAdmin ? '/admin/notifications' : '/notifications';
                const response = await fetch(endpoint, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to fetch notifications');
                }
                
                this.notifications = await response.json();
            } catch (error) {
                console.error('Error loading notifications:', error);
                this.notifications = [];
            }
        },
        
        async markAsRead(id) {
            try {
                const endpoint = this.isAdmin 
                    ? `/admin/notifications/${id}/mark-as-read` 
                    : `/notifications/${id}/mark-as-read`;
                    
                await fetch(endpoint, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                // Remove this notification from the array
                this.notifications = this.notifications.filter(n => n.id !== id);
                
                // Update the unread count in the badge
                const notificationIcon = document.querySelector('[x-data*="unreadCount"]').__x.$data;
                if (notificationIcon.unreadCount > 0) {
                    notificationIcon.unreadCount--;
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        },
        
        async markAllAsRead() {
            try {
                const endpoint = this.isAdmin 
                    ? '/admin/notifications/mark-all-as-read' 
                    : '/notifications/mark-all-as-read';
                    
                await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                // Clear all notifications and reset the counter
                this.notifications = [];
                const notificationIcon = document.querySelector('[x-data*="unreadCount"]').__x.$data;
                notificationIcon.unreadCount = 0;
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
            }
        }
    }));
});

// Initialize Pusher
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '{{ config('broadcasting.connections.pusher.key') }}',
    cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
    encrypted: true
});
</script> 