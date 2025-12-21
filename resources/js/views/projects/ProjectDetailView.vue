<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, RouterLink } from 'vue-router';
import axios from 'axios';
import type { Project } from '@/types/project';
import { Button } from '@/components/ui/button';

const route = useRoute();
const projectSlug = route.params.slug as string;

const project = ref<Project | null>(null);
const isLoading = ref(true);

const fetchProject = async () => {
    isLoading.value = true;
    try {
        const { data } = await axios.get<Project>(`/projects/${projectSlug}`);
        project.value = data.data;
    } catch (e) {
        console.error('Ошибка загрузки проекта:', e);
    } finally {
        isLoading.value = false;
    }
};

onMounted(fetchProject);
</script>

<template>
    <div class="space-y-6">
        <div v-if="isLoading" class="flex justify-center py-10">
            <span class="text-muted-foreground">Загрузка проекта...</span>
        </div>

        <div v-else-if="project">
            <h1 class="text-3xl font-bold text-foreground mb-4">Проект: {{ project.name }}</h1>
            <p class="text-muted-foreground">{{ project.description }}</p>

            <div class="mt-6">
                <RouterLink :to="{ name: 'project-tasks', params: { slug: project.slug } }">
                    <Button>Перейти к задачам</Button>
                </RouterLink>
            </div>

            <!-- Здесь в будущем будет управление пользователями и другие детали проекта -->
        </div>

        <div v-else class="text-center py-10 text-muted-foreground">
            Проект не найден.
        </div>
    </div>
</template>
