<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import axios from 'axios';
import { toast } from 'vue-sonner';
import type { User } from '@/types/user';

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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

// Props & Emits
const props = defineProps<{
    user: User | null;
    open: boolean;
}>();

const emit = defineEmits(['update:open', 'user-saved']);

const isEditMode = computed(() => !!props.user);

// Схема валидации меняется в зависимости от режима
const formSchema = computed(() => toTypedSchema(z.object({
    name: z.string().min(2, 'Имя должно быть не короче 2 символов'),
    email: z.string().email('Введите корректный email'),
    // Пароль необязателен для редактирования
    password: isEditMode.value
        ? z.string().min(8, 'Пароль минимум 8 символов').optional().or(z.literal(''))
        : z.string().min(8, 'Пароль минимум 8 символов'),
    role: z.string().min(1, 'Выберите роль'),
})));

const { handleSubmit, errors, defineField, resetForm, setValues, isSubmitting } = useForm({
    validationSchema: formSchema,
});

// Заполняем форму данными при открытии в режиме редактирования
watch(() => props.user, (currentUser) => {
    if (currentUser) {
        setValues({
            name: currentUser.name,
            email: currentUser.email,
            role: currentUser.roles[0]?.name || '', // Берем первую роль
        });
    } else {
        resetForm();
    }
});

const [name, nameAttrs] = defineField('name');
const [email, emailAttrs] = defineField('email');
const [password, passwordAttrs] = defineField('password');
const [role, roleAttrs] = defineField('role');

const onSubmit = handleSubmit(async (values) => {
    try {
        if (isEditMode.value) {
            // Режим редактирования
            await axios.patch(`/users/${props.user!.id}`, values);
            toast.success(`Пользователь ${values.name} успешно обновлен.`);
        } else {
            // Режим создания
            await axios.post('/users', values);
            toast.success(`Пользователь ${values.name} успешно создан.`);
        }

        emit('user-saved');
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
                <DialogTitle>{{ isEditMode ? 'Редактировать пользователя' : 'Новый пользователь' }}</DialogTitle>
                <DialogDescription>
                    {{ isEditMode ? 'Обновите данные сотрудника.' : 'Заполните данные для создания нового сотрудника.' }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="onSubmit" class="grid gap-4 py-4">
                <div class="grid gap-2">
                    <Label for="name">Имя</Label>
                    <Input id="name" v-model="name" v-bind="nameAttrs" />
                    <span v-if="errors.name" class="text-xs text-red-500">{{ errors.name }}</span>
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email" v-model="email" v-bind="emailAttrs" />
                    <span v-if="errors.email" class="text-xs text-red-500">{{ errors.email }}</span>
                </div>

                <div class="grid gap-2">
                    <Label>Роль</Label>
                    <Select v-model="role" v-bind="roleAttrs">
                        <SelectTrigger><SelectValue placeholder="Выберите роль" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="admin">Admin</SelectItem>
                            <SelectItem value="manager">Manager</SelectItem>
                            <SelectItem value="user">User</SelectItem>
                        </SelectContent>
                    </Select>
                    <span v-if="errors.role" class="text-xs text-red-500">{{ errors.role }}</span>
                </div>

                <div class="grid gap-2">
                    <Label for="password">Пароль</Label>
                    <Input id="password" type="password" v-model="password" v-bind="passwordAttrs" :placeholder="isEditMode ? 'Оставьте пустым, чтобы не менять' : ''"/>
                    <span v-if="errors.password" class="text-xs text-red-500">{{ errors.password }}</span>
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

