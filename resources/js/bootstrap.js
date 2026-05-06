import 'bootstrap';
import './echo.js';

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Send Echo socket ID with every request so Laravel's toOthers() works
window.Echo.connector.pusher.connection.bind('connected', () => {
    window.axios.defaults.headers.common['X-Socket-Id'] = window.Echo.socketId();
});

