// resources/js/views/users/components/columns.ts
import type { ColumnDef } from '@tanstack/vue-table'
import type { User } from '@/types/user'
import { h } from 'vue'
import { ArrowUpDown } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import UserActions from './UserActions.vue' // <--- Импортируем компонент

// Функция для динамического определения классов для ролей
const getRoleClasses = (roleName: string) => {
    switch (roleName) {
        case 'admin':
            return 'bg-red-100 text-red-700';
        case 'manager':
            return 'bg-green-100 text-green-700';
        case 'user':
            return 'bg-blue-100 text-blue-700';
        default:
            return 'bg-gray-100 text-gray-700';
    }
};

export const columns: ColumnDef<User>[] = [
    {
        accessorKey: 'id',
        header: 'ID',
    },
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => ['Имя', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
        },
    },
    {
        accessorKey: 'email',
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => ['Email', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
        },
    },
    {
        accessorKey: 'roles',
        header: 'Роли',
        cell: ({ row }) => {
            const roles = row.original.roles;
            if (!roles.length) return h('span', { class: 'text-gray-400 text-xs' }, 'Нет ролей');

            return h('div', { class: 'flex gap-1 flex-wrap' }, roles.map(role =>
                h('span', {
                    class: `px-2 py-1 text-xs font-medium rounded-full ${getRoleClasses(role.name)}`
                }, role.label)
            ));
        }
    },
    {
        accessorKey: 'created_at',
        header: 'Дата регистрации',
        cell: ({ row }) => {
            return new Date(row.getValue('created_at')).toLocaleDateString()
        }
    },
    {
        id: 'actions',
        enableHiding: false,
        cell: ({ row }) => {
            const user = row.original;

            // Рендерим Vue-компонент внутри ячейки
            return h(UserActions, {
                user: user,
                onEdit: (userToEdit) => { },
            })
        },
    },
]
