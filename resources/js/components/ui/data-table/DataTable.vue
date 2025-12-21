<!-- resources/js/components/ui/data-table/DataTable.vue -->
<script setup lang="ts" generic="TData, TValue">
import {
    FlexRender,
    getCoreRowModel,
    useVueTable,
    getPaginationRowModel,
    getSortedRowModel,
    getFilteredRowModel,
    type ColumnFiltersState,
    type SortingState,
    type ColumnDef,
} from '@tanstack/vue-table'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { ref } from 'vue'

const props = defineProps<{
    columns: ColumnDef<TData, TValue>[]
    data: TData[]
    searchPlaceholder?: string
    searchColumn?: string
}>()

const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])

const table = useVueTable({
    get data() { return props.data },
    get columns() { return props.columns },
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onSortingChange: updaterOrValue => {
        sorting.value = typeof updaterOrValue === 'function'
            ? updaterOrValue(sorting.value)
            : updaterOrValue
    },
    onColumnFiltersChange: updaterOrValue => {
        columnFilters.value = typeof updaterOrValue === 'function'
            ? updaterOrValue(columnFilters.value)
            : updaterOrValue
    },
    state: {
        get sorting() { return sorting.value },
        get columnFilters() { return columnFilters.value },
    },
    initialState: {
        pagination: {
            pageSize: 20,
        },
    },
})
</script>

<template>
    <div class="flex items-center" v-if="props.searchColumn">
        <Input
            class="max-w-sm dark:text-white"
            :placeholder="props.searchPlaceholder || 'Поиск...'"
            :model-value="table.getColumn(props.searchColumn)?.getFilterValue() as string"
            @update:model-value="table.getColumn(props.searchColumn)?.setFilterValue($event)"
        />
    </div>

    <div class="border rounded-md">
        <Table>
            <TableHeader>
                <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                    <TableHead v-for="header in headerGroup.headers" :key="header.id">
                        <FlexRender
                            v-if="!header.isPlaceholder"
                            :render="header.column.columnDef.header"
                            :props="header.getContext()"
                        />
                    </TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <template v-if="table.getRowModel().rows?.length">
                    <TableRow
                        v-for="row in table.getRowModel().rows"
                        :key="row.id"
                        :data-state="row.getIsSelected() ? 'selected' : undefined"
                    >
                        <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                        </TableCell>
                    </TableRow>
                </template>
                <template v-else>
                    <TableRow>
                        <TableCell :colspan="columns.length" class="h-24 text-center">
                            Нет данных.
                        </TableCell>
                    </TableRow>
                </template>
            </TableBody>
        </Table>
    </div>

    <div class="flex items-center justify-end py-4 space-x-2 text-foreground">
        <div class="flex items-center space-x-2">
            <p class="text-sm font-medium">
                Кол-во строк
            </p>
            <Select
                :model-value="`${table.getState().pagination.pageSize}`"
                @update:model-value="table.setPageSize"
            >
                <SelectTrigger class="h-8 w-[70px]">
                    <SelectValue :placeholder="`${table.getState().pagination.pageSize}`" />
                </SelectTrigger>
                <SelectContent side="top">
                    <SelectItem v-for="pageSize in [10, 20, 30, 40, 50]" :key="pageSize" :value="`${pageSize}`">
                        {{ pageSize }}
                    </SelectItem>
                </SelectContent>
            </Select>
        </div>
        <div class="flex w-[100px] items-center justify-center text-sm font-medium">
            Стр. {{ table.getState().pagination.pageIndex + 1 }} из
            {{ table.getPageCount() }}
        </div>
        <Button
            variant="outline"
            size="sm"
            :disabled="!table.getCanPreviousPage()"
            @click="table.previousPage()"
        >
            Назад
        </Button>
        <Button
            variant="outline"
            size="sm"
            :disabled="!table.getCanNextPage()"
            @click="table.nextPage()"
        >
            Вперед
        </Button>
    </div>
</template>
