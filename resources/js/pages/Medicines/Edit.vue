<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import { Select, SelectTrigger, SelectValue, SelectContent, SelectGroup, SelectItem } from '@/components/ui/select'
import { Pill, Package, Layers, CheckCircle2, ArrowLeft } from 'lucide-vue-next'
import InputError from '@/components/InputError.vue'

const props = defineProps({
    medicine: Object,
    categories: Array,
    units: Array,
})

const form = useForm({
    name: props.medicine.name,
    brand: props.medicine.brand,
    medicine_category_id: props.medicine.category?.id ?? '',
    unit_ids: props.medicine.units?.map(unit => unit.id) ?? [],
})

// Track which units are selected
const isUnitSelected = (unitId) => form.unit_ids.includes(unitId)

// Toggle unit selection
const toggleUnit = (unitId) => {
    if (isUnitSelected(unitId)) {
        form.unit_ids = form.unit_ids.filter(id => id !== unitId)
    } else {
        form.unit_ids.push(unitId)
    }
}

// Compute selected units count
const selectedUnitsCount = computed(() => form.unit_ids.length)

// Compute if form has changes
const hasChanges = computed(() => {
    return form.name !== props.medicine.name ||
        form.brand !== props.medicine.brand ||
        form.medicine_category_id !== (props.medicine.category?.id ?? '') ||
        form.unit_ids.length !== (props.medicine.units?.length ?? 0) ||
        form.unit_ids.some(id => !props.medicine.units?.map(u => u.id).includes(id))
})

function updateMedicine() {
    form.put(`/medicines/${props.medicine.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        },
        onError: (errors) => {
            console.error(errors)
        },
    })
}

const breadcrumbs = [
    { title: 'Medicines', href: '/medicines' },
    { title: 'Edit Medicine', href: `/medicines/${props.medicine.id}/edit` },
]
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Edit Medicine" />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
                <div class="container mx-auto px-4 py-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h1 class="text-4xl font-bold text-gray-900">Edit Medicine</h1>
                            <p class="text-gray-500 text-sm mt-1">Update medicine details and units</p>
                        </div>
                        <Link href="/medicines">
                            <Button variant="outline" class="rounded-lg gap-2 h-11 border-2">
                                <ArrowLeft class="w-5 h-5" />
                                <span class="hidden sm:inline">Back</span>
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-4 py-8">
                <form @submit.prevent="updateMedicine">
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
                                        <p class="text-xs text-gray-500">Update medicine details</p>
                                    </div>
                                </div>

                                <div class="space-y-5">
                                    <!-- Medicine Name -->
                                    <div>
                                        <Label for="name" class="text-sm font-semibold text-gray-700 block mb-2">
                                            Medicine Name
                                        </Label>
                                        <Input
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            placeholder="Enter medicine name"
                                            class="h-10 rounded-lg border-gray-200"
                                        />
                                        <InputError :message="form.errors.name" class="mt-2" />
                                    </div>

                                    <!-- Brand -->
                                    <div>
                                        <Label for="brand" class="text-sm font-semibold text-gray-700 block mb-2">
                                            Brand Name
                                        </Label>
                                        <Input
                                            id="brand"
                                            v-model="form.brand"
                                            type="text"
                                            placeholder="Enter brand name"
                                            class="h-10 rounded-lg border-gray-200"
                                        />
                                        <InputError :message="form.errors.brand" class="mt-2" />
                                    </div>

                                    <!-- Category -->
                                    <div>
                                        <Label for="medicine_category_id" class="text-sm font-semibold text-gray-700 block mb-2">
                                            Category
                                        </Label>
                                        <Select v-model="form.medicine_category_id">
                                            <SelectTrigger class="h-10 rounded-lg border-gray-200">
                                                <SelectValue placeholder="Select a category" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectItem
                                                        v-for="category in categories"
                                                        :key="category.id"
                                                        :value="category.id"
                                                    >
                                                        {{ category.name }}
                                                    </SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                        <InputError :message="form.errors.medicine_category_id" class="mt-2" />
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
                        isUnitSelected(unit.id)
                          ? 'border-blue-500 bg-blue-50'
                          : 'border-gray-200 bg-gray-50 hover:border-gray-300'
                      ]"
                                        >
                                            <input
                                                type="checkbox"
                                                :value="unit.id"
                                                :checked="isUnitSelected(unit.id)"
                                                @change="toggleUnit(unit.id)"
                                                class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 cursor-pointer"
                                            />
                                            <span :class="['text-sm font-medium', isUnitSelected(unit.id) ? 'text-blue-700' : 'text-gray-700']">
                        {{ unit.unit_name }}
                      </span>
                                        </label>
                                    </div>
                                    <InputError :message="form.errors.unit_ids" class="mt-3" />
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar: Summary & Actions -->
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
                                            <p class="font-medium text-gray-900">
                                                {{ form.name || '—' }}
                                            </p>
                                        </div>

                                        <div class="pb-3 border-b border-gray-200">
                                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Brand</p>
                                            <p class="font-medium text-gray-900">
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
                            v-if="selectedUnitsCount === 0"
                            class="text-sm text-gray-500 italic"
                        >
                          No units selected
                        </span>
                                                <span
                                                    v-for="unit in units.filter(u => isUnitSelected(u.id))"
                                                    :key="unit.id"
                                                    class="inline-block px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-medium"
                                                >
                          {{ unit.unit_name }}
                        </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Change Status Card -->
                                <div v-if="!hasChanges" class="bg-green-50 rounded-xl border border-green-200 p-4">
                                    <p class="text-xs text-green-700 font-medium">No changes made</p>
                                </div>
                                <div v-else class="bg-blue-50 rounded-xl border border-blue-200 p-4">
                                    <p class="text-xs text-blue-700 font-medium flex items-center gap-2">
                                        <CheckCircle2 class="w-4 h-4" />
                                        You have unsaved changes
                                    </p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="space-y-2">
                                    <Button
                                        type="submit"
                                        :disabled="form.processing || !hasChanges"
                                        class="w-full h-12 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 rounded-lg gap-2 text-base font-semibold transition-all"
                                    >
                                        <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                        </svg>
                                        {{ form.processing ? 'Updating...' : 'Update Medicine' }}
                                    </Button>
                                    <Link href="/medicines" class="block">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            class="w-full h-10 rounded-lg border-2"
                                        >
                                            Cancel
                                        </Button>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
