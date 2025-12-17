<script setup lang="ts">
// ProjectDetailView.vue
import { useRoute } from 'vue-router';

const route = useRoute();
const projectSlug = route.params.slug;
</script>

<template>
    <div>
        <h1>Проект: {{ projectSlug }}</h1>
        <p>Здесь будут детали проекта, управление пользователями и доска задач.</p>
    </div>
</template>
