import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Разрешаем отправку кук (Cookies)
window.axios.defaults.withCredentials = true;

// Автоматическая обработка CSRF (для Laravel 11+)
window.axios.defaults.withXSRFToken = true;

const backendUrl = import.meta.env.VITE_BACKEND_URL || 'http://localhost';

// Базовый URL
window.axios.defaults.baseURL = `${backendUrl}/api/v1`;
