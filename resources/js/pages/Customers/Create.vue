<script lang="ts" setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue';
import { Label } from '@/components/ui/label';
import {Input} from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';

const form = useForm({
    name: '',
    phone: '',
    email: '',
    address: '',
})

const submit = () => {
    form.post('/customers',{
        onSuccess: () => form.reset()
    })
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
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div>
                    <Label for="phone">Phone</Label>
                    <Input type="tel" v-model="form.phone" class="Input" />
                    <InputError :message="form.errors.phone" class="mt-2" />
                </div>

                <div>
                    <Label for="email">Email</Label>
                    <Input type="email" v-model="form.email" class="Input" />
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <div>
                    <Label for="address">Address</Label>
                    <Textarea v-model="form.address" class="Input"></Textarea>
                    <InputError :message="form.errors.address" class="mt-2" />
                </div>

                <Button type="submit" class="btn btn-primary">Save</Button>
            </form>
        </div>
    </AppLayout>
</template>
