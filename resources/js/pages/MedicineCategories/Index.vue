<script setup>
import { ref, computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import FlashMessage from '@/components/FlashMessage.vue'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import InputError from '@/components/InputError.vue'
import { DeleteIcon } from 'lucide-vue-next'

const props = defineProps({
    categories: Array
})

const showModal = ref(false)
const search = ref('')

const form = useForm({
    name: '',
    description: ''
})

// Filter categories based on search input
const filteredCategories = computed(() => {
    if (!search.value.trim()) return props.categories
    return props.categories.filter(cat =>
        cat.name.toLowerCase().includes(search.value.toLowerCase()) ||
        (cat.description && cat.description.toLowerCase().includes(search.value.toLowerCase()))
    )
})

const submit = () => {
    form.post(route('medicine-categories.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
            showModal.value = false
        }
    })
}
</script>

<template>
    <AppLayout>
        <Head title="Medicine Categories" />
        <FlashMessage />

        <div class="p-6 space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h1 class="text-2xl font-bold">Medicine Categories</h1>
                <div class="flex flex-col sm:flex-row gap-2 items-center">
                    <Input v-model="search" placeholder="Search categories..." class="w-full sm:w-64" />
                    <Button @click="showModal = true">Add Category</Button>
                </div>
            </div>

            <div class="overflow-x-auto bg-white rounded shadow">
                <table class="table-auto w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-2 text-left">#</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Description</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(category, index) in filteredCategories"
                            :key="category.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="border border-gray-300 px-4 py-2">{{ index + 1 }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ category.name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ category.description }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <Button variant="destructive" class="flex items-center gap-1"
                                    @click="$inertia.delete(route('medicine-categories.destroy', category.id))">
                                    <DeleteIcon class="h-5 w-5" />
                                    Delete
                                </Button>
                            </td>
                        </tr>
                        <tr v-if="filteredCategories.length === 0">
                            <td colspan="4" class="text-center py-4 text-gray-500">No categories found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Modal -->
            <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded shadow max-w-md w-full">
                    <h2 class="text-lg font-semibold mb-4">Add New Category</h2>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Input v-model="form.name" type="text" placeholder="Category Name" />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div>
                            <Input v-model="form.description" type="text" placeholder="Description (optional)" />
                            <InputError :message="form.errors.description" />
                        </div>
                        <div class="flex justify-end gap-2">
                            <Button type="button" variant="ghost" @click="showModal = false">Cancel</Button>
                            <Button type="submit" :disabled="form.processing">Save</Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
