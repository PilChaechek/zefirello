// resources/js/composables/useTitle.ts
import { ref, watch } from 'vue';
import { useRoute } from 'vue-router';

const pageTitle = ref('');

export function useTitle() {
    const route = useRoute();

    const setTitle = (newTitle: string) => {
        pageTitle.value = newTitle;
    };

    // This watcher now ALWAYS updates the title from the route meta.
    // This acts as a "reset" on every navigation.
    // Components with dynamic titles will then overwrite it after they mount.
    watch(
        () => route.meta.title,
        (newMetaTitle) => {
            if (newMetaTitle) {
                pageTitle.value = newMetaTitle as string;
            }
        },
        { immediate: true }
    );

    return { pageTitle, setTitle };
}
