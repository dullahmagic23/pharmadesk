<script lang="ts" setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue';
import { Label } from '@/components/ui/label';
import {Input} from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';


const props = defineProps({
    customer: Object,
})


const form = useForm({
    name: props.customer.name,
    phone:props.customer.phone,
    email:props.customer.email,
    address: props.customer.address
})


const submit = () => {
    form.put(route('customers.update',props.customer.id))
}
</script>

<template>
    <AppLayout>
        <div class="controller p-6">
            <h1 class="text-2xl font-bold mb-4">Create Customer</h1>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <Label for="name">Name</Label>
                    <Input v-model="form.name" class="Input" />
                    <div v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</div>
                </div>

                <div>
                    <Label for="phone">Phone</Label>
                    <Input v-model="form.phone" class="Input" />
                </div>

                <div>
                    <Label for="email">Email</Label>
                    <Input v-model="form.email" class="Input" />
                </div>

                <div>
                    <Label for="address">Address</Label>
                    <Textarea v-model="form.address" class="Input"></Textarea>
                </div>

                <Button type="submit" class="btn btn-primary">Save</Button>
            </form>
        </div>
    </AppLayout>
</template>
