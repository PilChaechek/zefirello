import './bootstrap';
import '../css/app.css'; // Импортируем стили здесь

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from '@/router';
import App from '@/App.vue';

const app = createApp(App);
const pinia = createPinia();

// Здесь мы позже подключим Pinia и Router
app.use(pinia);
app.use(router);
app.mount('#app');
