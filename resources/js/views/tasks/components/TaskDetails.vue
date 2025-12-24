<script setup lang="ts">
import { computed, ref } from 'vue';
import type { Task } from '@/types/task';
import IconLabel from '@/components/ui/icon-label/IconLabel.vue';
import VueEasyLightbox from 'vue-easy-lightbox/external-css';
import '@/../css/vendor/vue-easy-lightbox.css';

const props = defineProps<{
    task: Task | null;
    hideTitle?: boolean; // New prop
}>();

const visible = ref(false);
const index = ref(0);
const imgs = ref<string[]>([]);

const imageAttachments = computed(() => {
    return props.task?.attachments?.filter(a => a.mime_type.startsWith('image/')) || [];
});

const showLightbox = (clickedIndex: number) => {
    imgs.value = imageAttachments.value.map(a => a.url);
    index.value = clickedIndex;
    visible.value = true;
};

const handleHide = () => {
    visible.value = false;
};

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
        <div v-if="!hideTitle" class="space-y-2">
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
                <p class="text-sm font-medium text-muted-foreground">ID задачи</p>
                <p class="text-sm font-semibold">#{{ task.id }}</p>
            </div>

        </div>

        <div class="space-y-2">
            <h3 class="text-lg font-bold">Описание</h3>
            <p class="text-sm text-muted-foreground">{{ task.description || 'Нет описания' }}</p>
        </div>

        <!-- Attachments Section -->
        <div v-if="task.attachments && task.attachments.length > 0" class="space-y-2">
            <h3 class="text-lg font-bold">Вложения</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                <template v-for="(attachment, idx) in task.attachments" :key="attachment.id">
                    <a
                        v-if="attachment.mime_type.startsWith('image/')"
                        href="#"
                        @click.prevent="showLightbox(imageAttachments.findIndex(img => img.id === attachment.id))"
                        class="group relative block w-full aspect-square bg-muted rounded-lg overflow-hidden border border-border hover:border-primary transition-colors"
                    >
                        <img
                            :src="attachment.url"
                            :alt="attachment.original_name"
                            class="w-full h-full object-cover"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <p class="absolute bottom-2 left-2 right-2 text-xs text-white truncate group-hover:underline">
                            {{ attachment.original_name }}
                        </p>
                    </a>
                    <a
                        v-else
                        :href="attachment.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group relative block w-full aspect-square bg-muted rounded-lg overflow-hidden border border-border hover:border-primary transition-colors"
                    >
                        <!-- You can add a generic icon for non-image files here -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <p class="absolute bottom-2 left-2 right-2 text-xs text-white truncate group-hover:underline">
                            {{ attachment.original_name }}
                        </p>
                    </a>
                </template>
            </div>
        </div>
        <VueEasyLightbox
            :visible="visible"
            :imgs="imgs"
            :index="index"
            @hide="handleHide"
            :moveDisabled="true"
        />
    </div>
    <div v-else>
        <p class="text-muted-foreground">Выберите задачу для просмотра деталей.</p>
    </div>
</template>
