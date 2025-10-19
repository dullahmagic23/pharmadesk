<script setup>
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Package, Layers, Info, CheckCircle2, ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    product: Object,
});

const isEditMode = computed(() => props.product && Object.keys(props.product).length > 0);

const form = useForm({
    name: props.product?.name || '',
    category: props.product?.category || '',
    description: props.product?.description || '',
    unit: props.product?.unit || '',
});

const submit = () => {
    if (!isEditMode.value) {
        form.post(route('products.store'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset()
            }
        })
    } else {
        form.put(route('products.update', props.product.id), {
            onSuccess: () => {
                form.reset();
            },
            preserveScroll: true,
        });
    }
};

const breadcrumbs = [
    { title: 'Products', href: '/products' },
    { title: isEditMode.value ? 'Edit Product' : 'Create Product', href: isEditMode.value ? `/products/${props.product.id}/edit` : '/products/create' },
]
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
                <div class="container mx-auto px-4 py-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h1 class="text-4xl font-bold text-gray-900">
                                {{ isEditMode ? 'Edit Product' : 'Create Product' }}
                            </h1>
                            <p class="text-gray-500 text-sm mt-1">
                                {{ isEditMode ? 'Update product details' : 'Add a new product to your catalog' }}
                            </p>
                        </div>
                        <Link href="/products">
                            <Button variant="outline" class="rounded-lg gap-2 h-11 border-2">
                                <ArrowLeft class="w-5 h-5" />
                                <span class="hidden sm:inline">Back</span>
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-4 py-8">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Main Content -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Product Information Card -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="p-3 bg-blue-100 rounded-lg">
                                        <Package class="w-6 h-6 text-blue-600" />
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-gray-900">Product Information</h2>
                                        <p class="text-xs text-gray-500">Enter the product details</p>
                                    </div>
                                </div>

                                <div class="space-y-5">
                                    <!-- Product Name -->
                                    <div>
                                        <Label for="name" class="text-sm font-semibold text-gray-700 block mb-2">
                                            Product Name *
                                        </Label>
                                        <Input
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            placeholder="e.g., Acetaminophen Tablet"
                                            class="h-10 rounded-lg border-gray-200"
                                        />
                                        <InputError :message="form.errors.name" class="mt-2" />
                                    </div>

                                    <!-- Category -->
                                    <div>
                                        <Label for="category" class="text-sm font-semibold text-gray-700 block mb-2">
                                            Category *
                                        </Label>
                                        <Input
                                            id="category"
                                            v-model="form.category"
                                            type="text"
                                            placeholder="e.g., Pain Relief"
                                            class="h-10 rounded-lg border-gray-200"
                                        />
                                        <InputError :message="form.errors.category" class="mt-2" />
                                    </div>

                                    <!-- Unit -->
                                    <div>
                                        <Label for="unit" class="text-sm font-semibold text-gray-700 block mb-2">
                                            Unit *
                                        </Label>
                                        <Input
                                            id="unit"
                                            v-model="form.unit"
                                            type="text"
                                            placeholder="e.g., Tablet, Bottle, Box"
                                            class="h-10 rounded-lg border-gray-200"
                                        />
                                        <InputError :message="form.errors.unit" class="mt-2" />
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <Label for="description" class="text-sm font-semibold text-gray-700 block mb-2">
                                            Description
                                        </Label>
                                        <Textarea
                                            id="description"
                                            v-model="form.description"
                                            placeholder="Enter product description, usage instructions, side effects, etc."
                                            rows="5"
                                            class="rounded-lg border-gray-200 resize-none"
                                        />
                                        <InputError :message="form.errors.description" class="mt-2" />
                                        <p class="text-xs text-gray-500 mt-1">Optional - provide detailed information about the product</p>
                                    </div>
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
                                            <p class="font-medium text-gray-900 truncate">
                                                {{ form.name || '—' }}
                                            </p>
                                        </div>

                                        <div class="pb-3 border-b border-gray-200">
                                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Category</p>
                                            <p class="font-medium text-gray-900">
                                                {{ form.category || '—' }}
                                            </p>
                                        </div>

                                        <div class="pb-3 border-b border-gray-200">
                                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Unit</p>
                                            <p class="font-medium text-gray-900">
                                                {{ form.unit || '—' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Mode</p>
                                            <span :class="['inline-block px-2 py-1 rounded-full text-xs font-medium', isEditMode ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700']">
                                                {{ isEditMode ? 'Editing' : 'Creating' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Requirements Card -->
                                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-200 p-4">
                                    <p class="text-xs text-blue-700 font-semibold mb-3 flex items-center gap-2">
                                        <Info class="w-4 h-4" />
                                        Required fields
                                    </p>
                                    <ul class="space-y-2 text-xs text-blue-600">
                                        <li class="flex items-center gap-2">
                                            <CheckCircle2 class="w-4 h-4 flex-shrink-0" />
                                            Product name
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <CheckCircle2 class="w-4 h-4 flex-shrink-0" />
                                            Category
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <CheckCircle2 class="w-4 h-4 flex-shrink-0" />
                                            Unit
                                        </li>
                                    </ul>
                                </div>

                                <!-- Action Buttons -->
                                <div class="space-y-2">
                                    <Button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="w-full h-12 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 rounded-lg gap-2 text-base font-semibold transition-all"
                                    >
                                        <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                        </svg>
                                        {{ form.processing ? (isEditMode ? 'Updating...' : 'Creating...') : (isEditMode ? 'Update Product' : 'Create Product') }}
                                    </Button>
                                    <Link href="/products" class="block">
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
