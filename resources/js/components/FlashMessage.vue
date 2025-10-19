<script setup>
import { onMounted, ref, watch, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const flashMessage = ref(null)
const isVisible = ref(false)
let timeoutId = null

const typeConfig = {
    success: {
        icon: '✓',
        bgColor: 'bg-green-50 dark:bg-green-900/30',
        borderColor: 'border-green-200 dark:border-green-800',
        textColor: 'text-green-900 dark:text-green-100',
        dotColor: 'bg-green-500',
        progressColor: 'bg-green-500'
    },
    error: {
        icon: '✕',
        bgColor: 'bg-red-50 dark:bg-red-900/30',
        borderColor: 'border-red-200 dark:border-red-800',
        textColor: 'text-red-900 dark:text-red-100',
        dotColor: 'bg-red-500',
        progressColor: 'bg-red-500'
    },
    warning: {
        icon: '!',
        bgColor: 'bg-yellow-50 dark:bg-yellow-900/30',
        borderColor: 'border-yellow-200 dark:border-yellow-800',
        textColor: 'text-yellow-900 dark:text-yellow-100',
        dotColor: 'bg-yellow-500',
        progressColor: 'bg-yellow-500'
    },
    info: {
        icon: 'i',
        bgColor: 'bg-blue-50 dark:bg-blue-900/30',
        borderColor: 'border-blue-200 dark:border-blue-800',
        textColor: 'text-blue-900 dark:text-blue-100',
        dotColor: 'bg-blue-500',
        progressColor: 'bg-blue-500'
    }
}

const currentConfig = computed(() =>
    flashMessage.value ? typeConfig[flashMessage.value.type] : null
)

function showFlash(flash) {
    if (!flash) return

    for (const type of ['success', 'error', 'warning', 'info']) {
        if (flash[type]) {
            flashMessage.value = { type, msg: flash[type] }
            isVisible.value = true

            clearTimeout(timeoutId)
            timeoutId = setTimeout(() => {
                isVisible.value = false
                setTimeout(() => {
                    flashMessage.value = null
                }, 300)
            }, 5000)
            break
        }
    }
}

function close() {
    clearTimeout(timeoutId)
    isVisible.value = false
    setTimeout(() => {
        flashMessage.value = null
    }, 300)
}

// Watch for changes
watch(
    () => page.props.flash,
    (newFlash) => {
        if (newFlash && Object.keys(newFlash).length > 0) {
            showFlash(newFlash)
        }
    },
    { immediate: true, deep: true }
)

onMounted(() => {
    if (page.props.flash && Object.keys(page.props.flash).length > 0) {
        showFlash(page.props.flash)
    }
})
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-4 scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-4 scale-95"
    >
        <div
            v-if="flashMessage && isVisible"
            :class="[
                'fixed top-6 left-1/2 -translate-x-1/2 z-50',
                'max-w-sm w-full mx-4'
            ]"
        >
            <!-- Toast Container -->
            <div
                :class="[
                    'relative overflow-hidden rounded-xl shadow-lg border backdrop-blur-sm',
                    currentConfig.bgColor,
                    currentConfig.borderColor
                ]"
            >
                <!-- Content -->
                <div class="flex items-start gap-3 p-4">
                    <!-- Icon Dot -->
                    <div
                        :class="[
                            'flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-bold text-white text-sm',
                            currentConfig.dotColor
                        ]"
                    >
                        {{ currentConfig.icon }}
                    </div>

                    <!-- Message -->
                    <div class="flex-1 min-w-0 pt-0.5">
                        <p :class="['font-medium leading-snug', currentConfig.textColor]">
                            {{ flashMessage.msg }}
                        </p>
                    </div>

                    <!-- Close Button -->
                    <button
                        @click="close"
                        :class="[
                            'flex-shrink-0 ml-2 p-1.5 rounded-lg transition-colors',
                            'hover:bg-black/10 dark:hover:bg-white/10',
                            currentConfig.textColor
                        ]"
                        aria-label="Close notification"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <!-- Progress Bar -->
                <div
                    :class="[
                        'absolute bottom-0 left-0 h-1 transition-all duration-200 ease-linear',
                        currentConfig.progressColor
                    ]"
                    :style="{
                        animation: isVisible ? 'shrink 5s linear forwards' : 'none'
                    }"
                />
            </div>
        </div>
    </Transition>
</template>

<style scoped>
@keyframes shrink {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}
</style>
