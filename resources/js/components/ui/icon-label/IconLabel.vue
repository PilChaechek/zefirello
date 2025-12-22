<script setup lang="ts">
import { computed, defineAsyncComponent } from 'vue';

const props = defineProps<{
    icon: string;
    label: string;
    color?: string;
}>();

const LucideIcon = computed(() => {
    if (!props.icon) return null;
    return defineAsyncComponent(() =>
        import(`lucide-vue-next`)
            .then(mod => mod[props.icon as keyof typeof mod])
            .catch(() => null)
    );
});
</script>

<template>
    <span class="flex items-center gap-1.5" :class="color">
        <component :is="LucideIcon" v-if="LucideIcon" class="h-4 w-4" />
        <span>{{ label }}</span>
    </span>
</template>
