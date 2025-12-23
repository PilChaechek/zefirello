import type { Attachment } from './attachment';
import type { Project } from './project';
import type { User } from './user';

interface TaskMeta {
    value: string;
    label: string;
    icon?: string;
    color?: string;
}

export interface Task {
    id: number;
    title: string;
    description: string | null;
    status: TaskMeta;
    priority: TaskMeta;
    order: number;
    time_spent: number; // В минутах
    due_date: string | null;
    project_id: number;
    assignee_id: number | null;
    created_at: string;
    updated_at: string;

    // Связи (опционально, будут загружаться через ресурсы)
    project?: Project;
    assignee?: User;
    creator?: User;
    attachments?: Attachment[];
}
