<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-8">
            <h1 class="mb-6 text-2xl font-bold">Create Supplier</h1>
            <form @submit.prevent="submitForm" class="space-y-6">
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Supplier Name</label>
                    <Input
                        id="name"
                        type="text"
                        v-model="form.name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        :class="{ 'border-red-500': form.errors.name }"
                        required
                    />
                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
                </div>

                <div class="space-y-2">
                    <label for="contact_name" class="block text-sm font-medium text-gray-700">Contact Name</label>
                    <Input
                        id="contact_name"
                        type="text"
                        v-model="form.contact_name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        :class="{ 'border-red-500': form.errors.contact_name }"
                        required
                    />
                    <div v-if="form.errors.contact_name" class="mt-1 text-sm text-red-600">{{ form.errors.contact_name }}</div>
                </div>

                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <Input
                        id="email"
                        type="email"
                        v-model="form.email"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        :class="{ 'border-red-500': form.errors.email }"
                        required
                    />
                    <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</div>
                </div>

                <div class="space-y-2">
                    <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <Input
                        id="phone_number"
                        type="text"
                        v-model="form.phone_number"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        :class="{ 'border-red-500': form.errors.phone_number }"
                        required
                    />
                    <div v-if="form.errors.phone_number" class="mt-1 text-sm text-red-600">{{ form.errors.phone_number }}</div>
                </div>

                <div class="space-y-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <Input
                        id="address"
                        type="text"
                        v-model="form.address"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        :class="{ 'border-red-500': form.errors.address }"
                        required
                    />
                    <div v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</div>
                </div>

                <div class="flex space-x-4">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                        :disabled="form.processing"
                    >
                        Save Supplier
                    </button>
                    <Link
                        :href="route('suppliers.index')"
                        class="inline-flex items-center rounded-md bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400"
                    >
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { useForm,Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';

interface SupplierForm {
    name: string;
    contact_name: string;
    email: string;
    phone_number: string;
    address: string;
}

const form = useForm<SupplierForm>({
    name: '',
    contact_name: '',
    email: '',
    phone_number: '',
    address: '',
});

const submitForm = () => {
    form.post(route('suppliers.store'));
};
</script>
