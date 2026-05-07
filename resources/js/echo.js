import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_WS_PORT,
    wssPort: import.meta.env.VITE_REVERB_WSS_PORT,

    forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https' ,
    enabledTransports: ['ws','wss'],

    disableStats: true,
    authEndpoint: '/broadcasting/auth',

//    path: '/reverb',
});
console.log(import.meta.env.VITE_REVERB_HOST)
console.log(import.meta.env.VITE_REVERB_WS_PORT)