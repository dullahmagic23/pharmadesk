<template>
    <AppLayout :title="`Patient: ${patient.first_name}`">
        <Head :title="`Patient: ${patient.first_name}`" />
        <div class="conatiner p-6">
            <h1 class="text-2xl font-semibold mb-4">Patient Details</h1>
            <div class="grid grid-cols-2 gap-4">
                <div><strong>Name:</strong> {{ patient.first_name }} {{ patient.last_name }}</div>
                <div><strong>Gender:</strong> {{ patient.gender }}</div>
                <div><strong>Date of Birth:</strong> {{ patient.date_of_birth }}</div>
                <div><strong>Phone:</strong> {{ patient.phone }}</div>
                <div><strong>Email:</strong> {{ patient.email }}</div>
                <div class="col-span-2"><strong>Address:</strong> {{ patient.address }}</div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'

defineProps({
    patient: Object,
})
</script>
