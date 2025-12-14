<!-- src/layouts/AppLayout.vue -->
<script setup lang="ts">
import { SidebarProvider, SidebarInset, SidebarTrigger } from "@/components/ui/sidebar";
import AppSidebar from "@/components/layout/AppSidebar.vue";
import { Separator } from "@/components/ui/separator";
import ModeToggle from "@/components/layout/ModeToggle.vue";
import { useRoute } from 'vue-router';
import { computed } from 'vue';

const route = useRoute();
const pageTitle = computed(() => route.meta.title || 'Title');
</script>

<template>
    <SidebarProvider>
        <AppSidebar />

        <SidebarInset>
            <header class="flex h-16 shrink-0 items-center gap-2 border-b px-4 justify-between text-foreground">
                <div class="flex items-center gap-2">
                    <SidebarTrigger class="-ml-1" />
                    <Separator orientation="vertical" class="mr-2 h-4" />
                    <h2 class="font-semibold">{{ pageTitle }}</h2>
                </div>

                <div class="flex items-center gap-2">
                    <ModeToggle />
                </div>
            </header>

            <div class="flex flex-1 flex-col gap-4 p-4">
                <RouterView />
            </div>
        </SidebarInset>
    </SidebarProvider>
</template>
