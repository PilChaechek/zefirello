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
import { useTitle } from '@/composables/useTitle';
import { useTaskMetaStore } from '@/stores/taskMeta';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const route = useRoute();
const projectSlug = route.params.slug as string;

const project = ref<Project | null>(null);
const tasks = ref<Task[]>([]);
const isLoading = ref(true);
const { setTitle, setDescription } = useTitle();

const taskMetaStore = useTaskMetaStore();

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

        // Set the title and description after project is fetched
        if (project.value) {
            setTitle(`Проект: ${project.value.name}`);
            setDescription(project.value.description || '');
        }

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

const facetedFilterColumns = computed(() => [
    {
        id: 'status',
        title: 'Статус',
        options: taskMetaStore.getStatuses.map(s => ({
            label: s.label,
            value: s.value,
            icon: s.icon,
            color: s.color,
        })),
    },
]);

onMounted(() => {
    fetchProjectAndTasks();
    taskMetaStore.fetchTaskMeta();
});
</script>

<template>
    <div class="space-y-8">
        <div v-if="isLoading" class="flex justify-center py-10">
            <span class="text-muted-foreground">Загрузка данных...</span>
        </div>

        <div v-else-if="project">
            <!-- Project Details Section -->
            <!-- Description is now handled by AppLayout via useTitle composable -->

            <!-- Tasks Section -->
            <div class="space-y-4">
                <div class="flex">
                    <Button v-if="authStore.hasRole('admin')" @click="openCreateDialog">Добавить задачу</Button>
                </div>

                <DataTable
                    :columns="taskColumns"
                    :data="tasks"
                    search-placeholder="Найти задачу..."
                    search-column="title"
                    :faceted-filter-columns="facetedFilterColumns"
                />
            </div>
        </div>

        <div v-else class="text-center py-10 text-muted-foreground">
            Проект не найден.
        </div>

        <!-- Modals and Sheets -->
        <TaskDetailSheet v-model:open="isSheetOpen" :task="selectedTask" :project-slug="projectSlug" />
        <TaskFormDialog
            v-if="authStore.hasRole('admin')"
            v-model:open="isFormDialogOpen"
            :task="editingTask"
            :project-slug="projectSlug"
            @task-saved="fetchProjectAndTasks"
        />
    </div>
</template>
