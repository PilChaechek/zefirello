<!-- resources/js/views/users/UserIndexView.vue -->
<script setup lang="ts">
import { ref, onMounted, provide } from 'vue';
import axios from 'axios';
import type { User } from '@/types/user';
import UserCreateDialog from './components/UserCreateDialog.vue';
import DataTable from '@/components/ui/data-table/DataTable.vue';
import { columns } from './components/columns'; // Конфигурация колонок

const users = ref<User[]>([]);
const isLoading = ref(true);

const fetchUsers = async () => {
    // isLoading.value = true; // Можно раскомментировать, если нужен спиннер при каждом обновлении
    try {
        const { data } = await axios.get('/users');
        users.value = data.data;
    } catch (e) {
        console.error('Ошибка загрузки:', e);
    } finally {
        isLoading.value = false;
    }
};

// МАГИЯ VUE 3:
// Мы "раздаем" эту функцию всем дочерним компонентам,
// даже тем, что внутри DataTable -> Cell -> UserActions
provide('refreshUsers', fetchUsers);

onMounted(fetchUsers);
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <UserCreateDialog @user-created="fetchUsers" />
        </div>

        <div v-if="isLoading" class="flex justify-center py-10">
            <span class="text-muted-foreground">Загрузка данных...</span>
        </div>

        <DataTable
            v-else
            :columns="columns"
            :data="users"
        />
    </div>
</template>
