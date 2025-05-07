<template>
    <AppLayout title="Vendors">
        <div class="flex items-center justify-between">
            <h1 class="p-6 text-2xl font-bold">Vendors</h1>
            <Link :href="route('vendors.create')" class="p-6">Add Vendor</Link>
        </div>

        <div class="overflow-x-auto p-6 rounded-lg bg-white shadow">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-gray-100 text-xs font-semibold text-gray-600 uppercase">
                    <tr>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Contact Person</th>
                        <th class="px-6 py-4">Phone</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="vendor in vendors.data" :key="vendor.id" class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ vendor.name }}</td>
                        <td class="px-6 py-4">{{ vendor.contact_person ?? '-' }}</td>
                        <td class="px-6 py-4">{{ vendor.phone ?? '-' }}</td>
                        <td class="px-6 py-4">{{ vendor.email ?? '-' }}</td>
                        <td class="space-x-2 px-6 py-4 text-right">
                            <Link :href="`/vendors/${vendor.id}/edit`" class="text-blue-600 hover:underline">Edit</Link>
                            <button @click="destroy(vendor.id)" class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination class="mt-4" :links="vendors.links"  :items-per-page="10"/>
    </AppLayout>
</template>

<script lang="ts" setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Pagination } from '@/components/ui/pagination';
import { Link } from '@inertiajs/vue3';

defineProps({
    vendors: Object,
});

function destroy(id) {
    if (confirm('Are you sure you want to delete this vendor?')) {
        router.delete(`/vendors/${id}`);
    }
}
</script>
