<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import { z } from 'zod';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';

import logoCompany from '@/assets/images/zefirello.svg';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent } from '@/components/ui/card';
import {
    Field,
    FieldGroup,
    FieldLabel,
    FieldError,
} from '@/components/ui/field';

const auth = useAuthStore();
const router = useRouter();

// 1. Схема валидации
const loginSchema = toTypedSchema(z.object({
    email: z.string().email('Введите корректный Email'),
    password: z.string().min(6, 'Пароль должен быть не менее 6 символов'),
}));

// 2. Инициализация формы
const { handleSubmit, errors, defineField, isSubmitting } = useForm({
    validationSchema: loginSchema,
    initialValues: {
        email: '',
        password: ''
    }
});

// Связываем поля (v-model)
const [email, emailAttrs] = defineField('email');
const [password, passwordAttrs] = defineField('password');

// 3. Отправка
const onSubmit = handleSubmit(async (values) => {
    auth.errors = {};
    try {
        await auth.login(values);
        router.push({ name: 'home' });
    } catch (e) {
        // Ошибки API (422) обработаются в шаблоне через auth.errors
    }
});
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center bg-muted p-6 md:p-10">
        <div class="w-full max-w-sm md:max-w-4xl">
            <Card class="overflow-hidden p-0">
                <CardContent class="grid p-0 md:grid-cols-2">

                    <form @submit="onSubmit" class="p-6 md:p-8">
                        <FieldGroup class="flex flex-col gap-6">

                            <div class="flex flex-col items-center gap-2 text-center">
                                <h1 class="text-2xl font-bold">Вход в систему</h1>
                                <p class="text-muted-foreground text-balance text-sm">
                                    Введите данные для входа в аккаунт
                                </p>
                            </div>

                            <!-- data-invalid красит рамку в красный, если есть ошибка -->
                            <Field :data-invalid="!!errors.email || !!auth.errors.email">
                                <FieldLabel for="email">Email</FieldLabel>
                                <Input
                                    id="email"
                                    v-model="email"
                                    v-bind="emailAttrs"
                                    type="email"
                                    placeholder="email@example.com"
                                    :aria-invalid="!!errors.email"
                                />
                                <!-- Ошибка валидации (Zod) -->
                                <FieldError v-if="errors.email">{{ errors.email }}</FieldError>
                                <!-- Ошибка с бэкенда (Laravel) -->
                                <FieldError v-if="auth.errors.email">{{ auth.errors.email[0] }}</FieldError>
                            </Field>

                            <Field :data-invalid="!!errors.password">
                                <FieldLabel for="password">Пароль</FieldLabel>
                                <Input
                                    id="password"
                                    v-model="password"
                                    v-bind="passwordAttrs"
                                    type="password"
                                />
                                <FieldError v-if="errors.password">{{ errors.password }}</FieldError>
                            </Field>

                            <Field>
                                <Button type="submit" class="w-full" :disabled="isSubmitting">
                                    {{ isSubmitting ? 'Вход...' : 'Войти' }}
                                </Button>
                            </Field>

                        </FieldGroup>
                    </form>

                    <div class="relative hidden bg-primary md:flex flex-col items-center justify-center p-6 text-center">

                        <!-- Блок: Лого + Название -->
                        <div class="flex items-center gap-3 mb-4">
                            <!-- Логотип -->
                            <img
                                :src="logoCompany"
                                alt="Zefirello Logo"
                                class="h-12 w-12 object-contain"
                            />

                            <!-- Название -->
                            <!-- БЫЛО: text-foreground (черный) -->
                            <!-- СТАЛО: text-primary-foreground (белый) -->
                            <h2 class="text-4xl font-medium tracking-tight text-primary-foreground">
                                Zefirello
                            </h2>
                        </div>

                        <!-- Подзаголовок -->
                        <!-- БЫЛО: text-muted-foreground (серый, плохо видно) -->
                        <!-- СТАЛО: text-primary-foreground/80 (белый с прозрачностью 80%) -->
                        <p class="text-primary-foreground/80 text-lg font-medium">
                            Система управления задачами
                        </p>

                        <!-- Footer -->
                        <p class="text-sm text-primary-foreground/60 mt-2">
                            Enterprise Edition
                        </p>

                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
