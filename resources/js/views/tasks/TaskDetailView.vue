<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, RouterLink } from 'vue-router';
import axios from 'axios';
import type { Task } from '@/types/task';
import TaskDetails from './components/TaskDetails.vue';
import { Button } from '@/components/ui/button';

const route = useRoute();
const projectSlug = route.params.slug as string;
const taskId = route.params.taskId as string;

const task = ref<Task | null>(null);
const isLoading = ref(true);

const fetchTask = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get<{ data: Task }>(`/projects/${projectSlug}/tasks/${taskId}`);
        task.value = response.data.data;
    } catch (e) {
        console.error('Ошибка загрузки задачи:', e);
    } finally {
        isLoading.value = false;
    }
};

onMounted(fetchTask);
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-foreground">Задача #{{ taskId }}</h1>
            <RouterLink :to="{ name: 'project-detail', params: { slug: projectSlug } }">
                <Button variant="outline">Вернуться к проекту</Button>
            </RouterLink>
        </div>

        <div v-if="isLoading" class="flex justify-center py-10">
            <span class="text-muted-foreground">Загрузка задачи...</span>
        </div>

        <div v-else-if="task" class="p-6 border rounded-lg">
            <TaskDetails :task="task" />
        </div>

        <div v-else class="text-center py-10 text-muted-foreground">
            Задача не найдена.
        </div>
    </div>
</template>
