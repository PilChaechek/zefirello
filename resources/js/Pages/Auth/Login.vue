<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import { z } from 'zod';

const auth = useAuthStore();
const router = useRouter();

// 1. Схема валидации (Правила)
const loginSchema = z.object({
    email: z.string().email('Введите корректный Email'),
    password: z.string().min(6, 'Пароль должен быть не менее 6 символов'),
});

// 2. Состояние формы
const form = reactive({
    email: 'admin@zefirello.ru', // Для удобства тестов
    password: 'password',
});

const errors = ref<Record<string, string>>({}); // Ошибки фронта (Zod)
const isSubmitting = ref(false);

// 3. Отправка формы
const handleSubmit = async () => {
    // Сброс ошибок
    errors.value = {};
    auth.errors = {}; // Сброс ошибок бекенда

    // Валидация Zod
    const result = loginSchema.safeParse(form);
    if (!result.success) {
        // Превращаем массив ошибок Zod в удобный объект
        result.error.issues.forEach((issue) => {
            errors.value[issue.path[0]] = issue.message;
        });
        return;
    }

    isSubmitting.value = true;
    try {
        await auth.login(form);
        router.push({ name: 'home' }); // Редирект после успеха
    } catch (e) {
        // Ошибки бекенда (422) уже в auth.errors
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <div class="flex items-center justify-center min-h-screen">
        <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-6 text-center">Вход в систему</h1>

            <form @submit.prevent="handleSubmit" class="space-y-4">
                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2"
                        :class="{'border-red-500': errors.email || auth.errors.email}"
                    >
                    <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</p>
                    <p v-if="auth.errors.email" class="text-red-500 text-xs mt-1">{{ auth.errors.email[0] }}</p>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Пароль</label>
                    <input
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2"
                        :class="{'border-red-500': errors.password}"
                    >
                    <p v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password }}</p>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                >
                    {{ isSubmitting ? 'Вход...' : 'Войти' }}
                </button>
            </form>
        </div>
    </div>
</template>
