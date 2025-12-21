<!-- resources/js/views/users/UserIndexView.vue -->
<script setup lang="ts">
import { ref, onMounted, h } from 'vue';
import axios from 'axios';
import type { User } from '@/types/user';
import UserFormDialog from './components/UserFormDialog.vue'; // Импортируем новый универсальный компонент
import DataTable from '@/components/ui/data-table/DataTable.vue';
import { columns as originalColumns } from './components/columns'; // Конфигурация колонок
import UserActions from './components/UserActions.vue'; // Импортируем UserActions
import { Button } from '@/components/ui/button';

const users = ref<User[]>([]);
const isLoading = ref(true);
const isFormDialogOpen = ref(false);
const editingUser = ref<User | null>(null);

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

// Открытие формы для создания пользователя
const openCreateDialog = () => {
    editingUser.value = null; // Сбрасываем редактируемого пользователя для режима создания
    isFormDialogOpen.value = true;
};

// Открытие формы для редактирования пользователя
const openEditDialog = (user: User) => {
    editingUser.value = user; // Устанавливаем редактируемого пользователя
    isFormDialogOpen.value = true;
};

// Обновляем колонки, чтобы передать обработчик редактирования
const columns = originalColumns.map(column => {
    if (column.id === 'actions') {
        return {
            ...column,
            cell: ({ row }) => {
                const user = row.original;
                return h(UserActions, {
                    user: user,
                    onEdit: openEditDialog,
                    onUserDeleted: fetchUsers,
                });
            },
        };
    }
    return column;
});

onMounted(fetchUsers);
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <Button @click="openCreateDialog">Добавить пользователя</Button>
        </div>

        <div v-if="isLoading" class="flex justify-center py-10">
            <span class="text-muted-foreground">Загрузка данных...</span>
        </div>

        <DataTable
            v-else
            :columns="columns"
            :data="users"
            search-placeholder="Найти пользователя..."
            search-column="name"
        />

        <!-- Универсальный диалог для создания/редактирования -->
        <UserFormDialog
            v-model:open="isFormDialogOpen"
            :user="editingUser"
            @user-saved="fetchUsers"
        />
    </div>
</template>
