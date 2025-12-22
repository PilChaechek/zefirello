<script setup lang="ts">
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuItem,
    SidebarMenuButton,
    SidebarRail,
    useSidebar,
} from "@/components/ui/sidebar";

// Импортируем компоненты Dropdown
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";
import logoCompany from '@/assets/images/zefirello.svg';

// Иконки
import {
    Home, Users, LogOut, User, ChevronsUpDown, Folder
} from 'lucide-vue-next';

const auth = useAuthStore();
const router = useRouter();
const { isMobile, setOpenMobile } = useSidebar();

const handleLogout = async () => {
    await auth.logout();
    router.push({ name: 'login' });
};

const menuItems = [
    { title: "Главная", url: "/", icon: Home , exact: true},
    { title: "Проекты", url: "/projects", icon: Folder },
    { title: "Пользователи", url: "/users", icon: Users },
];

const handleLinkClick = () => {
    if (isMobile.value) {
        setOpenMobile(false);
    }
};
</script>

<template>
    <Sidebar>
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <div class="flex items-center gap-2 px-2 py-2">
                        <img :src="logoCompany" class="h-8 w-8 object-contain" alt="Logo" />
                        <span class="font-bold text-lg truncate group-data-[collapsible=icon]:hidden">Zefirello</span>
                    </div>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <SidebarGroup>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in menuItems" :key="item.title">
                        <SidebarMenuButton as-child tooltip="item.title">
                            <RouterLink
                                :to="item.url"
                                :active-class="item.exact ? '' : 'bg-accent text-accent-foreground'"
                                :exact-active-class="item.exact ? 'bg-accent text-accent-foreground' : ''"
                                @click="handleLinkClick"
                            >
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                            </RouterLink>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <SidebarMenu>
                <SidebarMenuItem>
                    <DropdownMenu>
                        <!-- Кнопка-триггер (Юзер) -->
                        <DropdownMenuTrigger as-child>
                            <SidebarMenuButton
                                size="lg"
                                class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                            >
                                <!-- Вместо Аватарки ставим иконку User в квадратике -->
                                <div class="flex aspect-square size-8 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground">
                                    <User class="size-4" />
                                </div>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth.user?.name || 'User' }}</span>
                                    <span class="truncate text-xs">{{ auth.user?.email || 'no-email' }}</span>
                                </div>
                                <ChevronsUpDown class="ml-auto size-4" />
                            </SidebarMenuButton>
                        </DropdownMenuTrigger>

                        <!-- Выпадающее меню -->
                        <DropdownMenuContent
                            class="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg"
                            :side="isMobile ? 'bottom' : 'right'"
                            align="end"
                            :side-offset="4"
                        >
                            <!-- Пункт Выход -->
                            <DropdownMenuItem @click="handleLogout">
                                <LogOut class="mr-2 h-4 w-4" />
                                Выйти
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarFooter>
    </Sidebar>
</template>
