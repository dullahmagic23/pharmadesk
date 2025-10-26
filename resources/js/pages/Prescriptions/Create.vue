<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Create Prescription"/>

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900">Create Prescription</h1>
                <p class="text-slate-600 mt-1">Enter patient details and prescribed medicines</p>
            </div>

            <!-- Main Form -->
            <form @submit.prevent="form.post(route('prescriptions.store'))" class="max-w-4xl space-y-6">

                <!-- Patient & Doctor Section -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
                    <h2 class="text-lg font-semibold text-slate-900 mb-6 pb-4 border-b border-slate-200">Patient & Doctor Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Patient Selection -->
                        <div>
                            <Label for="patient_id" class="text-sm font-medium text-slate-700 mb-2 block">
                                <span class="text-red-500">*</span> Patient
                            </Label>
                            <Select v-model="form.patient_id">
                                <SelectTrigger class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                    <SelectValue placeholder="Select a patient" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem v-for="patient in patients" :key="patient.id" :value="patient.id">
                                            {{ patient.first_name }} {{ patient.last_name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.patient_id" class="mt-2" />
                        </div>

                        <!-- Doctor Selection (only if not a doctor) -->
                        <div v-if="!isDoctor">
                            <Label for="doctor_id" class="text-sm font-medium text-slate-700 mb-2 block">
                                <span class="text-red-500">*</span> Doctor
                            </Label>
                            <Select v-model="form.doctor_id">
                                <SelectTrigger class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                    <SelectValue placeholder="Select a doctor" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                                            {{ doctor.first_name }} {{ doctor.last_name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.doctor_id" class="mt-2" />
                        </div>

                        <!-- Prescribed Date -->
                        <div>
                            <Label for="prescribed_at" class="text-sm font-medium text-slate-700 mb-2 block">
                                <span class="text-red-500">*</span> Prescribed Date
                            </Label>
                            <Input
                                type="date"
                                v-model="form.prescribed_at"
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <InputError :message="form.errors.prescribed_at" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
                    <h2 class="text-lg font-semibold text-slate-900 mb-6 pb-4 border-b border-slate-200">Additional Notes</h2>

                    <div>
                        <Label for="notes" class="text-sm font-medium text-slate-700 mb-2 block">Notes</Label>
                        <Textarea
                            v-model="form.notes"
                            placeholder="Add any special instructions or notes about the prescription..."
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent min-h-24 resize-none"
                        />
                        <InputError :message="form.errors.notes" class="mt-2" />
                    </div>
                </div>

                <!-- Medicines Section -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200">
                        <h2 class="text-lg font-semibold text-slate-900">Prescribed Medicines</h2>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700">
                            {{ form.medicines.length }} medicine{{ form.medicines.length !== 1 ? 's' : '' }}
                        </span>
                    </div>

                    <!-- Medicine Items -->
                    <div v-if="form.medicines.length > 0" class="space-y-4 mb-6">
                        <div
                            v-for="(medicine, index) in form.medicines"
                            :key="index"
                            class="bg-slate-50 border border-slate-200 rounded-lg p-6 hover:shadow-md transition-shadow"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <!-- Medicine Selection -->
                                <div>
                                    <Label :for="'medicine_' + index" class="text-sm font-medium text-slate-700 mb-2 block">
                                        <span class="text-red-500">*</span> Medicine
                                    </Label>
                                    <Select v-model="medicine.id">
                                        <SelectTrigger class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                            <SelectValue placeholder="Select medicine" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem v-for="med in medicines" :key="med.id" :value="med.id">
                                                    {{ med.name }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <!-- Dosage Selection -->
                                <div>
                                    <Label :for="'dosage_' + index" class="text-sm font-medium text-slate-700 mb-2 block">
                                        <span class="text-red-500">*</span> Dosage
                                    </Label>
                                    <Select v-model="medicine.dosage_id">
                                        <SelectTrigger class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                            <SelectValue placeholder="Select dosage" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem v-for="dosage in dosages" :key="dosage.id" :value="dosage.id">
                                                    {{ dosage.frequency }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>

                            <!-- Remove Button -->
                            <div class="flex justify-end">
                                <button
                                    type="button"
                                    @click="removeMedicine(index)"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="form.medicines.length === 0" class="text-center py-12 px-4 bg-slate-50 border border-dashed border-slate-300 rounded-lg">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-slate-500 font-medium">No medicines added yet</p>
                        <p class="text-slate-400 text-sm mt-1">Add at least one medicine to create the prescription</p>
                    </div>

                    <!-- Add Medicine Button -->
                    <button
                        type="button"
                        @click="addMedicine"
                        class="w-full mt-6 inline-flex items-center justify-center gap-2 px-4 py-2 border-2 border-dashed border-slate-300 text-slate-700 hover:border-blue-400 hover:bg-blue-50 rounded-lg transition font-medium"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Medicine
                    </button>

                    <InputError :message="form.errors['medicines']" class="mt-4" />
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-6">
                    <button
                        type="button"
                        @click="$inertia.get(route('prescriptions.index'))"
                        class="flex-1 px-6 py-3 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition font-medium"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <svg v-if="!form.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span v-if="form.processing" class="inline-block animate-spin">⏳</span>
                        {{ form.processing ? 'Saving...' : 'Save Prescription' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import {
    Select, SelectTrigger, SelectValue,
    SelectContent, SelectGroup, SelectItem
} from '@/components/ui/select'
import InputError from '@/components/InputError.vue'

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
    { title: 'Prescriptions', href: '/prescriptions' },
    { title: 'Create Prescription', href: '/prescriptions/create' },
]
</script>
