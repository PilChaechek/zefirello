<script setup lang="ts">
import { ref, onMounted, h, computed } from 'vue';
import axios from 'axios';
import type { Project } from '@/types/project';
import ProjectFormDialog from './components/ProjectFormDialog.vue';
import ProjectActions from './components/ProjectActions.vue';
import DataTable from '@/components/ui/data-table/DataTable.vue';
import { columns as originalColumns } from './components/columns';
import { Button } from '@/components/ui/button';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const projects = ref<Project[]>([]);
const isLoading = ref(true);
const isFormDialogOpen = ref(false);
const editingProject = ref<Project | null>(null);

const fetchProjects = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get<{ data: Project[] }>('/projects');
        projects.value = response.data.data;
    } catch (e) {
        console.error('Ошибка загрузки проектов:', e);
    } finally {
        isLoading.value = false;
    }
};

const openCreateDialog = () => {
    editingProject.value = null;
    isFormDialogOpen.value = true;
};

const openEditDialog = (project: Project) => {
    editingProject.value = project;
    isFormDialogOpen.value = true;
};

// Динамически вставляем компонент ProjectActions и фильтруем колонку "Действия"
const columns = computed(() => {
    const mappedColumns = originalColumns.map(column => {
        if (column.id === 'actions') {
            return {
                ...column,
                header: () => h('div', { class: 'text-right' }, 'Действия'),
                cell: ({ row }) => {
                    const project = row.original;
                    return h('div', { class: 'relative text-right' }, h(ProjectActions, {
                        project: project,
                        onEdit: () => openEditDialog(project),
                        onProjectDeleted: fetchProjects,
                    }));
                },
            };
        }
        return column;
    });

    if (authStore.hasRole('admin')) {
        return mappedColumns;
    }

    return mappedColumns.filter(column => column.id !== 'actions');
});

onMounted(fetchProjects);
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <Button v-if="authStore.hasRole('admin')" @click="openCreateDialog">Добавить проект</Button>
        </div>

        <div v-if="isLoading" class="flex justify-center py-10">
            <span class="text-muted-foreground">Загрузка данных...</span>
        </div>

        <DataTable
            v-else
            :columns="columns"
            :data="projects"
            search-placeholder="Найти проект..."
            search-column="name"
        />

        <ProjectFormDialog
            v-if="authStore.hasRole('admin')"
            v-model:open="isFormDialogOpen"
            :project="editingProject"
            @project-saved="fetchProjects"
        />
    </div>
</template>
