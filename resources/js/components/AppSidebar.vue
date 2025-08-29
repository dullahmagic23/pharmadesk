<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {computed} from 'vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import MedicalMenuComponent from './menus/MedicalMenuComponent.vue';
import CashierMenuComponent from '@/components/menus/CashierMenuComponent.vue';

interface Role{
    id:string,
    name:string
}
interface Auth {
    user: {
        roles: Role[]
    };
}

const page = usePage();
const auth = page.props.auth as Auth;

const isAdmin = computed(() => auth.user.roles.find(r=>r.name =='admin'));
const isCashier = computed(()=>auth.user.roles.find(r=>r.name == 'cashier'));

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <!-- <NavMain :items="mainNavItems" /> -->
            <MedicalMenuComponent v-if="isAdmin"/>
            <CashierMenuComponent v-if="isCashier"/>
        </SidebarContent>

        <SidebarFooter>
            <!-- <NavFooter :items="footerNavItems" /> -->
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
<style scoped>
.sidebar-animated {
    transition: background 0.3s, box-shadow 0.3s, filter 0.3s;
}
.sidebar-gradient {
    background: linear-gradient(135deg, #f3f4f6 0%, #e0e7ff 100%);
}
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #a78bfa #f3f4f6;
}
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: linear-gradient(90deg, #6366f1 0%, #a78bfa 100%);
    border-radius: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f3f4f6;
}
.sidebar-menu-item {
    transition: background 0.2s, color 0.2s, box-shadow 0.2s, border-left 0.2s;
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    margin-bottom: 2px;
    box-shadow: 0 1px 2px rgba(99,102,241,0.04);
    position: relative;
}
.sidebar-menu-item:hover,
.sidebar-menu-item:focus {
    background: #f3f4f6;
    color: #6366f1;
    box-shadow: 0 2px 8px rgba(99,102,241,0.08);
}
.sidebar-menu-item.active {
    background: linear-gradient(90deg, #6366f1 0%, #a78bfa 100%);
    color: #fff;
    box-shadow: 0 4px 16px rgba(99,102,241,0.12);
    border-left: 4px solid #a78bfa;
}
.sidebar-menu-item.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 8px;
    height: 8px;
    background: #a78bfa;
    border-radius: 50%;
}
.sidebar-footer {
    box-shadow: 0 -2px 8px rgba(0,0,0,0.03);
}
.sidebar-header {
    letter-spacing: 0.05em;
    font-weight: 600;
    font-size: 1.1rem;
}
/* Tooltip for collapsed icons */
.sidebar-menu-button[aria-label] {
    position: relative;
}
.sidebar-menu-button[aria-label]:hover::after {
    content: attr(aria-label);
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%) translateX(8px);
    background: #6366f1;
    color: #fff;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.85rem;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(99,102,241,0.12);
    z-index: 20;
}
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #a78bfa #f3f4f6;
}
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: linear-gradient(90deg, #6366f1 0%, #a78bfa 100%);
    border-radius: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f3f4f6;
}
.sidebar-menu-item {
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    margin-bottom: 2px;
    box-shadow: 0 1px 2px rgba(99,102,241,0.04);
}
.sidebar-menu-item:hover,
.sidebar-menu-item:focus {
    background: #f3f4f6;
    color: #6366f1;
    box-shadow: 0 2px 8px rgba(99,102,241,0.08);
}
.sidebar-menu-item.active {
    background: linear-gradient(90deg, #6366f1 0%, #a78bfa 100%);
    color: #fff;
    box-shadow: 0 4px 16px rgba(99,102,241,0.12);
}
.sidebar-footer {
    box-shadow: 0 -2px 8px rgba(0,0,0,0.03);
}
</style>
