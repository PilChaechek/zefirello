<script setup lang="ts">
import { computed, watch } from 'vue';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import axios from 'axios';
import { toast } from 'vue-sonner';
import type { Task } from '@/types/task';

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
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

// Props & Emits
const props = defineProps<{
    task: Task | null;
    open: boolean;
    projectSlug: string;
}>();

const emit = defineEmits(['update:open', 'task-saved']);

const isEditMode = computed(() => !!props.task);

const formSchema = toTypedSchema(z.object({
    title: z.string().min(2, 'Название должно быть не короче 2 символов').max(255),
    description: z.string().nullable().optional(),
    status: z.enum(['todo', 'in_progress', 'done', 'canceled']),
    priority: z.enum(['low', 'medium', 'high']),
    time_spent: z.number().min(0, 'Время не может быть отрицательным').optional(),
    assignee_id: z.number().nullable().optional(),
    due_date: z.string().nullable().optional(),
}));

const { handleSubmit, errors, defineField, resetForm, setValues, isSubmitting } = useForm({
    validationSchema: formSchema,
    initialValues: {
        status: 'todo',
        priority: 'medium',
    }
});

watch(() => props.task, (currentTask) => {
    if (currentTask) {
        setValues({
            title: currentTask.title,
            description: currentTask.description,
            status: currentTask.status,
            priority: currentTask.priority,
            time_spent: currentTask.time_spent,
            assignee_id: currentTask.assignee_id,
            due_date: currentTask.due_date,
        });
    } else {
        resetForm();
    }
});

const [title, titleAttrs] = defineField('title');
const [description, descriptionAttrs] = defineField('description');
const [status, statusAttrs] = defineField('status');
const [priority, priorityAttrs] = defineField('priority');
const [time_spent, time_spentAttrs] = defineField('time_spent');

const onSubmit = handleSubmit(async (values) => {
    try {
        const payload = {
            ...values,
            // Бэкенд ожидает order, пока добавим 0
            order: props.task?.order ?? 0,
        };

        if (isEditMode.value) {
            await axios.patch(`/projects/${props.projectSlug}/tasks/${props.task!.id}`, payload);
            toast.success(`Задача "${values.title}" успешно обновлена.`);
        } else {
            await axios.post(`/projects/${props.projectSlug}/tasks`, payload);
            toast.success(`Задача "${values.title}" успешно создана.`);
            resetForm();
        }

        emit('task-saved');
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
        <DialogContent class="sm:max-w-[500px]" :onOpenAutoFocus="(e) => e.preventDefault()">
            <DialogHeader>
                <DialogTitle>{{ isEditMode ? 'Редактировать задачу' : 'Новая задача' }}</DialogTitle>
                <DialogDescription>
                    {{ isEditMode ? 'Обновите данные задачи.' : 'Заполните данные для создания новой задачи.' }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="onSubmit" class="grid gap-4 py-4">
                <div class="grid gap-2">
                    <Label for="title">Название</Label>
                    <Input id="title" v-model="title" v-bind="titleAttrs" />
                    <span v-if="errors.title" class="text-xs text-red-500">{{ errors.title }}</span>
                </div>

                <div class="grid gap-2">
                    <Label for="description">Описание</Label>
                    <Textarea id="description" v-model="description" v-bind="descriptionAttrs" />
                    <span v-if="errors.description" class="text-xs text-red-500">{{ errors.description }}</span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="status">Статус</Label>
                        <Select v-model="status" v-bind="statusAttrs">
                            <SelectTrigger>
                                <SelectValue placeholder="Выберите статус" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="todo">К выполнению</SelectItem>
                                <SelectItem value="in_progress">В работе</SelectItem>
                                <SelectItem value="done">Готово</SelectItem>
                                <SelectItem value="canceled">Отменено</SelectItem>
                            </SelectContent>
                        </Select>
                        <span v-if="errors.status" class="text-xs text-red-500">{{ errors.status }}</span>
                    </div>

                    <div class="grid gap-2">
                        <Label for="priority">Приоритет</Label>
                        <Select v-model="priority" v-bind="priorityAttrs">
                            <SelectTrigger>
                                <SelectValue placeholder="Выберите приоритет" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="low">Низкий</SelectItem>
                                <SelectItem value="medium">Средний</SelectItem>
                                <SelectItem value="high">Высокий</SelectItem>
                            </SelectContent>
                        </Select>
                        <span v-if="errors.priority" class="text-xs text-red-500">{{ errors.priority }}</span>
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="time_spent">Затрачено времени (в минутах)</Label>
                    <Input id="time_spent" type="number" v-model.number="time_spent" v-bind="time_spentAttrs" />
                    <span v-if="errors.time_spent" class="text-xs text-red-500">{{ errors.time_spent }}</span>
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
