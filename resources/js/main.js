import './bootstrap.js'
import 'flowbite'
import.meta.glob([
    '../images/**',
    '../fonts/**',
]);
import {Dropdown} from "flowbite";
import { createApp } from 'vue';
import ChatComponent from './components/ChatComponent.vue';

const app = createApp({});
app.component('ChatComponent', ChatComponent);
app.mount('#app');



