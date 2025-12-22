import type { Project } from './project';
import type { User } from './user';

export interface Task {
    id: number;
    title: string;
    description: string | null;
    status: string;
    status_label?: string; // Добавлено
    priority: string;
    priority_label?: string; // Добавлено
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
}
