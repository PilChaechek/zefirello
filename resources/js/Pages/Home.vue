<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
    await auth.logout();
    router.push({ name: 'login' });
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex flex-col items-center pt-20">
        <div class="bg-white p-8 rounded-lg shadow-md w-96 text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">🎉 Успех!</h1>

            <div v-if="auth.user" class="mb-6 text-left bg-blue-50 p-4 rounded border border-blue-100">
                <p class="text-sm text-gray-500">Вы вошли как:</p>
                <p class="font-bold text-lg">{{ auth.user.name }}</p>
                <p class="text-gray-600">{{ auth.user.email }}</p>
            </div>

            <button
                @click="handleLogout"
                class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition duration-200"
            >
                Выйти из системы
            </button>
        </div>
    </div>
</template>
