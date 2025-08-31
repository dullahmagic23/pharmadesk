<script setup lang="ts">
import {
    ListCheckIcon,
    PlusCircleIcon,
    ListFilter,
    ListCollapse,
    FileTextIcon,
    LandmarkIcon,
    UsersRound,
    ChevronRightCircle,
    StoreIcon,
    UsersRoundIcon,
    CircleDollarSignIcon,
} from 'lucide-vue-next';

import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuGroup } from '@/components/ui/dropdown-menu';

import { SidebarMenuButton } from '@/components/ui/sidebar';
import { useSidebar } from '@/components/ui/sidebar';
import SidebarDropdownMenuItem from '@/components/SidebarDropdownMenuItem.vue';
import { usePage } from '@inertiajs/vue3';



const { isMobile } = useSidebar();

const medicalItems = [
    {
        title: 'Sales',
        icon: CircleDollarSignIcon,
        children: [
            { title: 'New Sale', href: '/sales/create', icon: PlusCircleIcon },
            { title: 'Manage Sales', href: '/sales', icon: ListFilter },
            {title:'Receipts',href:'/receipts',icon:FileTextIcon},
        ],
    },



    {
        title: 'Medicines',
        icon: ListCheckIcon,
        children: [
            { title: 'Add Medicine', href: '/medicines/create', icon: PlusCircleIcon },
            { title: 'Manage Medicines', href: '/medicines', icon: ListFilter },
            { title: 'Medicine Categories', href: '/medicine-categories', icon: ListCollapse },
        ],
    },

    {
        title: 'Products',
        icon: StoreIcon,
        children: [
            { title: 'Add Product', href: '/products/create', icon: PlusCircleIcon },
            { title: 'Manage Products', href: '/products', icon: ListFilter },
        ],
    },

    {
        title: 'People',
        icon: UsersRound,
        children: [
            {
                title: 'Customers',
                icon: UsersRound,
                children: [
                    { title: 'Register Customers', href: '/customers/create', icon: PlusCircleIcon },
                    { title: 'Manage Customer', href: '/customers', icon: ListFilter },
                ],
            },
            {
                title: 'Patients',
                icon: UsersRound,
                children: [
                    { title: 'Register Patient', href: '/patients/create', icon: PlusCircleIcon },
                    { title: 'Manage Patients', href: '/patients', icon: ListFilter },
                ],
            },
        ],
    },
    {
        title: 'Accounting',
        icon: FileTextIcon,
        children: [
            {
                title: 'Invoices',
                href: '/invoices',
                icon: FileTextIcon,
                children: [
                    { title: 'New Invoice', href: '/invoices/create', icon: PlusCircleIcon },
                    { title: 'Manage Invoices', href: '/invoices', icon: ListFilter },
                ],
            },
            {
                title: 'Expenses',
                href: '/expenses',
                icon: FileTextIcon,
                children: [
                    { title: 'New Expense', href: '/expenses/create', icon: PlusCircleIcon },
                    { title: 'Manage Expenses', href: '/expenses', icon: ListFilter },
                ],
            },
            {
                title: 'Payments',
                href: '/payments',
                icon: FileTextIcon,
                children: [
                    { title: 'New Payment', href: '/payments/create', icon: PlusCircleIcon },
                    { title: 'Manage Payments', href: '/payments', icon: ListFilter },
                ],
            },
            {
                title: 'Vendors',
                href: '/vendors',
                icon: FileTextIcon,
                children: [
                    { title: 'New Vendor', href: '/vendors/create', icon: PlusCircleIcon },
                    { title: 'Manage Vendors', href: '/vendors', icon: ListFilter },
                ],
            },
            {
                title: 'Suppliers',
                href: '/suppliers',
                icon: UsersRoundIcon,
                children: [
                    { title: 'New Supplier', href: '/suppliers/create', icon: PlusCircleIcon },
                    { title: 'Manage Suppliers', href: '/suppliers', icon: ListFilter },
                ],
            },
        ],
    },
];
</script>

<template>
    <div class="space-y-2">
        <DropdownMenu v-for="item in medicalItems" :key="item.title">
            <DropdownMenuTrigger as-child>
                <SidebarMenuButton size="lg" class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground">
                    <component :is="item.icon" />
                    {{ item.title }}
                    <ChevronRightCircle class="ml-auto size-4" />
                </SidebarMenuButton>
            </DropdownMenuTrigger>
            <DropdownMenuContent
                class="min-w-56 rounded-lg border p-1 shadow-md"
                :side="isMobile ? 'bottom' : 'right'"
                align="start"
                :side-offset="6"
            >
                <DropdownMenuGroup>
                    <!-- Recursively render children -->
                    <SidebarDropdownMenuItem v-for="child in item.children" :key="child.href || child.title" :item="child" />
                </DropdownMenuGroup>
            </DropdownMenuContent>
        </DropdownMenu>
    </div>
</template>
