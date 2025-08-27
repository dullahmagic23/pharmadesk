<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';

const form = useForm({
    name: '',
});

const breadcrumbs = [
    {title:'Dashboard', href:'/'},
    {title:'Roles', href:'/roles'},
    {title:'Create Roles', href:'/roles/create'}
]
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Create User Roles" />

        <div class="max-w-xl mx-auto mt-10 shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-6 text-gray-800">Create a New Role</h2>

            <form @submit.prevent="form.post(route('roles.store'))" class="space-y-5">
                <!-- Role Name -->
                <div>
                    <Label for="name" class="block text-sm font-medium text-gray-700">Role Name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="e.g., Admin"
                    />
                    <InputError :message="form.errors.name" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <Button
                        type="submit"
                        variant="default"
                        :disabled="form.processing"
                        class="w-full"
                    >
                        {{ form.processing ? 'Saving...' : 'Save Role' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Optional additional styles can go here */
</style>
