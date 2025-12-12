<script setup lang="ts">
import { SidebarProvider, SidebarInset, SidebarTrigger } from "@/components/ui/sidebar";
import AppSidebar from "@/components/layout/AppSidebar.vue";
import { Separator } from "@/components/ui/separator";
import ModeToggle from "@/components/layout/ModeToggle.vue";
</script>

<template>
    <!-- SidebarProvider управляет состоянием (открыт/закрыт) -->
    <SidebarProvider>
        <AppSidebar />

        <SidebarInset>
            <!-- Хедер лейаута -->
            <header class="flex h-16 shrink-0 items-center gap-2 border-b px-4 justify-between text-foreground">
                <div class="flex items-center gap-2">
                    <SidebarTrigger class="-ml-1" />
                    <Separator orientation="vertical" class="mr-2 h-4" />
                    <h2 class="font-semibold">Dashboard</h2>
                </div>

                <!-- Вставляем переключатель -->
                <div class="flex items-center gap-2">
                    <ModeToggle />
                </div>
            </header>

            <!-- Сюда Vue Router будет подставлять HomeView, TasksView и т.д. -->
            <div class="flex flex-1 flex-col gap-4 p-4">
                <RouterView />
            </div>
        </SidebarInset>
    </SidebarProvider>
</template>
