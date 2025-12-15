<!-- resources/js/views/users/components/UserTable.vue -->
<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import type { User } from '@/types/user';
import { Button } from '@/components/ui/button';
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { toast } from 'vue-sonner';

defineProps<{
  users: User[];
}>();

const emit = defineEmits(['user-deleted']);

const isAlertDialogOpen = ref(false);
const selectedUser = ref<User | null>(null);

const openDeleteDialog = (user: User) => {
  selectedUser.value = user;
  isAlertDialogOpen.value = true;
};

const deleteUser = async () => {
  if (!selectedUser.value) return;

  try {
    await axios.delete(`users/${selectedUser.value.id}`);
    emit('user-deleted');
    toast.success(`Пользователь ${selectedUser.value.name} успешно удален.`);
  } catch (e) {
    console.error('Ошибка при удалении пользователя:', e);
    toast.error('Не удалось удалить пользователя.');
  } finally {
    isAlertDialogOpen.value = false;
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
        <th class="px-4 py-3">Действия</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="user in users" :key="user.id" class="border-b">
        <td class="px-4 py-3">{{ user.id }}</td>
        <td class="px-4 py-3 font-medium">{{ user.name }}</td>
        <td class="px-4 py-3">{{ user.email }}</td>

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
                                </td>        <td class="px-4 py-3">{{ new Date(user.created_at).toLocaleDateString() }}</td>
        <td class="px-4 py-3">
          <Button variant="destructive" size="sm" @click="openDeleteDialog(user)">
            Удалить
          </Button>
        </td>
      </tr>
      </tbody>
    </table>
  </div>

  <AlertDialog :open="isAlertDialogOpen" @update:open="isAlertDialogOpen = $event">
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>Вы уверены?</AlertDialogTitle>
        <AlertDialogDescription>
          Это действие нельзя отменить. Пользователь "{{ selectedUser?.name }}" будет навсегда удален из системы.
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel>Отмена</AlertDialogCancel>
        <AlertDialogAction @click="deleteUser">
          Удалить
        </AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>

