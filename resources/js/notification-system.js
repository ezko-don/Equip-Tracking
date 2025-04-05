document.addEventListener('alpine:init', () => {
    Alpine.data('notificationSystem', () => ({
        open: false,
        notifications: [],
        unreadCount: 0,
        isAdmin: false,

        init() {
            // Check if current user is admin based on URL path
            this.isAdmin = window.location.pathname.includes('/admin');
            this.fetchNotifications();
            this.fetchUnreadCount();
            this.startPolling();
        },

        async fetchNotifications() {
            try {
                // Use the right endpoint based on whether this is admin or user
                const endpoint = this.isAdmin ? '/admin/notifications' : '/notifications';
                const response = await fetch(endpoint);
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                const data = await response.json();
                this.notifications = data;
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
            }
        },
        
        async fetchUnreadCount() {
            try {
                // Use the right endpoint based on whether this is admin or user
                const endpoint = this.isAdmin 
                    ? '/admin/notifications/unread-count' 
                    : '/notifications/unread-count';
                    
                const response = await fetch(endpoint);
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                const data = await response.json();
                this.unreadCount = data.count;
            } catch (error) {
                console.error('Failed to fetch unread count:', error);
            }
        },

        async markAsRead(id) {
            try {
                // Use the right endpoint based on whether this is admin or user
                const endpoint = this.isAdmin 
                    ? `/admin/notifications/${id}/mark-as-read` 
                    : `/notifications/${id}/mark-as-read`;
                    
                await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                this.notifications = this.notifications.filter(n => n.id !== id);
                this.fetchUnreadCount();
            } catch (error) {
                console.error('Failed to mark notification as read:', error);
            }
        },

        async markAllAsRead() {
            try {
                // Use the right endpoint based on whether this is admin or user
                const endpoint = this.isAdmin 
                    ? '/admin/notifications/mark-all-as-read' 
                    : '/notifications/mark-all-as-read';
                    
                await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                this.notifications = [];
                this.unreadCount = 0;
            } catch (error) {
                console.error('Failed to mark all notifications as read:', error);
            }
        },

        goToNotification(notification) {
            if (notification.url) {
                window.location.href = notification.url;
            }
        },

        startPolling() {
            setInterval(() => {
                this.fetchNotifications();
                this.fetchUnreadCount();
            }, 60000); // Poll every minute
        },

        handleNewNotification(notification) {
            this.notifications.unshift(notification);
            this.unreadCount++;
        }
    }));
}); 