import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

// Описываем интерфейс пользователя (чтобы IDE подсказывала поля)
interface User {
    id: number;
    name: string;
    email: string;
    roles?: { name: string }[];
}

export const useAuthStore = defineStore('auth', () => {
    // State
    const user = ref<User | null>(null);
    const errors = ref<Record<string, string[]>>({}); // Ошибки валидации

    // Getters
    const isAuthenticated = computed(() => !!user.value);

    // Actions
    const getUser = async () => {
        try {
            const response = await axios.get('/user');
            user.value = response.data;
        } catch (error) {
            user.value = null;
        }
    };

    const login = async (credentials: any) => {
        // Берем чистый домен из ENV (http://localhost:8001)
        const backendUrl = import.meta.env.VITE_BACKEND_URL;

        // 1. CSRF Handshake (запрос в корень, мимо /api/v1)
        await axios.get(`${backendUrl}/sanctum/csrf-cookie`);

        // 2. Login Request (тут сработает baseURL = /api/v1)
        try {
            await axios.post('/login', credentials);
            await getUser();
            errors.value = {};
        } catch (error: any) {
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
            }
            throw error;
        }
    };

    const logout = async () => {
        await axios.post('/logout');
        user.value = null;
    };

    return {
        user,
        errors,
        isAuthenticated,
        login,
        logout,
        getUser
    };
});
