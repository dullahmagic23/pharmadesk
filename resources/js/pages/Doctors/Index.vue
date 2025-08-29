<template>
    <AppLayout title="Doctors">

        <div class="container p-6 bg-white rounded shadow">
            <div class="flex justify-between mb-4">
                <h1 class="text-2xl font-semibold">Doctors</h1>
                <Link href="/doctors/create" class="btn btn-primary">Add Doctor</Link>
            </div>
            <table class="table-auto w-full">
                <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Specialization</th>
                    <th class="px-4 py-2">Phone</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="doctor in doctors" :key="doctor.id" class="border-b">
                    <td class="px-4 py-2">{{ doctor.first_name }} {{ doctor.last_name }}</td>
                    <td class="px-4 py-2">{{ doctor.specialization }}</td>
                    <td class="px-4 py-2">{{ doctor.phone }}</td>
                    <td class="px-4 py-2 space-x-2">
                        <Link :href="`/doctors/${doctor.id}`" class="text-blue-500">View</Link>
                        <Link :href="`/doctors/${doctor.id}/edit`" class="text-yellow-500">Edit</Link>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

defineProps({ doctors: Array })
</script>
