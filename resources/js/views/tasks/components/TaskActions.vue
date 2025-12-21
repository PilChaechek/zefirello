<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import { toast } from 'vue-sonner';
import type { Task } from '@/types/task';

// UI Components
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
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { MoreHorizontal, Pencil, Trash } from 'lucide-vue-next';

// Props & Emits
const props = defineProps<{
    task: Task;
    projectSlug: string;
}>();

const emit = defineEmits(['edit', 'task-deleted']);

const isDeleting = ref(false);

const deleteTask = async () => {
    isDeleting.value = true;
    try {
        await axios.delete(`/projects/${props.projectSlug}/tasks/${props.task.id}`);
        toast.success(`Задача "${props.task.title}" успешно удалена.`);
        emit('task-deleted');
    } catch (error: any) {
        toast.error(error.response?.data?.message || 'Ошибка при удалении задачи.');
    } finally {
        isDeleting.value = false;
    }
};
</script>

<template>
    <AlertDialog>
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="ghost" class="h-8 w-8 p-0">
                    <span class="sr-only">Открыть меню</span>
                    <MoreHorizontal class="h-4 w-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuItem @click="$emit('edit', task)">
                    <Pencil class="mr-2 h-4 w-4" />
                    Редактировать
                </DropdownMenuItem>
                <AlertDialogTrigger as-child>
                    <DropdownMenuItem class="text-red-600 focus:text-red-600">
                        <Trash class="mr-2 h-4 w-4" />
                        Удалить
                    </DropdownMenuItem>
                </AlertDialogTrigger>
            </DropdownMenuContent>
        </DropdownMenu>

        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Вы абсолютно уверены?</AlertDialogTitle>
                <AlertDialogDescription>
                    Это действие нельзя отменить. Задача
                    <span class="font-bold">"{{ task.title }}"</span> будет удалена.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Отмена</AlertDialogCancel>
                <AlertDialogAction @click="deleteTask" class="bg-red-600 hover:bg-red-700 text-white" :disabled="isDeleting">
                    {{ isDeleting ? 'Удаление...' : 'Удалить' }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
