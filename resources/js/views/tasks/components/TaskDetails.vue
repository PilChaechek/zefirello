<script setup lang="ts">
import { computed, ref } from 'vue';
import type { Task } from '@/types/task';
import IconLabel from '@/components/ui/icon-label/IconLabel.vue';
import VueEasyLightbox from 'vue-easy-lightbox/external-css';
import '@/../css/vendor/vue-easy-lightbox.css';
import TaskAttachmentForm from './TaskAttachmentForm.vue';
import type { Attachment } from '@/types/attachment';
import { useAuthStore } from '@/stores/auth';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { Button } from '@/components/ui/button';
import { Trash2 } from 'lucide-vue-next';
import axios from 'axios';
import { toast } from 'vue-sonner';

const props = defineProps<{
    task: Task | null;
    hideTitle?: boolean; // New prop
}>();

const authStore = useAuthStore();
const user = computed(() => authStore.user);

const visible = ref(false);
const index = ref(0);
const imgs = ref<string[]>([]);
const isDeleteDialogOpen = ref(false);
const attachmentToDelete = ref<Attachment | null>(null);

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

const handleAttachmentsAdded = (newAttachments: Attachment[]) => {
    if (props.task) {
        if (!props.task.attachments) {
            props.task.attachments = [];
        }
        props.task.attachments.push(...newAttachments);
    }
};

const confirmAttachmentDelete = (attachment: Attachment) => {
    attachmentToDelete.value = attachment;
    isDeleteDialogOpen.value = true;
};

const deleteAttachment = async () => {
    if (!attachmentToDelete.value) return;

    try {
        await axios.delete(`/attachments/${attachmentToDelete.value.id}`);

        if (props.task && props.task.attachments) {
            const index = props.task.attachments.findIndex(a => a.id === attachmentToDelete.value!.id);
            if (index > -1) {
                props.task.attachments.splice(index, 1);
            }
        }

        toast.success('Вложение успешно удалено');
    } catch (error) {
        console.error('Ошибка удаления вложения:', error);
        toast.error('Не удалось удалить вложение');
    } finally {
        isDeleteDialogOpen.value = false;
        attachmentToDelete.value = null;
    }
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
        <div class="space-y-4">
            <TaskAttachmentForm v-if="authStore.hasRole('admin') || authStore.hasRole('manager')" :task="task" @attachments-added="handleAttachmentsAdded" />

            <div v-if="task.attachments && task.attachments.length > 0" class="space-y-2">
                <h3 class="text-lg font-bold">Вложения</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <div v-for="attachment in task.attachments" :key="attachment.id" class="group relative">
                        <a
                            v-if="attachment.mime_type.startsWith('image/')"
                            href="#"
                            @click.prevent="showLightbox(imageAttachments.findIndex(img => img.id === attachment.id))"
                            class="block w-full aspect-square bg-muted rounded-lg overflow-hidden border border-border hover:border-primary transition-colors"
                        >
                            <img :src="attachment.url" :alt="attachment.original_name" class="w-full h-full object-cover" />
                        </a>
                        <a
                            v-else
                            :href="attachment.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="block w-full aspect-square bg-muted rounded-lg overflow-hidden border border-border hover:border-primary transition-colors flex items-center justify-center"
                        >
                            <!-- You can add a generic icon for non-image files here -->
                            <p class="text-xs p-2 text-center">{{ attachment.original_name }}</p>
                        </a>

                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent pointer-events-none"></div>
                        <p class="absolute bottom-2 left-2 right-2 text-xs text-white truncate pointer-events-none">
                            {{ attachment.original_name }}
                        </p>

                        <Button
                            v-if="user && user.id === attachment.user_id"
                            variant="destructive"
                            size="icon"
                            class="absolute top-2 right-2 h-7 w-7 opacity-0 group-hover:opacity-100 transition-opacity"
                            @click="confirmAttachmentDelete(attachment)"
                        >
                            <Trash2 class="h-4 w-4" />
                        </Button>
                    </div>
                </div>
            </div>
        </div>
        <VueEasyLightbox
            :visible="visible"
            :imgs="imgs"
            :index="index"
            @hide="handleHide"
            :moveDisabled="true"
        />
        <AlertDialog :open="isDeleteDialogOpen" @update:open="isDeleteDialogOpen = $event">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Вы абсолютно уверены?</AlertDialogTitle>
                    <AlertDialogDescription>
                        Это действие необратимо. Вложение "{{ attachmentToDelete?.original_name }}" будет удалено навсегда.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Отмена</AlertDialogCancel>
                    <AlertDialogAction @click="deleteAttachment" class="bg-red-600 hover:bg-red-700 text-white">Удалить</AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
    <div v-else>
        <p class="text-muted-foreground">Выберите задачу для просмотра деталей.</p>
    </div>
</template>

