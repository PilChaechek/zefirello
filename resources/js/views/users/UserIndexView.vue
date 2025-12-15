<!-- resources/js/Views/Users/UserIndex.vue -->
<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import type { User } from '@/types/user';
import UserCreateDialog from '@/views/users/components/UserCreateDialog.vue';
import UserTable from '@/views/users/components/UserTable.vue';

// Указываем тип для ref
const users = ref<User[]>([]);
const loading = ref(true);

const fetchUsers = async () => {
    // Не ставим loading = true, чтобы таблица не мигала при обновлении списка
    // или ставим, если хотим показать спиннер. Для UX лучше оставить старые данные пока грузятся новые.
    try {
        const response = await axios.get('users');
        users.value = response.data.data;
    } catch (e) {
        console.error('Ошибка:', e);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchUsers();
});
</script>

<template>
    <div class="p-6">
        <div v-if="loading" class="text-gray-500">Загрузка...</div>
        <UserTable v-else :users="users" @user-deleted="fetchUsers" />
        <UserCreateDialog @user-created="fetchUsers" />
    </div>
</template>
