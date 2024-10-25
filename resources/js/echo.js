import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: false,
    disableStats: true,
    enableTransports: ['ws','wss'],
});

// window.Echo.channel('chat')
//     .listen('.message.sent', (e) => {
//         console.log('Received message from echo.js:', e);
//     })
//     .error((error) => {
//         console.error('Echo error:', error);
//     });
