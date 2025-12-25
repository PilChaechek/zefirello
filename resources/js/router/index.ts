// src/router/index.ts
import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';

// 1. NEW: Расширяем типы Vue Router
// говорим TS, что в meta может быть title.
declare module 'vue-router' {
    interface RouteMeta {
        title?: string // Опциональный заголовок
        requiresAuth?: boolean
        guest?: boolean
        requiredRole?: string // NEW: Опциональная требуемая роль (например, 'admin')
    }
}

const AppLayout = () => import('@/layouts/AppLayout.vue');
const ProjectLayout = () => import('@/layouts/ProjectLayout.vue');

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
            // Users
            {
                path: 'users',
                name: 'users',
                component: () => import('@/views/users/UserIndexView.vue'),
                meta: {
                    title: 'Пользователи',
                    requiredRole: 'admin'
                }
            },
            // Projects
            {
                path: 'projects',
                component: ProjectLayout,
                children: [
                    {
                        path: '',
                        name: 'projects',
                        component: () => import('@/views/projects/ProjectIndexView.vue'),
                        meta: {
                            title: 'Проекты'
                        }
                    },
                    {
                        path: ':slug',
                        name: 'project-detail',
                        component: () => import('@/views/projects/ProjectDetailView.vue'),
                        meta: { title: 'Детали проекта' }
                    },
                    {
                        path: ':slug/tasks/:taskId',
                        name: 'task-detail',
                        component: () => import('@/views/tasks/TaskDetailView.vue'),
                        meta: { title: 'Детали задачи' }
                    }
                ]
            }
        ]
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/auth/LoginView.vue'),
        meta: {
            guest: true,
            title: 'Вход в систему'
        }
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
    const appName = import.meta.env.VITE_APP_NAME;

    // Fetch user session if not present
    if (!authStore.user) {
        try {
            await authStore.getUser();
        } catch (error) {
            // Ignore session errors
        }
    }

    const isAuthenticated = !!authStore.user;

    // Update document title from meta
    document.title = to.meta.title
        ? `${to.meta.title} | ${appName}`
        : appName;

    // Redirect logic
    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: 'login' });
    } else if (to.meta.guest && isAuthenticated) {
        next({ name: 'home' });
    } else if (to.meta.requiredRole && !authStore.hasRole(to.meta.requiredRole)) {
        // Если маршрут требует определенную роль, а у пользователя ее нет
        next({ name: 'not-found' });
    } else {
        next();
    }
});

export default router;
