import './bootstrap.js'

import { createApp } from 'vue';
import ChatComponent from './components/ChatComponent.vue';

const app = createApp({});
app.component('ChatComponent', ChatComponent); // Register component
app.mount('#app');
