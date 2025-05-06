<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mt-6 bg-white p-6 shadow rounded-lg">
            <h2 class="text-xl font-bold">Prescription Details</h2>
            <div class="flex justify-end space-x-3">
                <Link class="flex bg-gray-900 text-white p-2 rounded items-center" :href="route('prescriptions.index')">
                    <ArrowLeft class="mr-2 w-4 h-4"/>
                    Back</Link>
                <a class="bg-purple-600 text-white p-2 flex items-center rounded" :href="route('prescriptions.download', prescription.id)">
                    <PrinterIcon class="mr-2 w- h-4"/>
                    Print/Download Pdf
                </a>
            </div>
            <div class="mb-4">
                <h3 class="text-lg font-semibold">Patient:</h3>
                <p>{{ prescription.patient.first_name+' '+prescription.patient.last_name }}</p>
            </div>

            <div class="mb-4">
                <h3 class="text-lg font-semibold">Doctor:</h3>
                <p>{{ prescription.doctor.first_name+' '+prescription.doctor.last_name }}</p>
            </div>

            <div class="mb-4">
                <h3 class="text-lg font-semibold">Prescribed At:</h3>
                <p>{{ prescription.prescribed_at }}</p>
            </div>

            <div class="mb-4">
                <h3 class="text-lg font-semibold">Notes:</h3>
                <p v-if="prescription.notes">{{ prescription.notes }}</p>
                <p v-else class="text-gray-500 italic">No additional notes.</p>
            </div>

            <div class="mb-4">
                <h3 class="text-lg font-semibold mb-2">Medications:</h3>
                <ul v-if="prescription.medicines.length" class="list-disc list-inside">
                    <li v-for="medicine in prescription.medicines" :key="medicine.id">
                        {{ medicine.name }}
                        <span v-if="medicine.pivot.dosage" class="text-sm text-gray-600"> – {{ medicine.pivot.dosage.name }}</span>
                    </li>
                </ul>
                <p v-else class="text-gray-500 italic">No medicines listed.</p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import {ArrowLeft, PrinterIcon} from 'lucide-vue-next';

const props = defineProps({
    prescription: Object
})

const breadcrumbs = [
    {title:'Prescriptions', href:'/prescriptions'},
    {title:'Prescription Details', href:`/prescriptions/${props.prescription.id}`},
]
</script>
