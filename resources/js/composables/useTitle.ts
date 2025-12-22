// resources/js/composables/useTitle.ts
import { ref, watch } from 'vue';
import { useRoute } from 'vue-router';

const pageTitle = ref('');
const pageDescription = ref('');

export function useTitle() {
    const route = useRoute();

    watch(() => route.meta.title as string | undefined, (newTitle) => {
        if (newTitle) {
            pageTitle.value = newTitle;
            document.title = `Zefirello | ${newTitle}`;
        } else {
            pageTitle.value = '';
            document.title = 'Zefirello';
        }
        // Clear description on route change unless explicitly set by the new route's meta
        if (!route.meta.description) {
            pageDescription.value = '';
        }
    }, { immediate: true });

    const setTitle = (title: string) => {
        pageTitle.value = title;
        document.title = `Zefirello | ${title}`;
    };

    const setDescription = (description: string) => {
        pageDescription.value = description;
    };

    return {
        pageTitle,
        pageDescription,
        setTitle,
        setDescription,
    };
}
