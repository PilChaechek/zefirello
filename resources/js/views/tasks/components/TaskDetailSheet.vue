<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { RouterLink } from 'vue-router';
import type { Task } from '@/types/task';
import axios from 'axios';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetDescription,
} from '@/components/ui/sheet';
import TaskDetails from './TaskDetails.vue';
import { Button } from '@/components/ui/button';
import { ExternalLink } from 'lucide-vue-next';
import { Skeleton } from '@/components/ui/skeleton';

const props = defineProps<{
    task: Task | null;
    open: boolean;
    projectSlug: string;
}>();

const emit = defineEmits(['update:open']);

const fullTask = ref<Task | null>(null);
const isLoading = ref(false);

const fetchFullTask = async (taskId: number) => {
    isLoading.value = true;
    fullTask.value = null; // Reset previous task data
    try {
        const response = await axios.get<{ data: Task }>(`/projects/${props.projectSlug}/tasks/${taskId}`);
        fullTask.value = response.data.data;
    } catch (error) {
        console.error('Ошибка загрузки полной информации о задаче:', error);
        // Optionally, close the sheet or show an error message
    } finally {
        isLoading.value = false;
    }
};

watch(() => props.open, (isOpen) => {
    if (isOpen && props.task) {
        fetchFullTask(props.task.id);
    }
});

const sheetOpen = computed({
    get: () => props.open,
    set: (value) => emit('update:open', value),
});
</script>

<template>
    <Sheet v-model:open="sheetOpen">
        <SheetContent class="w-full sm:w-[540px] sm:max-w-[540px] overflow-y-auto px-6">
            <SheetHeader class="pr-6 pl-0 pb-2">
                <div class="flex items-center justify-between">
                    <SheetTitle v-if="props.task" class="truncate">{{ props.task.title }}</SheetTitle>
                    <RouterLink
                        v-if="props.task"
                        :to="{ name: 'task-detail', params: { slug: projectSlug, taskId: props.task.id } }"
                    >
                        <Button variant="ghost" size="icon">
                            <ExternalLink class="w-4 h-4" />
                        </Button>
                    </RouterLink>
                </div>
                <SheetDescription v-if="props.task" class="sr-only">
                    {{ fullTask?.description || props.task.description }}
                </SheetDescription>
            </SheetHeader>
            <div class="py-4">
                <div v-if="isLoading" class="space-y-4">
                    <Skeleton class="h-4 w-3/4" />
                    <Skeleton class="h-4 w-1/2" />
                    <div class="grid grid-cols-2 gap-4 pt-4">
                        <Skeleton class="h-8 w-full" />
                        <Skeleton class="h-8 w-full" />
                    </div>
                    <div class="space-y-2 pt-4">
                         <Skeleton class="h-4 w-1/4" />
                         <Skeleton class="h-16 w-full" />
                    </div>
                </div>
                <TaskDetails v-else :task="fullTask" hide-title />
            </div>
        </SheetContent>
    </Sheet>
</template>
