<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import { toast } from 'vue-sonner';
import type { Project } from '@/types/project';

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
    project: Project;
}>();

const emit = defineEmits(['edit', 'project-deleted']);

const isDeleting = ref(false);

const deleteProject = async () => {
    isDeleting.value = true;
    try {
        await axios.delete(`/projects/${props.project.id}`);
        toast.success(`Проект "${props.project.name}" успешно удален.`);
        emit('project-deleted');
    } catch (error: any) {
        toast.error(error.response?.data?.message || 'Ошибка при удалении проекта.');
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
                <DropdownMenuItem @click="$emit('edit', project)">
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
                    Это действие нельзя отменить. Проект
                    <span class="font-bold">"{{ project.name }}"</span> будет удален.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Отмена</AlertDialogCancel>
                <AlertDialogAction @click="deleteProject" class="bg-red-600 hover:bg-red-700 text-white" :disabled="isDeleting">
                    {{ isDeleting ? 'Удаление...' : 'Удалить' }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
