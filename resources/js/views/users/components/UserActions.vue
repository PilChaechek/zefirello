<!-- resources/js/views/users/components/UserActions.vue -->
<script setup lang="ts">
import { ref, inject } from 'vue';
import { MoreHorizontal, Pencil, Trash } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import type { User } from '@/types/user';
import axios from 'axios';
import { toast } from 'vue-sonner';

const props = defineProps<{
    user: User;
}>();

const refreshUsers = inject<() => void>('refreshUsers');

const isDeleteDialogOpen = ref(false);
const isDeleting = ref(false);

const handleDelete = async () => {
    isDeleting.value = true;
    try {
        await axios.delete(`/users/${props.user.id}`);
        toast.success('Пользователь удален');

        if (refreshUsers) {
            refreshUsers();
        }
    } catch (e) {
        console.error(e);
        toast.error('Ошибка удаления');
    } finally {
        isDeleting.value = false;
        isDeleteDialogOpen.value = false;
    }
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="h-8 w-8 p-0">
                <span class="sr-only">Открыть меню</span>
                <MoreHorizontal class="h-4 w-4" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuLabel>Действия</DropdownMenuLabel>
            <DropdownMenuItem @click="$emit('edit', user)">
                <Pencil class="mr-2 h-4 w-4" />
                Редактировать
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem @click="isDeleteDialogOpen = true" class="text-red-600 focus:text-red-600">
                <Trash class="mr-2 h-4 w-4" />
                Удалить
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>

    <AlertDialog :open="isDeleteDialogOpen" @update:open="isDeleteDialogOpen = $event">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Вы абсолютно уверены?</AlertDialogTitle>
                <AlertDialogDescription>
                    Это действие нельзя отменить. Пользователь <b>{{ user.name }}</b> будет удален.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Отмена</AlertDialogCancel>
                <AlertDialogAction
                    @click.prevent="handleDelete"
                    class="bg-red-600 hover:bg-red-700 text-white"
                    :disabled="isDeleting"
                >
                    {{ isDeleting ? 'Удаление...' : 'Удалить' }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
