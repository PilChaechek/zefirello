<script setup lang="ts">
import { computed } from 'vue';
import type { Task } from '@/types/task';
import IconLabel from '@/components/ui/icon-label/IconLabel.vue';

const props = defineProps<{
    task: Task | null;
}>();

const formattedDueDate = computed(() => {
    if (!props.task?.due_date) return 'Не указан';
    const date = new Date(props.task.due_date);
    return date.toLocaleDateString('ru-RU', { year: 'numeric', month: 'long', day: 'numeric' });
});

const formattedTimeSpent = computed(() => {
    if (!props.task?.time_spent) return '0 мин';
    const minutes = props.task.time_spent;
    if (minutes < 60) return `${minutes} мин`;
    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;
    return `${hours} ч ${remainingMinutes} мин`;
});
</script>

<template>
    <div v-if="task" class="space-y-4">
        <div class="space-y-2">
            <h2 class="text-xl">{{ task.title }}</h2>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
                <p class="text-sm font-medium text-muted-foreground">Статус</p>
                <IconLabel
                    v-if="task.status.icon"
                    :icon="task.status.icon"
                    :label="task.status.label"
                    :color="task.status.color"
                />
                <p v-else class="text-sm font-semibold">{{ task.status.label }}</p>
            </div>
            <div class="space-y-1">
                <p class="text-sm font-medium text-muted-foreground">Приоритет</p>
                <IconLabel
                    v-if="task.priority.icon"
                    :icon="task.priority.icon"
                    :label="task.priority.label"
                />
                <p v-else class="text-sm font-semibold">{{ task.priority.label }}</p>
            </div>
            <div class="space-y-1">
                <p class="text-sm font-medium text-muted-foreground">Затрачено</p>
                <p class="text-sm font-semibold">{{ formattedTimeSpent }}</p>
            </div>
            <div class="space-y-1">
                <p class="text-sm font-medium text-muted-foreground">Срок</p>
                <p class="text-sm font-semibold">{{ formattedDueDate }}</p>
            </div>
        </div>

        <div class="space-y-2">
            <h3 class="text-lg font-bold">Описание</h3>
            <p class="text-sm text-muted-foreground">{{ task.description || 'Нет описания' }}</p>
        </div>
    </div>
    <div v-else>
        <p class="text-muted-foreground">Выберите задачу для просмотра деталей.</p>
    </div>
</template>
