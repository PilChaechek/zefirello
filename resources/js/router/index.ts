import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

// Импортируем Layout тоже лениво, или статично (не критично, но лениво лучше для разделения бандла)
const AppLayout = () => import('@/layouts/AppLayout.vue');

const routes: RouteRecordRaw[] = [
    // 1. АВТОРИЗОВАННАЯ ЗОНА (Дашборд, Задачи)
    // Мы оборачиваем их в AppLayout, чтобы был Сайдбар
    {
        path: '/',
        component: AppLayout, // Сначала грузится оболочка
        meta: { requiresAuth: true },
        children: [
            {
                path: '', // Это путь "/"
                name: 'home',
                // ЛЕНИВАЯ ЗАГРУЗКА: Файл HomeView.js скачается только когда юзер войдет
                component: () => import('@/views/HomeView.vue'),
            },
            // Пример будущей страницы
            /*
            {
                path: 'tasks',
                name: 'tasks',
                component: () => import('@/views/tasks/TasksView.vue'),
            }
            */
        ]
    },

    // 2. ПУБЛИЧНАЯ ЗОНА (Логин)
    {
        path: '/login',
        name: 'login',
        // ЛЕНИВАЯ ЗАГРУЗКА
        component: () => import('@/views/auth/LoginView.vue'),
        meta: { guest: true }
    },

    // 3. ОШИБКИ
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('@/views/NotFoundView.vue'),
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// --- GUARD (ЗАЩИТА) ---
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // Проверка сессии при перезагрузке страницы (F5)
    // Делаем это только если юзера нет в сторе, но есть куки/токен (условно)
    if (!authStore.user) {
        try {
            await authStore.getUser();
        } catch (error) {
            // Если сессия протухла — ничего страшного, просто user останется null
        }
    }

    const isAuthenticated = !!authStore.user;

    // Логика редиректов
    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: 'login' });
    } else if (to.meta.guest && isAuthenticated) {
        next({ name: 'home' });
    } else {
        next();
    }
});

export default router;
