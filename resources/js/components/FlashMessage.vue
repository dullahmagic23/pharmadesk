<script setup>
import { onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const visible = ref(false);
const message = ref('');

watch(() => page.props.flash?.success, (newMessage) => {
  if (newMessage) show(newMessage);
});

onMounted(() => {
  const msg = page.props.flash?.success;
  if (msg) show(msg);
});

function show(msg) {
  message.value = msg;
  visible.value = true;
  setTimeout(() => {
    visible.value = false;
    message.value = '';
  }, 4000);
}

</script>

<template>
  <div
    v-if="visible"
    class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded shadow z-50"
  >
    {{ message }}
  </div>
</template>
