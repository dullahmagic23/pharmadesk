<template>
  <div class="p-4 rounded-2xl shadow flex items-center justify-between">
    <div>
      <h3>{{ title }}</h3>
      <p class="text-2xl font-bold">{{ value }}</p>
    </div>
    <component :is="iconComponent" class="w-8 h-8" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Stethoscope, Pill, Users } from 'lucide-vue-next'

const props = defineProps({
  title: String,
  value: [String, Number],
  icon: String,
})

const iconComponent = computed(() => {
  return {
    stethoscope: Stethoscope,
    pill: Pill,
    users: Users,
  }[props.icon] || Users
})
</script>
