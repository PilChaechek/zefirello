<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import type { Project } from '@/types/project';
import type { Task } from '@/types/task';
import { Button } from '@/components/ui/button';

const route = useRoute();
const projectSlug = route.params.slug as string;

const project = ref<Project | null>(null);
const tasks = ref<Task[]>([]);
const isLoading = ref(true);

const fetchProjectAndTasks = async () => {
    isLoading.value = true;
    try {
        // Загружаем и проект, и задачи параллельно для скорости
        const [projectResponse, tasksResponse] = await Promise.all([
            axios.get<{ data: Project }>(`/projects/${projectSlug}`),
            axios.get<{ data: Task[] }>(`/projects/${projectSlug}/tasks`)
        ]);
        project.value = projectResponse.data.data;
        tasks.value = tasksResponse.data.data;
    } catch (e) {
        console.error('Ошибка загрузки проекта или задач:', e);
    } finally {
        isLoading.value = false;
    }
};

onMounted(fetchProjectAndTasks);
</script>

<template>
    <div class="space-y-8">
        <div v-if="isLoading" class="flex justify-center py-10">
            <span class="text-muted-foreground">Загрузка данных...</span>
        </div>

        <div v-else-if="project">
            <!-- Секция с деталями проекта -->
            <div>
                <h1 class="text-3xl font-bold text-foreground mb-2">Проект: {{ project.name }}</h1>
                <p class="text-muted-foreground">{{ project.description }}</p>
            </div>

            <!-- Секция с задачами -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-foreground">Задачи</h2>
                    <Button>Добавить задачу</Button>
                </div>

                <div class="border rounded-md">
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
        </div>

        <div v-else class="text-center py-10 text-muted-foreground">
            Проект не найден.
        </div>
    </div>
</template>
