<template>
    <AppLayout :title="`${doctor.first_name} ${doctor.last_name}`">
       <div class="container p-6">
           <h1 class="text-2xl font-semibold mb-4">{{ doctor.first_name }} {{ doctor.last_name }}</h1>
           <div class="space-y-2 bg-white p-4 rounded shadow">
               <p><strong>Email:</strong> {{ doctor.email }}</p>
               <p><strong>Phone:</strong> {{ doctor.phone }}</p>
               <p><strong>Gender:</strong> {{ doctor.gender }}</p>
               <p><strong>DOB:</strong> {{ doctor.date_of_birth }}</p>
               <p><strong>Specialization:</strong> {{ doctor.specialization }}</p>
               <p><strong>Address:</strong> {{ doctor.address }}</p>
           </div>
       </div>
    </AppLayout>
</template>

<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({ doctor: Object })
</script>
