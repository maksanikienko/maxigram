import './bootstrap.js';
import { createApp } from 'vue';
import LandingPage from './Components/LandingPage.vue';

const app = createApp({});
app.component('LandingPage', LandingPage);
app.mount('#landing-app');
