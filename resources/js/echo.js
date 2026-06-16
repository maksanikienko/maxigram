import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

const isTLS = import.meta.env.VITE_REVERB_SCHEME === 'https';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_WS_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_WSS_PORT ?? 443,
    forceTLS: isTLS,
    enabledTransports: isTLS ? ['wss'] : ['ws'],
    disableStats: true,
    authEndpoint: '/broadcasting/auth',
});