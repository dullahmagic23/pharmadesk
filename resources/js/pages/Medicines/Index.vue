<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const {medicines} = usePage().props

// Filter criteria
const filterQuery = ref('')
const filterCategory = ref('')
const filterBrand = ref('')

// Computed filtered medicines based on filterQuery, filterCategory, and filterBrand
const filteredMedicines = computed(() => {
    return medicines.filter(medicine => {
        const matchesName = medicine.name.toLowerCase().includes(filterQuery.value.toLowerCase())
        const matchesCategory = filterCategory.value ? medicine.category?.name.toLowerCase().includes(filterCategory.value.toLowerCase()) : true
        const matchesBrand = filterBrand.value ? medicine.brand.toLowerCase().includes(filterBrand.value.toLowerCase()) : true
        return matchesName && matchesCategory && matchesBrand
    })
})

const breadcrumbs = [
    {title:'Medicines', url: '/medicines'},
]
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="container mx-auto p-6">
            <h2 class="text-2xl font-semibold mb-4">Medicines</h2>

            <Link href="/medicines/create">
            <Button class="mb-4">Add New Medicine</Button>
            </Link>

            <div class="flex mb-4 space-x-4">
                <div class="w-1/3">
                    <input v-model="filterQuery" type="text" placeholder="Search by name"
                        class="w-full p-2 border border-gray-300 rounded-md" />
                </div>
                <div class="w-1/3">
                    <select v-model="filterCategory" class="w-full p-2 border border-gray-300 rounded-md ml-2">
                        <option value="">Filter by Category</option>
                        <option
                            v-for="category in medicines.map(m => m.category?.name).filter((v, i, a) => a.indexOf(v) === i)"
                            :key="category" :value="category">
                            {{ category }}
                        </option>
                    </select>
                </div>
                <div class="w-1/3">
                    <input v-model="filterBrand" type="text" placeholder="Filter by brand"
                        class="w-full p-2 border border-gray-300 rounded-md ml-2" />
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Brand</th>
                            <th class="px-4 py-2 text-left">Category</th>
                            <th class="px-4 py-2 text-left">Units</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="medicine in filteredMedicines" :key="medicine.id">
                            <td class="px-4 py-2">{{ medicine.name }}</td>
                            <td class="px-4 py-2">{{ medicine.brand }}</td>
                            <td class="px-4 py-2">{{ medicine.category?.name }}</td>
                            <td class="px-4 py-2">
                                <ul>
                                    <li v-for="unit in medicine?.units" :key="unit.id">{{ unit?.unit_name }}</li>
                                </ul>
                            </td>
                            <td class="px-4 py-2">
                                <Link :href="`/medicines/${medicine.id}/edit`">
                                <Button>Edit</Button>
                                </Link>
                                <Link :href="`/medicines/${medicine.id}`">
                                <Button class="ml-2">View</Button>
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    border: 1px solid #e0e0e0;
    text-align: left;
}

th {
    background-color: #f7fafc;
}
</style>
