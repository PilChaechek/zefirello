<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import type { Project } from '@/types/project';
import ProjectFormDialog from './components/ProjectFormDialog.vue';
import ProjectActions from './components/ProjectActions.vue'; // Импортируем новый компонент
import { Button } from '@/components/ui/button';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';

const projects = ref<Project[]>([]);
const isLoading = ref(true);
const isFormDialogOpen = ref(false);
const editingProject = ref<Project | null>(null);

const fetchProjects = async () => {
    isLoading.value = true;
    try {
        const { data } = await axios.get('/projects');
        projects.value = data; // API возвращает массив напрямую
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

onMounted(fetchProjects);
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <Button @click="openCreateDialog">Добавить проект</Button>
        </div>

        <div v-if="isLoading" class="flex justify-center py-10">
            <span class="text-muted-foreground">Загрузка данных...</span>
        </div>

        <div v-else>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Название</TableHead>
                        <TableHead>Описание</TableHead>
                        <TableHead>Slug</TableHead>
                        <TableHead class="text-right">Действия</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-if="projects.length === 0">
                        <TableCell :colspan="4" class="text-center">
                            Проекты еще не созданы.
                        </TableCell>
                    </TableRow>
                    <TableRow v-for="project in projects" :key="project.id">
                        <TableCell>{{ project.name }}</TableCell>
                        <TableCell>{{ project.description }}</TableCell>
                        <TableCell>{{ project.slug }}</TableCell>
                        <TableCell class="text-right">
                            <ProjectActions
                                :project="project"
                                @edit="openEditDialog"
                                @project-deleted="fetchProjects"
                            />
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <ProjectFormDialog
            v-model:open="isFormDialogOpen"
            :project="editingProject"
            @project-saved="fetchProjects"
        />
    </div>
</template>
