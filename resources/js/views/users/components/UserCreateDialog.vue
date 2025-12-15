<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import axios from 'axios';

// UI Components
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
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
const emit = defineEmits(['user-created']);

// Состояние модалки
const isOpen = ref(false);

// 1. Схема валидации (Zod)
const formSchema = toTypedSchema(z.object({
    name: z.string().min(2, 'Имя должно быть не короче 2 символов'),
    email: z.string().email('Введите корректный email'),
    password: z.string().min(8, 'Пароль минимум 8 символов'),
    role: z.string().min(1, 'Выберите роль'),
}));

// 2. Инициализация формы
const { handleSubmit, errors, defineField, resetForm, isSubmitting } = useForm({
    validationSchema: formSchema,
});

// Биндинг полей (v-model)
const [name, nameAttrs] = defineField('name');
const [email, emailAttrs] = defineField('email');
const [password, passwordAttrs] = defineField('password');
const [role, roleAttrs] = defineField('role');

// 3. Отправка формы
const onSubmit = handleSubmit(async (values) => {
    try {
        await axios.post('/users', values);

        // Успех
        isOpen.value = false; // Закрываем модалку
        resetForm();          // Чистим форму
        emit('user-created'); // Сообщаем родителю, что надо обновить список

    } catch (error: any) {
        console.error(error);
        // Тут можно добавить обработку ошибок от бэкенда (например, "Email занят")
        alert('Ошибка при создании. Проверь консоль.');
    }
});
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger as-child class="mt-4">
            <Button>Добавить пользователя</Button>
        </DialogTrigger>

        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Новый пользователь</DialogTitle>
                <DialogDescription>
                    Заполните данные для создания нового сотрудника.
                </DialogDescription>
            </DialogHeader>

            <form @submit="onSubmit" class="grid gap-4 py-4">

                <!-- Name -->
                <div class="grid gap-2">
                    <Label for="name">Имя</Label>
                    <Input id="name" v-model="name" v-bind="nameAttrs" placeholder="Ivan Ivanov" />
                    <span v-if="errors.name" class="text-xs text-red-500">{{ errors.name }}</span>
                </div>

                <!-- Email -->
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email" v-model="email" v-bind="emailAttrs" placeholder="ivan@example.com" />
                    <span v-if="errors.email" class="text-xs text-red-500">{{ errors.email }}</span>
                </div>

                <!-- Role Select -->
                <div class="grid gap-2">
                    <Label>Роль</Label>
                    <Select v-model="role" v-bind="roleAttrs">
                        <SelectTrigger>
                            <SelectValue placeholder="Выберите роль" />
                        </SelectTrigger>
                        <SelectContent>
                            <!-- В реальном проекте список ролей должен приходить с API -->
                            <SelectItem value="admin">Admin</SelectItem>
                            <SelectItem value="manager">Manager</SelectItem>
                            <SelectItem value="user">User</SelectItem>
                        </SelectContent>
                    </Select>
                    <span v-if="errors.role" class="text-xs text-red-500">{{ errors.role }}</span>
                </div>

                <!-- Password -->
                <div class="grid gap-2">
                    <Label for="password">Пароль</Label>
                    <Input id="password" type="password" v-model="password" v-bind="passwordAttrs" />
                    <span v-if="errors.password" class="text-xs text-red-500">{{ errors.password }}</span>
                </div>

                <DialogFooter>
                    <Button type="submit" :disabled="isSubmitting">
                        {{ isSubmitting ? 'Сохранение...' : 'Создать' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
