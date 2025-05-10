<template>
    <AppLayout title="Users">
        <div class="container mx-auto p-6 bg-white rounded-xl shadow">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">All Users</h1>
                <Link href="/users/create">
                <Button>Add New User</Button>
                </Link>
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <input v-model="filters.search" type="text" placeholder="Search by name or email"
                    class="border p-2 rounded w-full" />
                <select v-model="filters.role" class="border p-2 rounded w-full">
                    <option value="">All Roles</option>
                    <option v-for="role in uniqueRoles" :key="role" :value="role">{{ role }}</option>
                </select>
                <select v-model="filters.status" class="border p-2 rounded w-full">
                    <option value="">All Statuses</option>
                    <option value="1">Active</option>
                    <option value="0">Disabled</option>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Role</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(user, index) in filteredUsers" :key="user.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ index + 1 }}</td>
                            <td class="px-4 py-2">{{ user.name }}</td>
                            <td class="px-4 py-2">{{ user.email }}</td>
                            <td class="px-4 py-2 capitalize">{{ user.roles[0]?.name || '—' }}</td>
                            <td class="px-4 py-2">
                                <span
                                    :class="user.is_active ? 'text-green-600 font-semibold' : 'text-red-500 font-semibold'">
                                    {{ user.is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <Link :href="`/users/${user.id}/edit`">
                                <Button size="sm" variant="secondary">Edit</Button>
                                </Link>
                                <Button size="sm" variant="destructive" @click="deleteUser(user.id)">Delete</Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const users = usePage().props.users

const filters = ref({
    search: '',
    role: '',
    status: '',
})

const uniqueRoles = computed(() => {
    const roles = users.map(user => user.roles[0]?.name).filter(Boolean)
    return [...new Set(roles)]
})

const filteredUsers = computed(() => {
    return users.filter(user => {
        const name = user.name?.toLowerCase() || ''
        const email = user.email?.toLowerCase() || ''

        const matchesSearch =
            name.includes(filters.value.search.toLowerCase()) ||
            email.includes(filters.value.search.toLowerCase())

        const matchesRole = filters.value.role ? user.roles[0]?.name === filters.value.role : true
        const matchesStatus = filters.value.status !== '' ? String(user.is_active) === filters.value.status : true

        return matchesSearch && matchesRole && matchesStatus
    })
})


function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(`/users/${id}`)
    }
}
</script>
