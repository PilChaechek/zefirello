<!-- resources/js/views/users/components/UserTable.vue -->
<script setup lang="ts">
import axios from 'axios';
import type { User } from '@/types/user';
import { Button } from '@/components/ui/button';

const props = defineProps<{
  users: User[];
}>();

const emit = defineEmits(['user-deleted']);

const deleteUser = async (id: number) => {
  if (confirm('Вы уверены, что хотите удалить этого пользователя?')) {
    try {
      await axios.delete(`users/${id}`);
      emit('user-deleted');
    } catch (e) {
      console.error('Ошибка при удалении пользователя:', e);
      alert('Не удалось удалить пользователя.');
    }
  }
};
</script>

<template>
  <div class="overflow-x-auto border rounded-lg">
    <table class="w-full text-sm text-left text-foreground">
      <thead class="bg-gray-100 text-gray-700 uppercase">
      <tr>
        <th class="px-4 py-3">ID</th>
        <th class="px-4 py-3">Имя</th>
        <th class="px-4 py-3">Email</th>
        <th class="px-4 py-3">Роль</th>
        <th class="px-4 py-3">Дата регистрации</th>
        <th class="px-4 py-3">Действия</th> <!-- Новая колонка -->
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
        <td class="px-4 py-3">
          <Button variant="destructive" size="sm" @click="deleteUser(user.id)">
            Удалить
          </Button>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>
