import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: '8080',
    forceTLS: false,
    disableStats: true,
    enableTransports: ['ws'],
});
// console.log(import.meta.env.VITE_REVERB_HOST)
//console.log(import.meta.env.VITE_REVERB_PORT)
