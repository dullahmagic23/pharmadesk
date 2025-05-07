<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
} from '@/components/ui/dropdown-menu';
import { Link } from '@inertiajs/vue3';
import { ChevronRightIcon } from 'lucide-vue-next';

defineProps<{
    item: {
        title: string;
        href?: string;
        icon?: any;
        children?: Array<any>;
    };
}>();

defineOptions({
    name: 'SidebarDropdownMenuItem',
});
</script>

<template>
    <!-- If the item has children, render it as a parent with a submenu -->
    <DropdownMenu v-if="item.children && item.children.length">
        <DropdownMenuTrigger as-child>
            <div
                class="relative flex w-full cursor-pointer select-none items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground data-[state=open]:bg-accent data-[state=open]:text-accent-foreground"
            >
                <component :is="item.icon" class="size-4" v-if="item.icon" />
                <span class="flex-1 text-left">{{ item.title }}</span>
                <ChevronRightIcon class="ml-auto size-4 text-muted-foreground" />
            </div>
        </DropdownMenuTrigger>

        <!-- Submenu content opens to the right -->
        <DropdownMenuContent
            side="right"
            align="start"
            :side-offset="6"
            class="min-w-[200px] rounded-md border p-1 shadow-md z-50"
        >
            <DropdownMenuGroup>
                <SidebarDropdownMenuItem
                    v-for="childItem in item.children"
                    :key="childItem.href || childItem.title"
                    :item="childItem"
                />
            </DropdownMenuGroup>
        </DropdownMenuContent>
    </DropdownMenu>

    <!-- If no children, render as a normal menu item -->
    <DropdownMenuItem v-else as-child>
        <Link class="flex w-full items-center gap-2 px-2 py-1.5 text-sm" :href="item.href!" as="button">
            <component :is="item.icon" class="size-4" v-if="item.icon" />
            {{ item.title }}
        </Link>
    </DropdownMenuItem>
</template>
