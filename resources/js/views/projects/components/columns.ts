"use strict"

import { h } from 'vue';
import { RouterLink } from 'vue-router';
import type { ColumnDef } from "@tanstack/vue-table"
import type { Project } from "@/types/project"
import { Button } from '@/components/ui/button';
import { ArrowUpDown } from 'lucide-vue-next';

export const columns: ColumnDef<Project>[] = [
  {
    accessorKey: "id",
    header: "ID",
    enableSorting: true,
  },
  {
    accessorKey: "name",
    header: ({ column }) => {
      return h(Button, {
        variant: "ghost",
        onClick: () => column.toggleSorting(column.getIsSorted() === "asc"),
      }, () => ["Название", h(ArrowUpDown, { class: "ml-2 h-4 w-4" })]);
    },
    cell: ({ row }) => {
        const project = row.original;
        return h(RouterLink, { to: { name: 'project-detail', params: { slug: project.slug } }, class: 'text-primary hover:underline' }, () => project.name);
    },
  },
  {
    accessorKey: "description",
    header: "Описание",
    cell: ({ row }) => {
        return h('div', { class: 'truncate overflow-hidden max-w-md' }, row.getValue("description"));
    },
  },
  {
    accessorKey: "slug",
    header: "Slug",
    cell: ({ row }) => row.getValue("slug"),
  },
  {
    id: "actions",
    cell: () => null, // Placeholder, будет заменен в ProjectIndexView.vue
  },
]
