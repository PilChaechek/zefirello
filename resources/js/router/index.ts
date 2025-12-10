import { createRouter, createWebHistory } from 'vue-router';
import Login from '@/Pages/Auth/Login.vue'; // Скоро создадим
import { useAuthStore } from '@/stores/auth';

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { guest: true } // Пометка: только для гостей
    },
    {
        path: '/',
        name: 'home',
        component: () => import('@/Pages/Home.vue'), // Ленивая загрузка
        meta: { requiresAuth: true } // Пометка: только для своих
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Глобальная защита роутов (Guard)
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // Если мы еще не знаем, залогинен ли юзер — спросим у API
    // (Это нужно при обновлении страницы F5)
    if (authStore.user === null) {
        await authStore.getUser();
    }

    // Логика перенаправления
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next({ name: 'login' }); // Иди логинься
    } else if (to.meta.guest && authStore.isAuthenticated) {
        next({ name: 'home' }); // Ты уже вошел, вали домой
    } else {
        next(); // Проходи
    }
});

export default router;
