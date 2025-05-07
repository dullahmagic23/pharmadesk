<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { PlusCircle } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button/index.js';

defineProps({
    equipment: Array,
});

const form = useForm({

})
</script>

<template>
    <Head title="Equipment" />
    <AppLayout>
        <div class="p-4 space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Equipment</h1>
                <Link href="/equipment/create" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <PlusCircle class="mr-2 w-4 h-4" />
                    Add Equipment
                </Link>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                    <tr>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Supplier</th>
                        <th class="px-4 py-2 text-left">Price</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in equipment" :key="item.id">
                        <td class="px-4 py-2">{{ item.name }}</td>
                        <td class="px-4 py-2">{{ item.supplier?.name ?? '—' }}</td>
                        <td class="px-4 py-2">{{ item.price }}</td>
                        <td class="flex px-4 py-2 space-x-4">
                            <Link :href="`/equipments/${item.id}/edit`" class="text-blue-600 hover:underline">Edit</Link>
                            <form @submit.prevent="form.delete(route('equipments.destroy', item.id))">
                                <Button type="submit" variant="destructive">Remove</Button>
                            </form>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
