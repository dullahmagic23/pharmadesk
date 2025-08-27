<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {useForm} from '@inertiajs/vue3';

defineProps<{
    roles: Array<{
        id: number;
        name: string;
        created_at: string;
    }>
}>();

const form = useForm({
    form_id: null,
})

function deleteRole(id: any) {
    form.form_id = id
    if (confirm('Are you sure you want to delete this role?')) {
        form.delete(route('roles.destroy', id));
    }
}

const breadcrumbs = [
    {title:'Dashboard', href:'/'},
    {title:'Roles', href:'/roles'}
]
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Roles" />

        <div class="max-w-5xl mx-auto mt-10 p-6 bg-white shadow rounded-lg">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Roles</h1>
                <Link :href="route('roles.create')">
                    <Button variant="default">Create Role</Button>
                </Link>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(role, index) in roles" :key="role.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ role.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ new Date(role.created_at).toLocaleDateString() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <Link :href="route('roles.edit', role.id)">
                                <Button variant="secondary" size="sm">Edit</Button>
                            </Link>
                            <Button variant="destructive" size="sm" @click="deleteRole(role.id)">Delete</Button>
                        </td>
                    </tr>
                    <tr v-if="roles.length === 0">
                        <td colspan="4" class="text-center py-6 text-gray-500">No roles found.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Optional styles */
</style>
