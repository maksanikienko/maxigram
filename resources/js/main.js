import './bootstrap.js'

import { createApp } from 'vue';
import ChatComponent from './Components/ChatComponent.vue';

const app = createApp({});
app.component('ChatComponent', ChatComponent);
app.mount('#app');



