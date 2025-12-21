<script setup lang="ts">
import { computed, watch } from 'vue';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import axios from 'axios';
import { toast } from 'vue-sonner';
import type { Project } from '@/types/project';

// UI Components
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

// Props & Emits
const props = defineProps<{
    project: Project | null;
    open: boolean;
}>();

const emit = defineEmits(['update:open', 'project-saved']);

const isEditMode = computed(() => !!props.project);

const formSchema = toTypedSchema(z.object({
    name: z.string().min(2, 'Название должно быть не короче 2 символов').max(255),
    description: z.string().optional(),
    slug: z.string().optional(),
}));

const { handleSubmit, errors, defineField, resetForm, setValues, isSubmitting } = useForm({
    validationSchema: formSchema,
});

// Заполняем форму данными при открытии в режиме редактирования
watch(() => props.project, (currentProject) => {
    if (currentProject) {
        setValues({
            name: currentProject.name,
            description: currentProject.description,
            slug: currentProject.slug,
        });
    } else {
        resetForm();
    }
});

const [name, nameAttrs] = defineField('name');
const [description, descriptionAttrs] = defineField('description');
const [slug, slugAttrs] = defineField('slug');

const onSubmit = handleSubmit(async (values) => {
    try {
        if (isEditMode.value) {
            // Режим редактирования (пока не используется, но готово на будущее)
            await axios.patch(`/projects/${props.project!.slug}`, values);
            toast.success(`Проект ${values.name} успешно обновлен.`);
        } else {
            // Режим создания
            await axios.post('/projects', values);
            toast.success(`Проект ${values.name} успешно создан.`);
            resetForm(); // Очищаем форму после успешного создания
        }

        emit('project-saved');
        emit('update:open', false);
    } catch (error: any) {
        console.error(error);
        const errorMessage = error.response?.data?.message || (isEditMode.value ? 'Ошибка обновления' : 'Ошибка создания');
        toast.error(errorMessage);
    }
});
</script>

<template>
    <Dialog :open="open" @update:open="$emit('update:open', $event)">
        <DialogContent class="sm:max-w-[425px]" :onOpenAutoFocus="(e) => e.preventDefault()">
            <DialogHeader>
                <DialogTitle>{{ isEditMode ? 'Редактировать проект' : 'Новый проект' }}</DialogTitle>
                <DialogDescription>
                    {{ isEditMode ? 'Обновите данные проекта.' : 'Заполните данные для создания нового проекта.' }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="onSubmit" class="grid gap-4 py-4">
                <div class="grid gap-2">
                    <Label for="name">Название</Label>
                    <Input id="name" v-model="name" v-bind="nameAttrs" />
                    <span v-if="errors.name" class="text-xs text-red-500">{{ errors.name }}</span>
                </div>

                <div class="grid gap-2">
                    <Label for="description">Описание</Label>
                    <Textarea id="description" v-model="description" v-bind="descriptionAttrs" />
                    <span v-if="errors.description" class="text-xs text-red-500">{{ errors.description }}</span>
                </div>

                <div class="grid gap-2">
                    <Label for="slug">URL (Slug)</Label>
                    <Input id="slug" v-model="slug" v-bind="slugAttrs" />
                    <span class="text-xs text-gray-500">Оставьте пустым для авто-генерации.</span>
                    <span v-if="errors.slug" class="text-xs text-red-500">{{ errors.slug }}</span>
                </div>

                <DialogFooter>
                    <Button type="submit" :disabled="isSubmitting">
                        {{ isSubmitting ? 'Сохранение...' : (isEditMode ? 'Сохранить изменения' : 'Создать') }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>