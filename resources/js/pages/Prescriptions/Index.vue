<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold">Prescriptions</h2>
                <Link :href="route('prescriptions.create')" class="btn btn-primary">
                    + New Prescription
                </Link>
            </div>
        </template>

        <div class="mt-6 shadow rounded-lg p-4">
            <div class="mb-4 flex items-center gap-2">
                <Input
                    v-model="search"
                    type="text"
                    placeholder="Filter by patient or doctor"
                    class="input input-bordered w-full h-10"
                />
            </div>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-200">
                <tr class="border-b">
                    <th class="px-4 py-2 text-left">Patient</th>
                    <th class="px-4 py-2 text-left">Doctor</th>
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="prescription in filteredPrescriptions" :key="prescription.id" class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">{{ prescription.patient.first_name+' '+prescription.patient.last_name }}</td>
                    <td class="px-4 py-2">{{ prescription.doctor.first_name+' '+prescription.doctor.last_name }}</td>
                    <td class="px-4 py-2">{{ prescription.prescribed_at }}</td>
                    <td class="flex px-4 py-2 space-x-2">
                        <Link :href="route('prescriptions.show', prescription.id)" class="flex bg-blue-500 text-white rounded p-2">
                            <EyeIcon />
                            View
                        </Link>
                        <Button variant="destructive" @click="deletePrescription(prescription.id)">
                            <DeleteIcon />
                            Delete
                        </Button>
                    </td>
                </tr>
                <tr v-if="filteredPrescriptions.length === 0">
                    <td colspan="4" class="text-center py-4 text-gray-500">No prescriptions found.</td>
                </tr>
                </tbody>
            </table>

            <Pagination :links="prescriptions.links" class="mt-4" :items-per-page="15"/>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import {Pagination} from '@/components/ui/pagination'
import {DeleteIcon,EyeIcon} from 'lucide-vue-next';
import { Button } from '@/components/ui/button/index.js';
import { Input } from '@/components/ui/input';

const props = defineProps({
    prescriptions: Object
})

const search = ref('')

const filteredPrescriptions = computed(() => {
    if (!search.value.trim()) {
        return props.prescriptions.data;
    }
    const term = search.value.toLowerCase();
    return props.prescriptions.data.filter(prescription => {
        const patient = `${prescription.patient.first_name} ${prescription.patient.last_name}`.toLowerCase();
        const doctor = `${prescription.doctor.first_name} ${prescription.doctor.last_name}`.toLowerCase();
        return patient.includes(term) || doctor.includes(term);
    });
});

const deletePrescription = (id) => {
    if (confirm('Are you sure you want to delete this prescription?')) {
        router.delete(route('prescriptions.destroy', id))
    }
}
const breadcrumbs = [
    {title:'Prescriptions', href:'/prescriptions'},
]
</script>
