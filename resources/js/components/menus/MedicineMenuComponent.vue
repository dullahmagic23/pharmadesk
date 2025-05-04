<script setup lang="ts">
import {
  ListCheckIcon,
  PlusCircleIcon,
  ListFilter,
  ListCollapse,
  FileTextIcon,
  ChevronsUpDown,
} from 'lucide-vue-next';

import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
} from '@/components/ui/dropdown-menu';

import { SidebarMenuButton } from '@/components/ui/sidebar';
import { Link } from '@inertiajs/vue3';
import { useSidebar } from '@/components/ui/sidebar';

const { isMobile, state } = useSidebar();

const medicinesMenuItems = [
  { title: 'Add Medicine', href: '/medicines/create', icon: PlusCircleIcon },
  { title: 'Manage Medicines', href: '/medicines', icon: ListFilter },
  { title: 'Medicine Categories', href: '/medicine-categories', icon: ListCollapse },
  { title: 'Add Stock', href: '/medicine-stocks/create', icon: PlusCircleIcon },
  { title: 'View Stock History', href: '/medicine-stocks', icon: FileTextIcon },
];
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <SidebarMenuButton
        size="lg"
        class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
      >
        <ListCheckIcon />
        Medicines
        <ChevronsUpDown class="ml-auto size-4" />
      </SidebarMenuButton>
    </DropdownMenuTrigger>
    <DropdownMenuContent
      class="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg"
      :side="isMobile ? 'bottom' : state === 'collapsed' ? 'left' : 'bottom'"
      align="end"
      :side-offset="4"
    >
      <DropdownMenuGroup>
        <DropdownMenuItem
          v-for="item in medicinesMenuItems"
          :key="item.href"
          as-child
        >
          <Link class="flex items-center gap-2 w-full" :href="item.href" as="button">
            <component :is="item.icon" class="size-4" />
            {{ item.title }}
          </Link>
        </DropdownMenuItem>
      </DropdownMenuGroup>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
