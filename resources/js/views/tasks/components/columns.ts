"use strict"

import { h } from 'vue';
import type { ColumnDef } from "@tanstack/vue-table"
import type { Task } from "@/types/task"
import { Button } from '@/components/ui/button';
import { ArrowUpDown } from 'lucide-vue-next';
import TaskActions from './TaskActions.vue';

export const columns = (
    projectSlug: string,
    editHandler: (task: Task) => void,
    deleteHandler: () => void,
    openTaskSheet: (task: Task) => void
): ColumnDef<Task>[] => [
    {
        accessorKey: "id",
        header: ({ column }) => h(Button, {
            variant: "ghost",
            onClick: () => column.toggleSorting(column.getIsSorted() === "asc"),
        }, () => ["ID", h(ArrowUpDown, { class: "ml-2 h-4 w-4" })]),
        cell: ({ row }) => h('div', { onClick: () => openTaskSheet(row.original), class: 'cursor-pointer' }, row.original.id),
    },
    {
        accessorKey: "title",
        header: "Название",
        cell: ({ row }) => h('div', { onClick: () => openTaskSheet(row.original), class: 'cursor-pointer' }, row.original.title),
    },
    {
        accessorKey: "status",
        header: "Статус",
        cell: ({ row }) => h('div', { onClick: () => openTaskSheet(row.original), class: 'cursor-pointer' }, row.original.status),
    },
    {
        accessorKey: "priority",
        header: "Приоритет",
        cell: ({ row }) => h('div', { onClick: () => openTaskSheet(row.original), class: 'cursor-pointer' }, row.original.priority),
    },
    {
        accessorKey: "time_spent",
        header: "Время (мин)",
        cell: ({ row }) => h('div', { onClick: () => openTaskSheet(row.original), class: 'cursor-pointer' }, row.original.time_spent),
    },
    {
        id: "actions",
        header: () => h('div', { class: 'text-right' }, 'Действия'),
        cell: ({ row }) => {
            const task = row.original;
            return h('div', { class: 'relative text-right' }, h(TaskActions, {
                task: task,
                projectSlug: projectSlug,
                onEdit: () => editHandler(task),
                onTaskDeleted: deleteHandler,
            }));
        },
    },
];
