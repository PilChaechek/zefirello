<!-- resources/js/Views/Users/UserIndex.vue -->
<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Состояние
const users = ref([]);
const loading = ref(true);

// Загрузка данных при открытии страницы
onMounted(async () => {
    try {
        // Прямой запрос к API
        const response = await axios.get('users');
        users.value = response.data.data;

    } catch (e) {
        console.error('Ошибка загрузки:', e);
        alert('Не удалось загрузить пользователей. Проверь консоль.');
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div class="p-6">

        <!-- Индикатор загрузки -->
        <div v-if="loading" class="text-gray-500">Загрузка...</div>

        <!-- Таблица -->
        <div v-else class="overflow-x-auto border rounded-lg">
            <table class="w-full text-sm text-left text-foreground">
                <thead class="bg-gray-100 text-gray-700 uppercase">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Имя</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Дата регистрации</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="user in users" :key="user.id" class="border-b">
                    <td class="px-4 py-3">{{ user.id }}</td>
                    <td class="px-4 py-3 font-medium">{{ user.name }}</td>
                    <td class="px-4 py-3">{{ user.email }}</td>
                    <td class="px-4 py-3">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
