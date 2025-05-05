<script setup>
import { ref, onMounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

const props = defineProps({
    product: Object,
});

const form = useForm({
    name: props.product?.name || '',
    category: props.product?.category || '',
    description: props.product?.description || '',
    unit: props.product?.unit || '',
});

const submit = () => {
    if (Object.keys(props.product).length <=0 ) {
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
    {title:'Products',href:'/products'},
    {title:'Edit/Create Products',href:'/products/create'},
]
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-6 space-y-6">
            <h2 class="text-2xl font-semibold">Edit Product</h2>
            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <Label for="name">Product Name</Label>
                    <Input v-model="form.name" type="text" class="mt-1" />
                    <InputError :message="form.errors.name" class="mt-1" />
                </div>

                <div>
                    <Label for="category">Category</Label>
                    <Input v-model="form.category" type="text" class="mt-1" />
                    <InputError :message="form.errors.category" class="mt-1" />
                </div>

                <div>
                    <Label for="description">Description</Label>
                    <Textarea v-model="form.description" rows="4" />
                    <InputError :message="form.errors.description" class="mt-1" />
                </div>

                <div>
                    <Label for="unit">Unit</Label>
                    <Input v-model="form.unit" type="text" class="mt-1" />
                    <InputError :message="form.errors.unit" class="mt-1" />
                </div>

                <div class="pt-4">
                    <Button type="submit" :disabled="form.processing">Save</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
