import './bootstrap.js'
import 'flowbite'
import.meta.glob([
    '../images/**',
    '../fonts/**',
]);

import { createApp } from 'vue';
import ChatComponent from './components/ChatComponent.vue';

const app = createApp({});
app.component('ChatComponent', ChatComponent); // Register component
app.mount('#app');
