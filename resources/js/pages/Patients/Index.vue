<template>
    <AppLayout title="Patients">
        <Head title="Patients" />
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-semibold">Patients</h1>
                <Link href="/patients/create" class="btn btn-primary">Add Patient</Link>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white shadow rounded">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">Name</th>
                        <th class="p-2 text-left">Gender</th>
                        <th class="p-2 text-left">Phone</th>
                        <th class="p-2 text-left">Email</th>
                        <th class="p-2 text-left">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="patient in patients" :key="patient.id" class="border-t">
                        <td class="p-2">{{ patient.first_name }} {{ patient.last_name }}</td>
                        <td class="p-2 capitalize">{{ patient.gender }}</td>
                        <td class="p-2">{{ patient.phone }}</td>
                        <td class="p-2">{{ patient.email }}</td>
                        <td class="p-2 flex space-x-2">
                            <Link :href="`/patients/${patient.id}`" class="text-blue-600 hover:underline">View</Link>
                            <Link :href="`/patients/${patient.id}/edit`" class="text-yellow-600 hover:underline">Edit</Link>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script lang="ts" setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
    patients: Array
})
</script>
