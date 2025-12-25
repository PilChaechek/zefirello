<script setup lang="ts">
import { ref, computed } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Loader2, Paperclip } from 'lucide-vue-next';
import axios from 'axios';
import type { Task } from '@/types/task';
import type { Attachment } from '@/types/attachment';

const props = defineProps<{
    task: Task;
}>();

const emit = defineEmits<{
    (e: 'attachments-added', attachments: Attachment[]): void;
}>();

const fileInput = ref<HTMLInputElement | null>(null);
const selectedFiles = ref<File[]>([]);
const isUploading = ref(false);

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files) {
        selectedFiles.value = Array.from(target.files);
    }
};

const triggerFileInput = () => {
    fileInput.value?.click();
};

const handleUpload = async () => {
    if (selectedFiles.value.length === 0) {
        toast.error('Файлы не выбраны', {
            description: 'Пожалуйста, выберите файлы для загрузки.',
        });
        return;
    }

    isUploading.value = true;

    const uploadPromises = selectedFiles.value.map(file => {
        const formData = new FormData();
        formData.append('file', file);
        return axios.post<{ data: Attachment }>(
            `/tasks/${props.task.id}/attachments`,
            formData,
            { headers: { 'Content-Type': 'multipart/form-data' } }
        );
    });

    try {
        const responses = await Promise.all(uploadPromises);
        const newAttachments = responses.map(response => response.data.data);
        emit('attachments-added', newAttachments);

        toast.success(`Успешно загружено ${newAttachments.length} файлов`);

        selectedFiles.value = [];
        if (fileInput.value) {
            fileInput.value.value = '';
        }
    } catch (error) {
        console.error('Ошибка загрузки файлов:', error);
        toast.error('Ошибка загрузки файлов', {
            description: 'Не удалось загрузить один или несколько файлов. Попробуйте снова.',
        });
    } finally {
        isUploading.value = false;
    }
};

const selectedFilesLabel = computed(() => {
    if (selectedFiles.value.length === 0) {
        return 'Файлы не выбраны';
    }
    if (selectedFiles.value.length === 1) {
        return selectedFiles.value[0].name;
    }
    return `Выбрано файлов: ${selectedFiles.value.length}`;
});
</script>

<template>
    <div class="space-y-2">
        <h3 class="text-lg font-bold">Загрузить вложения</h3>
        <div class="flex items-center gap-2 w-1/2">
            <input
                ref="fileInput"
                type="file"
                multiple
                @change="handleFileChange"
                class="hidden"
                :disabled="isUploading"
            />
            <Button @click="triggerFileInput" variant="outline" class="flex-grow justify-start font-normal min-w-0 w-3/4" :disabled="isUploading">
                <Paperclip class="w-4 h-4 mr-2 flex-shrink-0" />
                <span class="truncate">{{ selectedFilesLabel }}</span>
            </Button>
            <Button @click="handleUpload" :disabled="selectedFiles.length === 0 || isUploading">
                <Loader2 v-if="isUploading" class="w-4 h-4 mr-2 animate-spin" />
                <span>Загрузить</span>
            </Button>
        </div>
    </div>
</template>
