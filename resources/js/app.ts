import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import './bootstrap';
import '../css/app.css';

import { setZodRuLocale } from '@/lib/zod-ru';
setZodRuLocale();

import { useTaskMetaStore } from '@/stores/taskMeta';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

const taskMetaStore = useTaskMetaStore();
taskMetaStore.fetchTaskMeta();

app.mount('#app');
