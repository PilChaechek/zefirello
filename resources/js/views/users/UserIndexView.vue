<!-- resources/js/Views/Users/UserIndex.vue -->
<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import type { User } from '@/types/user';

// Указываем тип для ref
const users = ref<User[]>([]);
const loading = ref(true);

onMounted(async () => {
    try {
        const response = await axios.get('users');
        users.value = response.data.data;
    } catch (e) {
        console.error('Ошибка:', e);
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div class="p-6">
        <div v-if="loading" class="text-gray-500">Загрузка...</div>

        <div v-else class="overflow-x-auto border rounded-lg">
            <table class="w-full text-sm text-left text-foreground">
                <thead class="bg-gray-100 text-gray-700 uppercase">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Имя</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Роль</th> <!-- Новая колонка -->
                    <th class="px-4 py-3">Дата регистрации</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="user in users" :key="user.id" class="border-b">
                    <td class="px-4 py-3">{{ user.id }}</td>
                    <td class="px-4 py-3 font-medium">{{ user.name }}</td>
                    <td class="px-4 py-3">{{ user.email }}</td>

                    <!-- Вывод ролей -->
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1">
                            <span
                                v-for="role in user.roles"
                                :key="role.name"
                                class="px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full"
                            >
                                {{ role.label }}
                            </span>
                            <span v-if="user.roles.length === 0" class="text-gray-400 text-xs">
                                Нет ролей
                            </span>
                        </div>
                    </td>

                    <td class="px-4 py-3">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
