<script setup lang="ts">
import { computed, watch } from 'vue';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import axios from 'axios';
import { toast } from 'vue-sonner';
// import type { Project } from '@/types/project'; // TODO: Создать тип Project

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
import { Textarea } from '@/components/ui/textarea'; // Используем Textarea для описания

// Props & Emits
const props = defineProps<{
    project: any | null; // TODO: Заменить any на Project
    open: boolean;
}>();

const emit = defineEmits(['update:open', 'project-saved']);

const isEditMode = computed(() => !!props.project);

// Схема валидации
const formSchema = toTypedSchema(z.object({
    name: z.string().min(3, 'Название должно быть не короче 3 символов'),
    description: z.string().optional(),
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
        });
    } else {
        resetForm();
    }
});

const [name, nameAttrs] = defineField('name');
const [description, descriptionAttrs] = defineField('description');

const onSubmit = handleSubmit(async (values) => {
    try {
        if (isEditMode.value) {
            // TODO: Режим редактирования
            // await axios.patch(`/api/v1/projects/${props.project!.id}`, values);
            toast.success(`Проект ${values.name} успешно обновлен.`);
        } else {
            // TODO: Режим создания
            // await axios.post('/api/v1/projects', values);
            toast.success(`Проект ${values.name} успешно создан.`);
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
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>{{ isEditMode ? 'Редактировать проект' : 'Новый проект' }}</DialogTitle>
                <DialogDescription>
                    {{ isEditMode ? 'Обновите данные проекта.' : 'Заполните данные для создания нового проекта.' }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="onSubmit" class="grid gap-4 py-4">
                <!-- Name -->
                <div class="grid gap-2">
                    <Label for="name">Название</Label>
                    <Input id="name" v-model="name" v-bind="nameAttrs" />
                    <span v-if="errors.name" class="text-xs text-red-500">{{ errors.name }}</span>
                </div>

                <!-- Description -->
                <div class="grid gap-2">
                    <Label for="description">Описание</Label>
                    <Textarea id="description" v-model="description" v-bind="descriptionAttrs" />
                    <span v-if="errors.description" class="text-xs text-red-500">{{ errors.description }}</span>
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
