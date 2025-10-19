<template>
  <div class="w-full">
    <Popover v-model:open="open" class="w-full">
      <PopoverTrigger as-child>
        <Button
          variant="outline"
          role="combobox"
          :aria-expanded="open"
          class="w-full justify-between"
        >
          <span class="truncate">
            {{ selectedLabel || placeholder }}
          </span>
          <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
        </Button>
      </PopoverTrigger>

      <!-- Make dropdown same width as trigger -->
      <PopoverContent class="w-[var(--radix-popover-trigger-width)] p-0">
        <Command class="rounded-md border w-full">
          <CommandInput placeholder="Search..." v-model="search" />
          <CommandList>
            <CommandEmpty>No results found.</CommandEmpty>
            <CommandGroup>
              <CommandItem
                v-for="item in filteredItems"
                :key="item.value"
                :value="item.label"
                @select="onSelect(item)"
                class="flex items-center justify-between"
              >
                <span class="truncate">{{ item.label }}</span>
                <CheckIcon
                  :class="
                    cn(
                      'ml-2 h-4 w-4',
                      item.value === modelValue
                        ? 'opacity-100'
                        : 'opacity-0'
                    )
                  "
                />
              </CommandItem>
            </CommandGroup>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { cn } from "@/lib/utils"; // Utility to join classes
import { Button } from "@/components/ui/button";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import {
  Command,
  CommandInput,
  CommandList,
  CommandEmpty,
  CommandItem,
  CommandGroup,
} from "@/components/ui/command";
import { CheckIcon, ChevronsUpDown } from "lucide-vue-next";

const props = defineProps<{
  items: { value: string; label: string; data?: any }[];
  modelValue: string | null;
  placeholder?: string;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: string | null): void;
  (e: "select-item", item: { value: string; label: string; data?: any }): void;
}>();

const open = ref(false);
const search = ref("");

const selectedItem = computed(() =>
  props.items.find((i) => i.value === props.modelValue) || null
);

const selectedLabel = computed(() => selectedItem.value?.label ?? "");

const filteredItems = computed(() =>
  props.items.filter((i) =>
    i.label.toLowerCase().includes(search.value.toLowerCase())
  )
);

function onSelect(item: { value: string; label: string; data?: any }) {
  emit("update:modelValue", item.value);
  emit("select-item", item);
  open.value = false;
  search.value = ""; // reset after selection
}
</script>
