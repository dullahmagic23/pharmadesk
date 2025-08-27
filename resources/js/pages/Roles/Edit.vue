<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    role: {
        id: number;
        name: string;
    };
}>();

const form = useForm({
    name: props.role.name,
});

const breadcrumbs = [
    {title:'Dashboard', href:'/'},
    {title:'Roles', href:'/roles'},
    {title:'Edit Roles', href:`/roles/${props.role.id}/edit`}
]
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Edit Role" />

        <div class="max-w-md mx-auto mt-10 bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-6 text-gray-800">Edit Role</h2>

            <form @submit.prevent="form.put(route('roles.update', props.role.id))" class="space-y-5">
                <!-- Role Name -->
                <div>
                    <Label for="name" class="block text-sm font-medium text-gray-700">Role Name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="e.g., Manager"
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
                        {{ form.processing ? 'Saving...' : 'Update Role' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Optional scoped styles */
</style>
