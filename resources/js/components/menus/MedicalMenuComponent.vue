<script setup lang="ts">
import {
    ListCheckIcon,
    PlusCircleIcon,
    ListFilter,
    ListCollapse,
    FileTextIcon,
    LandmarkIcon,
    UsersRound,
    ChartBar,
    CalendarIcon,
    ChevronRightCircle,
    StoreIcon,
    ListPlusIcon,
    Settings2Icon,
    HeartIcon,
    UsersRoundIcon,
    CircleDollarSignIcon,
    Users2Icon,
    UserPlus,
    UserSquare2Icon,
    Users2,
    FileImageIcon,
    Recycle,
    UsersIcon,
    UserPlus2Icon,
    PlusSquare,
    DollarSignIcon,
} from 'lucide-vue-next';

import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuGroup } from '@/components/ui/dropdown-menu';

import { SidebarMenuButton } from '@/components/ui/sidebar';
import { useSidebar } from '@/components/ui/sidebar';
import SidebarDropdownMenuItem from '@/components/SidebarDropdownMenuItem.vue';
import { usePage } from '@inertiajs/vue3';
import {computed} from 'vue';

const page = usePage();



const { isMobile } = useSidebar();

const hasAccess = (item: any)=>{
    if (!item.roles) return true;
    return item.roles.some((role: string) => auth.user.roles.includes(role));
}

const medicalItems = [
    {
        title: 'Sales',
        icon: CircleDollarSignIcon,
        children: [
            { title: 'New Sale', href: '/sales/create', icon: PlusCircleIcon },
            { title: 'Manage Sales', href: '/sales', icon: ListFilter },
            { title: 'Payments', href: '/sales-payments', icon: DollarSignIcon },
            { title: 'Receipts', href: '/receipts', icon: FileTextIcon },
        ],
    },

    {
        title: 'Stocks',
        icon: ListPlusIcon,
        children: [
            { title: 'Restock', href: '/stocks/create', icon: PlusCircleIcon },
            { title: 'Manage Stock', href: '/stocks', icon: Settings2Icon },
            { title: 'Stock Categories', href: '/medicine-categories', icon: ListCollapse },
            { title: 'Stock Conversion', href: '/stock-conversion', icon: Recycle },
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
        title: 'Prescriptions',
        icon: FileTextIcon,
        children: [
            { title: 'New Prescription', href: '/prescriptions/create', icon: PlusCircleIcon },
            { title: 'Manage Prescriptions', href: '/prescriptions', icon: ListFilter },
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
            {
                title: 'Doctors',
                icon: LandmarkIcon,
                children: [
                    { title: 'Add Doctor', href: '/doctors/create', icon: PlusCircleIcon },
                    { title: 'Manage Doctors', href: '/doctors', icon: ListFilter },
                ],
            },
            {
                title: 'Users',
                icon: UserSquare2Icon,
                children: [
                    { title: 'New User', href: '/users/create', icon: UserPlus },
                    { title: 'Manage Users', href: '/users', icon: UsersIcon },
                    { title: 'User Logs', href: '/users/activity-logs', icon: Users2 },
                    {
                        title: 'User Roles',
                        icon: UserPlus2Icon,
                        children: [
                            { title: 'New Role', href: '/roles/create', icon: PlusSquare },
                            {title: 'Manage Roles', href:'/roles', icon: ListPlusIcon}
                        ],
                    },
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
            {
                title: 'Bills',
                href: '/bills',
                icon: FileTextIcon,
                children: [
                    { title: 'New Bill', href: '/bills/create', icon: PlusCircleIcon },
                    { title: 'Manage Bills', href: '/bills', icon: ListFilter },
                    { title: 'Bill Payment', href: '/bill-payments/create', icon: ListFilter },
                    { title: 'Payment History', href: '/bill-payments', icon: ListFilter },
                ],
            },
            {
                title: 'Purchase Orders',
                href: '/purchases',
                icon: FileTextIcon,
                children: [
                    { title: 'New Purchase Order', href: '/purchases/create', icon: PlusCircleIcon },
                    { title: 'Manage Purchase Orders', href: '/purchases', icon: ListFilter },
                ],
            },
        ],
    },
    {
        title: 'Equipments',
        icon: HeartIcon,
        children: [
            { title: 'Add Equipment', href: '/equipments/create', icon: PlusCircleIcon },
            { title: 'Manage Equipments', href: '/equipments', icon: ListFilter },
        ],
    },
    {
        title: 'Appointments',
        icon: CalendarIcon,
        children: [
            { title: 'New Appointment', href: '/appointments/create', icon: PlusCircleIcon },
            { title: 'Manage Appointments', href: '/appointments', icon: ListFilter },
        ],
    },
    {
        title: 'Reports',
        icon: ChartBar,
        children: [
            { title: 'Sales Report', href: '/reports/sales', icon: FileImageIcon },
            {title: 'Medicine Report', href:'/reports/medicines', icon:FileImageIcon},
            {title:'Product Report', href:'/reports/products', icon:FileImageIcon},
            // { title: 'Customers Report', href: '/reports/sales', icon: FileImageIcon },
            // { title: 'Inventory Report', href: '/reports/sales', icon: FileImageIcon },
            // { title: 'Stock Report', href: '/reports/sales', icon: FileImageIcon },
            { title: 'Purchase Report', href: '/reports/sales', icon: FileImageIcon },
            { title: 'Profit & Loss Report', href: '/reports/sales', icon: FileImageIcon },
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
