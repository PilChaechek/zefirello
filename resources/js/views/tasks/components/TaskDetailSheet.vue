<script setup lang="ts">
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import type { Task } from '@/types/task';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
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
        <SheetContent class="w-[500px] sm:max-w-none md:w-1/2 lg:w-1/3 overflow-y-auto px-6">
            <SheetHeader class="flex-row items-center justify-between pr-6 pl-0 pb-0">
                <RouterLink
                    v-if="task"
                    :to="{ name: 'task-detail', params: { slug: projectSlug, taskId: task.id } }"
                >
                    <Button variant="ghost" size="icon">
                        <ExternalLink class="w-4 h-4" />
                    </Button>
                </RouterLink>
            </SheetHeader>
            <div>
                <TaskDetails :task="task" />
            </div>
        </SheetContent>
    </Sheet>
</template>
