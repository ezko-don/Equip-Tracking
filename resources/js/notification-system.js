document.addEventListener('alpine:init', () => {
    Alpine.data('notificationSystem', () => ({
        open: false,
        notifications: [],
        unreadCount: 0,

        init() {
            this.fetchNotifications();
            this.startPolling();
        },

        async fetchNotifications() {
            try {
                const response = await fetch('/admin/notifications');
                const data = await response.json();
                this.notifications = data;
                this.unreadCount = data.length;
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
            }
        },

        async markAsRead(id) {
            try {
                await fetch(`/admin/notifications/${id}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                this.notifications = this.notifications.filter(n => n.id !== id);
                this.unreadCount = this.notifications.length;
            } catch (error) {
                console.error('Failed to mark notification as read:', error);
            }
        },

        async markAllAsRead() {
            try {
                await fetch('/admin/notifications/mark-all-as-read', {
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
            setInterval(() => this.fetchNotifications(), 60000); // Poll every minute
        },

        handleNewNotification(notification) {
            this.notifications.unshift(notification);
            this.unreadCount++;
        }
    }));
}); 