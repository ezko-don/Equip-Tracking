import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Import your systems after Alpine initialization
import './notification-system';
import './chat-system';

// Import Font Awesome
import '@fortawesome/fontawesome-free/js/fontawesome';
import '@fortawesome/fontawesome-free/js/solid';
import '@fortawesome/fontawesome-free/js/regular';
import '@fortawesome/fontawesome-free/js/brands';
import '@fortawesome/fontawesome-free/css/all.min.css';

// Fix for hamburger menu
document.addEventListener('DOMContentLoaded', function() {
    // Ensure mobile menu works correctly
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', function() {
            // Force Alpine to update the mobile menu visibility
            const navElement = this.closest('[x-data]');
            if (navElement && navElement.__x) {
                navElement.__x.updateElements();
            }
        });
    }
});

Alpine.start();

// Add this to your existing Alpine.js initialization
document.addEventListener('alpine:init', () => {
    Alpine.store('darkMode', {
        on: false,
        toggle() {
            this.on = !this.on;
            localStorage.setItem('darkMode', this.on);
        },
        init() {
            this.on = localStorage.getItem('darkMode') === 'true';
        }
    })
})
