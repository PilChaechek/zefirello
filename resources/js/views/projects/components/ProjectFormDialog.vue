<script setup lang="ts">
import { computed, watch, ref, onMounted } from 'vue';
import { useForm, useFieldValue } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import axios from 'axios';
import { toast } from 'vue-sonner';
import type { Project } from '@/types/project';
import type { User } from '@/types/user';
import { useAuthStore } from '@/stores/auth';

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
import { ChevronsUpDown, Check } from 'lucide-vue-next';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { cn } from '@/lib/utils';

// Props & Emits
const props = defineProps<{
    project: Project | null;
    open: boolean;
}>();

const emit = defineEmits(['update:open', 'project-saved']);

const auth = useAuthStore();
const isEditMode = computed(() => !!props.project);
const allUsers = ref<User[]>([]);
const isUsersComboboxOpen = ref(false);

const formSchema = toTypedSchema(z.object({
    name: z.string().min(2, 'Название должно быть не короче 2 символов').max(255),
    description: z.string().nullable().optional(),
    slug: z.string().nullable().optional(),
    users: z.array(z.number()).optional(),
}));

const { handleSubmit, errors, defineField, resetForm, setValues, isSubmitting, setFieldValue } = useForm({
    validationSchema: formSchema,
});

const [name, nameAttrs] = defineField('name');
const [description, descriptionAttrs] = defineField('description');
const [slug, slugAttrs] = defineField('slug');
// Используем useFieldValue для чистого Ref без лишних атрибутов
const userIds = useFieldValue<number[]>('users');

// Загрузка всех пользователей для селекта
const fetchUsers = async () => {
    try {
        const response = await axios.get<{ data: User[] }>('/users');
        allUsers.value = response.data.data;
    } catch (e) {
        toast.error('Ошибка загрузки списка пользователей');
        console.error(e);
    }
};

onMounted(fetchUsers);


// Заполняем форму данными при открытии
watch(() => props.open, (isOpen) => {
    if (!isOpen) return;

    if (props.project) { // Режим редактирования
        setValues({
            name: props.project.name,
            description: props.project.description,
            slug: props.project.slug,
            users: props.project.users?.map(u => u.id) || [],
        });
    } else { // Режим создания
        resetForm({
            values: {
                name: '',
                description: '',
                slug: '',
                users: auth.user ? [auth.user.id] : [],
            },
        });
    }
});

const selectedUsers = computed(() =>
    allUsers.value.filter(user => userIds.value?.includes(user.id)),
);

const handleUserSelect = (userId: number) => {
    const selected = [...(userIds.value || [])];
    const index = selected.indexOf(userId);
    if (index > -1) {
        selected.splice(index, 1);
    } else {
        selected.push(userId);
    }
    // Используем setFieldValue для обновления значения в vee-validate
    setFieldValue('users', selected);
};

const onSubmit = handleSubmit(async (values) => {
    // ВЫВОДИМ ДАННЫЕ В КОНСОЛЬ ПЕРЕД ОТПРАВКОЙ
    console.log('Submitting form with values:', values);

    try {
        if (isEditMode.value) {
            await axios.patch(`/projects/${props.project!.slug}`, values);
            toast.success(`Проект ${values.name} успешно обновлен.`);
        } else {
            await axios.post('/projects', values);
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
        <DialogContent class="sm:max-w-[525px]" :onOpenAutoFocus="(e) => e.preventDefault()">
            <DialogHeader>
                <DialogTitle>{{ isEditMode ? 'Редактировать проект' : 'Новый проект' }}</DialogTitle>
                <DialogDescription>
                    {{ isEditMode ? 'Обновите данные проекта.' : 'Заполните данные и назначьте участников.' }}
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

                <!-- Users Combobox -->
                <div class="grid gap-2">
                    <Label>Участники</Label>
                    <Popover v-model:open="isUsersComboboxOpen">
                        <PopoverTrigger as-child>
                            <Button
                                variant="outline"
                                role="combobox"
                                class="w-full justify-between"
                            >
                                <span v-if="selectedUsers.length > 0" class="truncate">
                                    {{ selectedUsers.map(u => u.name).join(', ') }}
                                </span>
                                <span v-else class="text-muted-foreground">Выберите участников...</span>
                                <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-[--radix-popover-trigger-width] p-0">
                            <Command>
                                <CommandInput placeholder="Найти пользователя..." />
                                <CommandList>
                                    <CommandEmpty>Пользователи не найдены.</CommandEmpty>
                                    <CommandGroup>
                                        <CommandItem
                                            v-for="user in allUsers"
                                            :key="user.id"
                                            :value="user.id"
                                            @select.prevent="handleUserSelect(user.id)"
                                        >
                                            <div :class="cn('mr-2 flex h-4 w-4 items-center justify-center rounded-sm border border-primary',
                                                 userIds?.includes(user.id) ? 'bg-primary text-primary-foreground' : 'opacity-50 [&_svg]:invisible'
                                            )">
                                                <Check class="h-4 w-4" />
                                            </div>
                                            <span>{{ user.name }}</span>
                                        </CommandItem>
                                    </CommandGroup>
                                </CommandList>
                            </Command>
                        </PopoverContent>
                    </Popover>
                    <span v-if="errors.users" class="text-xs text-red-500">{{ errors.users }}</span>
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