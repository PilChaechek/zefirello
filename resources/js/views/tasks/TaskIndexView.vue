<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import type { Task } from '@/types/task';
import { Button } from '@/components/ui/button';

const route = useRoute();
const projectSlug = route.params.slug as string;

const tasks = ref<Task[]>([]);
const isLoading = ref(true);

const fetchTasks = async () => {
    isLoading.value = true;
    try {
        const { data } = await axios.get(`/projects/${projectSlug}/tasks`);
        tasks.value = data;
    } catch (e) {
        console.error('Ошибка загрузки задач:', e);
    } finally {
        isLoading.value = false;
    }
};

onMounted(fetchTasks);
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-foreground">Задачи проекта {{ projectSlug }}</h1>
            <Button>Добавить задачу</Button>
        </div>

        <div v-if="isLoading" class="flex justify-center py-10">
            <span class="text-muted-foreground">Загрузка задач...</span>
        </div>

        <div v-else>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Название</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Статус</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Приоритет</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Время (мин)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Исполнитель</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Действия</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                    <tr v-for="task in tasks" :key="task.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ task.id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ task.title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ task.status }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ task.priority }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ task.time_spent }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ task.assignee?.name || 'Не назначен' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <!-- Действия для задачи (редактировать/удалить) будут здесь -->
                            <Button variant="ghost" size="sm">Редактировать</Button>
                            <Button variant="ghost" size="sm" class="text-red-600">Удалить</Button>
                        </td>
                    </tr>
                    <tr v-if="tasks.length === 0">
                        <td :colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">Задачи не найдены.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
