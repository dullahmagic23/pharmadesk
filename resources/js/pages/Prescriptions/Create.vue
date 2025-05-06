<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Create a prescription"/>

        <div class="container p-6">
            <form @submit.prevent="form.post(route('prescriptions.store'))" class="space-y-6">
                <!-- Patient -->
                <div>
                    <Label for="patient_id">Patient</Label>
                    <Select v-model="form.patient_id" class="w-full">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select a patient" class="w-full" />
                        </SelectTrigger>
                        <SelectContent class="w-full">
                            <SelectGroup class="w-full">
                                <SelectItem v-for="patient in patients" :key="patient.id" :value="patient.id" class="w-full">
                                    {{ patient.first_name+' '+patient.last_name }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.patient_id" />
                </div>

                <!-- Doctor (only if not a doctor) -->
                <div v-if="!isDoctor">
                    <Label for="doctor_id">Doctor</Label>
                    <Select v-model="form.doctor_id" class="w-full">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select a doctor" class="w-full" />
                        </SelectTrigger>
                        <SelectContent class="w-full">
                            <SelectGroup class="w-full">
                                <SelectItem v-for="doctor in doctors" :key="doctor.id" :value="doctor.id" class="w-full">
                                    {{ doctor.first_name+' '+doctor.last_name }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.doctor_id" />
                </div>

                <!-- Prescribed Date -->
                <div>
                    <Label for="prescribed_at">Prescribed Date</Label>
                    <Input type="date" v-model="form.prescribed_at" />
                    <InputError :message="form.errors.prescribed_at" />
                </div>

                <!-- Notes -->
                <div>
                    <Label for="notes">Notes</Label>
                    <Textarea v-model="form.notes" />
                    <InputError :message="form.errors.notes" />
                </div>

                <!-- Medicines -->
                <div>
                    <h3 class="font-medium text-lg mb-2">Medicines</h3>
                    <div v-for="(medicine, index) in form.medicines" :key="index" class="grid grid-cols-2 gap-4 items-center mb-4">
                        <div>
                            <Label :for="'medicine_' + index">Medicine</Label>
                            <Select v-model="medicine.id" class="w-full">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select medicine" class="w-full" />
                                </SelectTrigger>
                                <SelectContent class="w-full">
                                    <SelectGroup class="w-full">
                                        <SelectItem v-for="med in medicines" :key="med.id" :value="med.id" class="w-full">
                                            {{ med.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>

                        <div>
                            <Label :for="'dosage_' + index">Dosage</Label>
                            <Select v-model="medicine.dosage_id" class="w-full">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select dosage" class="w-full" />
                                </SelectTrigger>
                                <SelectContent class="w-full">
                                    <SelectGroup class="w-full">
                                        <SelectItem v-for="dosage in dosages" :key="dosage.id" :value="dosage.id" class="w-full">
                                            {{ dosage.frequency }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="col-span-2 text-right">
                            <button type="button" class="text-red-600 text-sm" @click="removeMedicine(index)">
                                Remove
                            </button>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary" @click="addMedicine">
                        + Add Medicine
                    </button>

                    <InputError :message="form.errors['medicines']" class="mt-2" />
                </div>

                <div class="pt-6">
                    <Button :disabled="form.processing">Save Prescription</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import {
    Select, SelectTrigger, SelectValue,
    SelectContent, SelectGroup, SelectItem
} from '@/components/ui/select'
import InputError from '@/components/InputError.vue';

const props = defineProps({
    patients: Array,
    doctors: Array,
    medicines: Array,
    dosages: Array,
    isDoctor: Boolean,
})

const form = useForm({
    patient_id: '',
    doctor_id: '',
    prescribed_at: new Date().toISOString().substr(0, 10),
    notes: '',
    medicines: []
})

const addMedicine = () => {
    form.medicines.push({ id: '', dosage_id: '' })
}

const removeMedicine = (index) => {
    form.medicines.splice(index, 1)
}

const breadcrumbs = [
    {title:'Prescriptions',href:'/prescriptions'},
    {title:'Create Prescription',href:'/prescriptions/create'},
]
</script>
