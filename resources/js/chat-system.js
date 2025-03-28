document.addEventListener('alpine:init', () => {
    Alpine.data('chatSystem', () => ({
        isChatOpen: false,
        isMinimized: false,
        messages: [],
        newMessage: '',
        unreadCount: 0,
        currentUserId: document.querySelector('meta[name="user-id"]').content,

        init() {
            this.fetchMessages();
            this.listenForMessages();
        },

        async fetchMessages() {
            try {
                const response = await fetch('/chat/messages');
                const data = await response.json();
                this.messages = data.reverse();
                this.scrollToBottom();
            } catch (error) {
                console.error('Failed to fetch messages:', error);
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim()) return;

            try {
                const response = await fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        content: this.newMessage,
                    }),
                });

                const data = await response.json();
                this.messages.push(data);
                this.newMessage = '';
                this.scrollToBottom();
            } catch (error) {
                console.error('Failed to send message:', error);
            }
        },

        toggleChat() {
            this.isChatOpen = !this.isChatOpen;
            if (this.isChatOpen) {
                this.unreadCount = 0;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        minimizeChat() {
            this.isMinimized = !this.isMinimized;
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.getElementById('chat-messages');
                container.scrollTop = container.scrollHeight;
            });
        },

        listenForMessages() {
            Echo.private('chat')
                .listen('NewChatMessage', (e) => {
                    this.messages.push(e.message);
                    if (!this.isChatOpen) {
                        this.unreadCount++;
                    }
                    this.scrollToBottom();
                });
        }
    }));
}); 