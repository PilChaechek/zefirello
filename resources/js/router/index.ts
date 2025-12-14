// src/router/index.ts
import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

// 1. NEW: Расширяем типы Vue Router
// говорим TS, что в meta может быть title.
declare module 'vue-router' {
    interface RouteMeta {
        title?: string // Опциональный заголовок
        requiresAuth?: boolean
        guest?: boolean
    }
}

const AppLayout = () => import('@/layouts/AppLayout.vue');

const routes: RouteRecordRaw[] = [
    {
        path: '/',
        component: AppLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'home',
                component: () => import('@/views/HomeView.vue'),
                meta: { title: 'Главная' }
            },
            // {
            //     path: 'tasks',
            //     name: 'tasks',
            //     component: () => import('@/views/TasksView.vue'), // Условно
            //     // NEW: Добавили заголовок
            //     meta: { title: 'Мои задачи' }
            // }
        ]
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/auth/LoginView.vue'),
        // NEW: Добавили заголовок
        meta: { guest: true, title: 'Вход в систему' }
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('@/views/NotFoundView.vue'),
        meta: { title: 'Страница не найдена' }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // 1. Проверка сессии (твой код)
    if (!authStore.user) {
        try {
            await authStore.getUser();
        } catch (error) {
            // Игнорируем ошибку сессии
        }
    }

    const isAuthenticated = !!authStore.user;

    // 2. Обновление заголовка вкладки браузера
    const appName = import.meta.env.VITE_APP_NAME;
    document.title = to.meta.title
        ? `${to.meta.title} | ${appName}`
        : appName;

    // 3. Логика редиректов (твой код)
    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: 'login' });
    } else if (to.meta.guest && isAuthenticated) {
        next({ name: 'home' });
    } else {
        next();
    }
});

export default router;
