import './bootstrap';
import '../css/app.css'; // Импортируем стили здесь

import { createApp } from 'vue';
import App from './App.vue';

const app = createApp(App);

// Здесь мы позже подключим Pinia и Router
app.mount('#app');
