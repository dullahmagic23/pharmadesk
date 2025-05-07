<template>
    <AppLayout>
        <div class="container  p-6">
            <h1 class="mb-6 text-2xl font-bold">Suppliers</h1>
            <Link :href="route('suppliers.create')" class="mb-6 inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
            >Add Supplier
            </Link>
            <table class="">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class=" text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Name</th>
                        <th class=" text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Email</th>
                        <th class=" text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Phone</th>
                        <th class=" text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="supplier in suppliers" :key="supplier.id">
                        <td class="px-6 py-4 whitespace-nowrap">{{ supplier.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ supplier.email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ supplier.phone_number }}</td>
                        <td class="space-x-2 px-6 py-4 whitespace-nowrap">
                            <Link
                                :href="route('suppliers.edit', supplier.id)"
                                class="inline-flex items-center rounded-md bg-yellow-500 px-3 py-1 text-white hover:bg-yellow-600"
                            >
                                Edit
                            </Link>
                            <button
                                @click="deleteSupplier(supplier.id)"
                                class="inline-flex items-center rounded-md bg-red-500 px-3 py-1 text-white hover:bg-red-600"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

interface Supplier {
    id: number;
    name: string;
    contact_name: string;
    email: string;
    phone_number: string;
    address: string;
}

defineProps<{
    suppliers: Supplier[];
    message?: string;
}>();

const deleteSupplier = (id: number) => {
    if (confirm('Are you sure you want to delete this supplier?')) {
        router.delete(route('suppliers.destroy', id));
    }
};
</script>
