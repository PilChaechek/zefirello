import { defineStore } from 'pinia';
import axios from 'axios';

interface MetaItem {
    value: string;
    label: string;
}

interface TaskMetaState {
    statuses: MetaItem[];
    priorities: MetaItem[];
    loading: boolean;
    error: string | null;
}

export const useTaskMetaStore = defineStore('taskMeta', {
    state: (): TaskMetaState => ({
        statuses: [],
        priorities: [],
        loading: false,
        error: null,
    }),
    actions: {
        async fetchTaskMeta() {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get<{ statuses: MetaItem[], priorities: MetaItem[] }>('/tasks/meta');
                this.statuses = response.data.statuses;
                this.priorities = response.data.priorities;
            } catch (error: any) {
                this.error = error.message || 'Failed to fetch task metadata.';
                console.error('Error fetching task meta:', error);
            } finally {
                this.loading = false;
            }
        },
    },
    getters: {
        getStatuses: (state) => state.statuses,
        getPriorities: (state) => state.priorities,
    },
});

