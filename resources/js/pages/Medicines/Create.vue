<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'
import SelectLabel from '@/components/ui/select/SelectLabel.vue'
import InputError from '@/components/InputError.vue'
import { ChevronRight, Pill, Package, Layers, FileText, CheckCircle2 } from 'lucide-vue-next'

const props = defineProps({
    categories: Array,
    units: Array
})

const form = useForm({
    name: '',
    brand: '',
    medicine_category_id: '',
    description: '',
    units: []
})

const isSelected = (unitId) => form.units.some(u => u.medicine_unit_id === unitId)

const toggleUnit = (unitId) => {
    if (isSelected(unitId)) {
        form.units = form.units.filter(u => u.medicine_unit_id !== unitId)
    } else {
        form.units.push({
            medicine_unit_id: unitId,
        })
    }
}

const submit = () => {
    form.post(route('medicines.store'), {
        onFinish: () => {
            form.reset()
        },
        preserveScroll: true,
        onError: (errors) => {
            console.error(errors)
        },
    })
}

const breadcrumbs = [
    { title: 'Medicines', href: '/medicines' },
    { title: 'Create Medicine', href: '/medicines/create' },
]

const selectedUnitsCount = () => form.units.length
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Register new Medicine" />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
                <div class="container mx-auto px-4 py-6">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900">Register Medicine</h1>
                        <p class="text-gray-500 text-sm mt-1">Add a new medicine to your inventory</p>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-4 py-8">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Main Content -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Basic Information Card -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="p-3 bg-blue-100 rounded-lg">
                                        <Pill class="w-6 h-6 text-blue-600" />
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-gray-900">Basic Information</h2>
                                        <p class="text-xs text-gray-500">Enter the medicine details</p>
                                    </div>
                                </div>

                                <div class="space-y-5">
                                    <!-- Medicine Name -->
                                    <div>
                                        <Label for="name" class="text-sm font-semibold text-gray-700 block mb-2">
                                            Medicine Name *
                                        </Label>
                                        <Input
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            placeholder="e.g., Paracetamol"
                                            class="h-10 rounded-lg border-gray-200"
                                        />
                                        <InputError :message="form.errors.name" class="mt-2" />
                                    </div>

                                    <!-- Brand -->
                                    <div>
                                        <Label for="brand" class="text-sm font-semibold text-gray-700 block mb-2">
                                            Brand Name *
                                        </Label>
                                        <Input
                                            id="brand"
                                            v-model="form.brand"
                                            type="text"
                                            placeholder="e.g., Calpol"
                                            class="h-10 rounded-lg border-gray-200"
                                        />
                                        <InputError :message="form.errors.brand" class="mt-2" />
                                    </div>

                                    <!-- Category -->
                                    <div>
                                        <Label for="medicine_category_id" class="text-sm font-semibold text-gray-700 block mb-2">
                                            Category *
                                        </Label>
                                        <Select v-model="form.medicine_category_id">
                                            <SelectTrigger class="h-10 rounded-lg border-gray-200">
                                                <SelectValue placeholder="Select a category" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectLabel>Available Categories</SelectLabel>
                                                <SelectItem v-for="category in categories" :key="category.id" :value="category.id">
                                                    {{ category.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <InputError :message="form.errors.medicine_category_id" class="mt-2" />
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <Label for="description" class="text-sm font-semibold text-gray-700 block mb-2">
                                            Description
                                        </Label>
                                        <Textarea
                                            id="description"
                                            v-model="form.description"
                                            placeholder="Enter medicine description, usage, side effects, etc."
                                            rows="4"
                                            class="rounded-lg border-gray-200 resize-none"
                                        />
                                        <InputError :message="form.errors.description" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Units Selection Card -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="p-3 bg-purple-100 rounded-lg">
                                        <Package class="w-6 h-6 text-purple-600" />
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-gray-900">Units</h2>
                                        <p class="text-xs text-gray-500">Select available measurement units</p>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        <label
                                            v-for="unit in units"
                                            :key="unit.id"
                                            :class="[
                        'flex items-center gap-3 p-3 rounded-lg border-2 cursor-pointer transition-all',
                        isSelected(unit.id)
                          ? 'border-blue-500 bg-blue-50'
                          : 'border-gray-200 bg-gray-50 hover:border-gray-300'
                      ]"
                                        >
                                            <input
                                                type="checkbox"
                                                :value="unit.id"
                                                :checked="isSelected(unit.id)"
                                                @change="toggleUnit(unit.id)"
                                                class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 cursor-pointer"
                                            />
                                            <span :class="['text-sm font-medium', isSelected(unit.id) ? 'text-blue-700' : 'text-gray-700']">
                        {{ unit.unit_name }}
                      </span>
                                        </label>
                                    </div>
                                    <InputError :message="form.errors.units" class="mt-3" />
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar: Summary & Submit -->
                        <div class="lg:col-span-1">
                            <div class="sticky top-24 space-y-4">
                                <!-- Summary Card -->
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                                        <Layers class="w-5 h-5 text-blue-600" />
                                        Summary
                                    </h3>

                                    <div class="space-y-3">
                                        <div class="pb-3 border-b border-gray-200">
                                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Name</p>
                                            <p class="font-medium text-gray-900 truncate">
                                                {{ form.name || '—' }}
                                            </p>
                                        </div>

                                        <div class="pb-3 border-b border-gray-200">
                                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Brand</p>
                                            <p class="font-medium text-gray-900 truncate">
                                                {{ form.brand || '—' }}
                                            </p>
                                        </div>

                                        <div class="pb-3 border-b border-gray-200">
                                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Category</p>
                                            <p class="font-medium text-gray-900">
                                                {{ categories.find(c => c.id == form.medicine_category_id)?.name || '—' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-2">Selected Units</p>
                                            <div class="flex flex-wrap gap-2">
                        <span
                            v-if="selectedUnitsCount() === 0"
                            class="text-sm text-gray-500 italic"
                        >
                          No units selected
                        </span>
                                                <span
                                                    v-for="unit in units.filter(u => isSelected(u.id))"
                                                    :key="unit.id"
                                                    class="inline-block px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-medium"
                                                >
                          {{ unit.unit_name }}
                        </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Info Card -->
                                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-200 p-4">
                                    <p class="text-xs text-blue-700 font-medium mb-2">Required fields</p>
                                    <ul class="space-y-1 text-xs text-blue-600">
                                        <li class="flex items-center gap-2">
                                            <CheckCircle2 class="w-4 h-4" />
                                            Medicine name
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <CheckCircle2 class="w-4 h-4" />
                                            Brand name
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <CheckCircle2 class="w-4 h-4" />
                                            Category
                                        </li>
                                    </ul>
                                </div>

                                <!-- Submit Button -->
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="w-full h-12 bg-blue-600 hover:bg-blue-700 rounded-lg gap-2 text-base font-semibold"
                                >
                                    <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                    </svg>
                                    {{ form.processing ? 'Saving...' : 'Register Medicine' }}
                                </Button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
