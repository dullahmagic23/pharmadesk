<script setup lang="ts">
import {
  ListCheckIcon,
  PlusCircleIcon,
  ListFilter,
  ListCollapse,
  FileTextIcon,
  FileIcon,
  LandmarkIcon,
  UsersRound,
  ChartBar,
  CalendarIcon,
  ChevronsUpDown,
  StoreIcon,
  ListPlusIcon,
  Settings2Icon,
} from 'lucide-vue-next';

import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuSubContent,
} from '@/components/ui/dropdown-menu';

import { SidebarMenuButton } from '@/components/ui/sidebar';
import { Link } from '@inertiajs/vue3';
import { useSidebar } from '@/components/ui/sidebar';

const { isMobile, state } = useSidebar();

const medicalItems = [
  {
    title: 'Stocks',
    icon: ListPlusIcon,
    children: [
      { title: 'Add Items', href: '/stocks/create', icon: PlusCircleIcon },
      { title: 'Medicine Stock', href: '/medicines', icon: PlusCircleIcon },
      { title: 'Products Stock', href: '/medicine-categories', icon: Settings2Icon },
      { title: 'Stock History', href: '/medicine-categories', icon: ListPlusIcon },
      { title: 'Stock Managent', href: '/medicine-categories', icon: ListCollapse },
      { title: 'Stock Reports', href: '/medicine-categories', icon: ListCollapse },
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
    ]
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
    title: 'Appointments',
    icon: CalendarIcon,
    children: [
      { title: 'New Appointment', href: '/appointments/create', icon: PlusCircleIcon },
      { title: 'Manage Appointments', href: '/appointments', icon: ListFilter },
    ],
  },
  {
    title: 'Inventory',
    icon: ListCheckIcon,
    children: [
      { title: 'Add Item', href: '/pharmacy-inventory/create', icon: PlusCircleIcon },
      { title: 'Manage Inventory', href: '/pharmacy-inventory', icon: ListFilter },
    ],
  },
  {
    title: 'Vendors & Purchases',
    icon: FileIcon,
    children: [
      { title: 'Add Vendor', href: '/vendors/create', icon: PlusCircleIcon },
      { title: 'Manage Vendors', href: '/vendors', icon: ListFilter },
      { title: 'Create Purchase', href: '/purchases/create', icon: PlusCircleIcon },
      { title: 'Manage Purchases', href: '/purchases', icon: ListFilter },
    ],
  },
  {
    title: 'Reports',
    icon: ChartBar,
    children: [
      { title: 'View Reports', href: '/reports', icon: FileTextIcon },
    ],
  },
];
</script>

<template>
  <div class="space-y-2">
    <DropdownMenu v-for="item in medicalItems" :key="item.title">
      <DropdownMenuTrigger as-child>
        <SidebarMenuButton size="lg"
          class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground">
          <component :is="item.icon" />
          {{ item.title }}
          <ChevronsUpDown class="ml-auto size-4" />
        </SidebarMenuButton>
      </DropdownMenuTrigger>
      <DropdownMenuContent class="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg"
        :side="isMobile ? 'bottom' : state === 'collapsed' ? 'left' : 'bottom'" align="end" :side-offset="4">
        <DropdownMenuGroup>
          <DropdownMenuItem v-for="child in item.children" :key="child.href" as-child>
            <Link class="flex items-center gap-2 w-full" :href="child.href" as="button">
            <component :is="child.icon" class="size-4" v-if="child.icon" />
            {{ child.title }}
            </Link>
          </DropdownMenuItem>
        </DropdownMenuGroup>
      </DropdownMenuContent>
    </DropdownMenu>
  </div>
</template>
