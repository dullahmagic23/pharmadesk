<script lang="ts" setup>
import AppLayout from '@/layouts/AppLayout.vue';

defineProps({ customer: Object })
</script>

<template>
    <AppLayout>
        <div class="container p-6">
            <h1 class="text-2xl font-bold mb-4">Customer Details</h1>
            <p><strong>Name:</strong> {{ customer.name }}</p>
            <p><strong>Phone:</strong> {{ customer.phone }}</p>
            <p><strong>Email:</strong> {{ customer.email }}</p>
            <p><strong>Address:</strong> {{ customer.address }}</p>
        </div>
    </AppLayout>
</template>
