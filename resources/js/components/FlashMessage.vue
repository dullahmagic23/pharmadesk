<script setup>
import { onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const flashMessage = ref(null);
const timeout = ref(null);

const icons = {
    success: '✅',
    error: '❌',
    warning: '⚠️',
    info: 'ℹ️'
};

const colors = {
    success: 'bg-green-100 border-green-300 text-green-800',
    error: 'bg-red-100 border-red-300 text-red-800',
    warning: 'bg-yellow-100 border-yellow-300 text-yellow-800',
    info: 'bg-blue-100 border-blue-300 text-blue-800'
};

function show(message) {
    if (!message || (!message.success && !message.error && !message.warning && !message.info)) {
        return;
    }

    const type = Object.keys(message)[0];
    const msg = message[type];

    flashMessage.value = { type, msg };

    clearTimeout(timeout.value);
    timeout.value = setTimeout(() => {
        flashMessage.value = null;
    }, 5000);
}

function close() {
    clearTimeout(timeout.value);
    flashMessage.value = null;
}

watch(() => page.props.flash, (newFlash) => {
    if (newFlash) show(newFlash);
}, { deep: true });

onMounted(() => {
    if (page.props.flash) show(page.props.flash);
});
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform opacity-0 scale-95 -translate-x-1/2"
        enter-to-class="transform opacity-100 scale-100 -translate-x-1/2"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="transform opacity-100 scale-100 -translate-x-1/2"
        leave-to-class="transform opacity-0 scale-95 -translate-x-1/2"
    >
        <div
            v-if="flashMessage"
            :class="['fixed top-4 left-1/2 transform -translate-x-1/2 px-4 py-3 rounded-lg shadow-lg flex items-center space-x-3 z-50', colors[flashMessage.type]]"
            role="alert"
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
                aria-label="Close"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </Transition>
</template>
