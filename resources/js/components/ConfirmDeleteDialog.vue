<script setup>
import {
    AlertDialog,
    AlertDialogTrigger,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogFooter,
    AlertDialogTitle,
    AlertDialogDescription,
    AlertDialogCancel,
    AlertDialogAction,
} from "@/components/ui/alert-dialog";

import { Button } from "@/components/ui/button";
import { Trash2 as DeleteIcon, AlertTriangle } from "lucide-vue-next";

const props = defineProps({
    title: {
        type: String,
        default: "Are you absolutely sure?",
    },
    description: {
        type: String,
        default:
            "This action cannot be undone. This will permanently delete the item from our servers.",
    },
    onConfirm: {
        type: Function,
        required: true,
    },
    triggerVariant: {
        type: String,
        default: "ghost",
    },
    triggerSize: {
        type: String,
        default: "sm",
    },
    confirmLabel: {
        type: String,
        default: "Delete",
    },
    cancelLabel: {
        type: String,
        default: "Cancel",
    },
    buttonLabel: {
        type: String,
        default: "Delete"
    },
    buttonLabelTextColor: {
        type: String,
        default: "text-red-600 hover:text-red-700"
    },
    showIcon: {
        type: Boolean,
        default: true
    },
    iconOnly: {
        type: Boolean,
        default: false
    }
});
</script>

<template>
    <AlertDialog>
        <AlertDialogTrigger as-child>
            <Button
                :variant="triggerVariant"
                :size="triggerSize"
                :class="[
          buttonLabelTextColor,
          'transition-all duration-200 hover:scale-105 active:scale-95'
        ]"
            >
                <DeleteIcon v-if="showIcon" class="h-4 w-4" :class="{ 'mr-2': !iconOnly }" />
                <span v-if="!iconOnly">{{ buttonLabel }}</span>
            </Button>
        </AlertDialogTrigger>

        <AlertDialogContent class="max-w-md rounded-2xl shadow-2xl">
            <AlertDialogHeader class="space-y-4">
                <!-- Warning Icon -->
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-200 dark:bg-red-900/50">
                        <AlertTriangle class="h-7 w-7 text-red-600 dark:text-red-400" />
                    </div>
                </div>

                <div class="space-y-2 text-center">
                    <AlertDialogTitle class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ title }}
                    </AlertDialogTitle>
                    <AlertDialogDescription class="text-base leading-relaxed text-gray-600 dark:text-gray-400">
                        {{ description }}
                    </AlertDialogDescription>
                </div>
            </AlertDialogHeader>

            <AlertDialogFooter class="flex-col gap-2 sm:flex-row sm:gap-3">
                <AlertDialogCancel as-child>
                    <Button
                        variant="outline"
                        class="h-11 w-full rounded-xl font-semibold shadow-sm transition-all hover:scale-[1.02] sm:w-auto"
                    >
                        {{ cancelLabel }}
                    </Button>
                </AlertDialogCancel>

                <AlertDialogAction as-child>
                    <Button
                        variant="destructive"
                        @click="onConfirm"
                        class="h-11 w-full rounded-xl bg-gradient-to-r from-red-600 to-red-700 font-semibold shadow-lg transition-all hover:scale-[1.02] hover:from-red-700 hover:to-red-800 active:scale-95 sm:w-auto"
                    >
                        <DeleteIcon class="mr-2 h-4 w-4" />
                        {{ confirmLabel }}
                    </Button>
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
