<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import type { Project } from '@/types/project';
import type { Task } from '@/types/task';
import { Button } from '@/components/ui/button';
import TaskDetailSheet from '@/views/tasks/components/TaskDetailSheet.vue';
import TaskFormDialog from '@/views/tasks/components/TaskFormDialog.vue';
import DataTable from '@/components/ui/data-table/DataTable.vue';
import { columns as taskColumnsDefinition } from '@/views/tasks/components/columns';

const route = useRoute();
const projectSlug = route.params.slug as string;

const project = ref<Project | null>(null);
const tasks = ref<Task[]>([]);
const isLoading = ref(true);

// State for TaskDetailSheet
const isSheetOpen = ref(false);
const selectedTask = ref<Task | null>(null);

// State for TaskFormDialog
const isFormDialogOpen = ref(false);
const editingTask = ref<Task | null>(null);

const fetchProjectAndTasks = async () => {
    isLoading.value = true;
    try {
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

// --- Handlers ---
const openTaskSheet = (task: Task) => {
    selectedTask.value = task;
    isSheetOpen.value = true;
};

const openCreateDialog = () => {
    editingTask.value = null;
    isFormDialogOpen.value = true;
};

const openEditDialog = (task: Task) => {
    editingTask.value = task;
    isFormDialogOpen.value = true;
};

const taskColumns = computed(() =>
    taskColumnsDefinition(projectSlug, openEditDialog, fetchProjectAndTasks, openTaskSheet)
);

onMounted(fetchProjectAndTasks);
</script>

<template>
    <div class="space-y-8">
        <div v-if="isLoading" class="flex justify-center py-10">
            <span class="text-muted-foreground">Загрузка данных...</span>
        </div>

        <div v-else-if="project">
            <!-- Project Details Section -->
            <div>
                <h1 class="text-3xl font-bold text-foreground mb-2">Проект: {{ project.name }}</h1>
                <p class="text-muted-foreground">{{ project.description }}</p>
            </div>

            <!-- Tasks Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-foreground">Задачи</h2>
                    <Button @click="openCreateDialog">Добавить задачу</Button>
                </div>

                <DataTable
                    :columns="taskColumns"
                    :data="tasks"
                    search-placeholder="Найти задачу..."
                    search-column="title"
                />
            </div>
        </div>

        <div v-else class="text-center py-10 text-muted-foreground">
            Проект не найден.
        </div>

        <!-- Modals and Sheets -->
        <TaskDetailSheet v-model:open="isSheetOpen" :task="selectedTask" :project-slug="projectSlug" />
        <TaskFormDialog
            v-model:open="isFormDialogOpen"
            :task="editingTask"
            :project-slug="projectSlug"
            @task-saved="fetchProjectAndTasks"
        />
    </div>
</template>
