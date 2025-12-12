<script setup lang="ts">
import { useRouter } from 'vue-router';

const router = useRouter();
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 text-gray-800">
        <h1 class="text-9xl font-bold text-blue-600">404</h1>
        <h2 class="text-2xl font-semibold mt-4">Страница не найдена</h2>
        <p class="text-gray-500 mt-2 mb-8">Возможно, вы ошиблись в адресе или страница была удалена.</p>

        <button
            @click="router.push({ name: 'home' })"
            class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
        >
            Вернуться на главную
        </button>
    </div>
</template>
