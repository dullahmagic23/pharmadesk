<script setup>
import { onMounted, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const flashMessage = ref(null)
let timeoutId = null

const icons = {
    success: '✅',
    error: '❌',
    warning: '⚠️',
    info: 'ℹ️'
}

const colors = {
    success: 'bg-green-100 border-green-300 text-green-800',
    error: 'bg-red-100 border-red-300 text-red-800',
    warning: 'bg-yellow-100 border-yellow-300 text-yellow-800',
    info: 'bg-blue-100 border-blue-300 text-blue-800'
}

function showFlash(flash) {
    if (!flash) return
    for (const type of ['success', 'error', 'warning', 'info']) {
        if (flash[type]) {
            flashMessage.value = { type, msg: flash[type] }

            clearTimeout(timeoutId)
            timeoutId = setTimeout(() => {
                flashMessage.value = null
            }, 5000)
            break
        }
    }
}

function close() {
    clearTimeout(timeoutId)
    flashMessage.value = null
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
        enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-2"
    >
        <div
            v-if="flashMessage"
            :class="[
        'fixed top-6 left-1/2 -translate-x-1/2 px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 z-50 border',
        colors[flashMessage.type]
      ]"
        >
      <span class="text-xl">
        {{ icons[flashMessage.type] }}
      </span>
            <span class="font-medium">
        {{ flashMessage.msg }}
      </span>
            <button
                @click="close"
                class="ml-auto text-current opacity-70 hover:opacity-100 transition-opacity"
            >
                ✖
            </button>
        </div>
    </Transition>
</template>
