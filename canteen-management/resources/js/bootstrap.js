import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Laravel Echo Configuration
 */
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

/**
 * Register global Echo listeners for real-time notifications
 */
document.addEventListener('DOMContentLoaded', function() {
    // Listen for new orders (for admins and tenants)
    window.Echo.channel('orders')
        .listen('.order.new', (e) => {
            console.log('New order placed:', e);
            
            // Show notification
            if (typeof showNotification === 'function') {
                showNotification('New order received!', 'info');
            }
            
            // Play notification sound
            playNotificationSound();
        })
        .listen('.order.status.updated', (e) => {
            console.log('Order status updated:', e);
            
            // Dispatch Livewire events
            if (window.Livewire) {
                window.Livewire.dispatch('orderStatusUpdated', e);
            }
        });

    // Listen for user-specific notifications
    if (window.Laravel && window.Laravel.user) {
        window.Echo.private(`user.${window.Laravel.user.id}`)
            .listen('.order.status.updated', (e) => {
                console.log('Your order status updated:', e);
                
                // Show personalized notification
                if (typeof showNotification === 'function') {
                    showNotification(`Your order ${e.order_number} is now ${e.status}!`, 'success');
                }
            });
    }
});

/**
 * Utility function to play notification sound
 */
function playNotificationSound() {
    try {
        const audio = new Audio('/sounds/notification.mp3');
        audio.volume = 0.3;
        audio.play().catch(e => console.log('Could not play notification sound:', e));
    } catch (e) {
        console.log('Notification sound not available');
    }
}

/**
 * Utility function to show notifications
 */
window.showNotification = function(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
    
    // Set colors based on type
    const colors = {
        'success': 'bg-green-500 text-white',
        'error': 'bg-red-500 text-white',
        'warning': 'bg-yellow-500 text-black',
        'info': 'bg-blue-500 text-white'
    };
    
    notification.className += ` ${colors[type] || colors.info}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <span class="mr-2">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, 5000);
};
