<script setup lang="ts">
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import type { Task } from '@/types/task';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetDescription, // Keep for accessibility, but maybe not used visually
} from '@/components/ui/sheet';
import TaskDetails from './TaskDetails.vue';
import { Button } from '@/components/ui/button';
import { ExternalLink } from 'lucide-vue-next';

const props = defineProps<{
    task: Task | null;
    open: boolean;
    projectSlug: string;
}>();

const emit = defineEmits(['update:open']);

const sheetOpen = computed({
    get: () => props.open,
    set: (value) => emit('update:open', value),
});
</script>

<template>
    <Sheet v-model:open="sheetOpen">
        <SheetContent class="w-full sm:w-[540px] sm:max-w-[540px] overflow-y-auto px-6">
            <SheetHeader class="pr-6 pl-0 pb-2">
                <div>
                    <RouterLink
                        v-if="task"
                        :to="{ name: 'task-detail', params: { slug: projectSlug, taskId: task.id } }"
                    >
                        <Button variant="ghost" size="icon">
                            <ExternalLink class="w-4 h-4" />
                        </Button>
                    </RouterLink>
                </div>
                <SheetTitle v-if="task">{{ task.title }}</SheetTitle>
                <!-- Accessible description for screen readers -->
                <SheetDescription v-if="task?.description" class="sr-only">
                    {{ task.description }}
                </SheetDescription>
            </SheetHeader>
            <TaskDetails :task="task" hide-title />
        </SheetContent>
    </Sheet>
</template>
