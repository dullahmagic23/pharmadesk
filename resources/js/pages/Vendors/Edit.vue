<template>
    <AppLayout title="Add Vendor">
        <div class="container p-6">
            <div class="p-4">
                <h1 class="text-2xl font-bold">Add Vendor</h1>
            </div>

            <form @submit.prevent="submit" class="bg-white p-6 rounded-lg shadow space-y-6">
                <div>
                    <Label for="name">Vendor Name</Label>
                    <Input v-model="form.name" id="name" type="text" class="mt-1 block w-full" />
                    <InputError :message="form.errors.name" />
                </div>

                <div>
                    <Label for="contact_person">Contact Person</Label>
                    <Input v-model="form.contact_person" id="contact_person" type="text" class="mt-1 block w-full" />
                    <InputError :message="form.errors.contact_person" />
                </div>

                <div>
                    <Label for="phone">Phone</Label>
                    <Input v-model="form.phone" id="phone" type="text" class="mt-1 block w-full" />
                    <InputError :message="form.errors.phone" />
                </div>

                <div>
                    <Label for="email">Email</Label>
                    <Input v-model="form.email" id="email" type="email" class="mt-1 block w-full" />
                    <InputError :message="form.errors.email" />
                </div>

                <div>
                    <Label for="address">Address</Label>
                    <Textarea v-model="form.address" id="address" class="mt-1 block w-full" />
                    <InputError :message="form.errors.address" />
                </div>

                <div class="flex justify-start">
                    <Button type="submit" :disabled="form.processing">
                        <SaveIcon />
                        Update </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script lang="ts" setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Button } from '@/components/ui/button'
import InputError from '@/components/InputError.vue'
import {SaveIcon} from 'lucide-vue-next';


const  props = defineProps({
    vendor: Object,
})

const form = useForm({
    name: props.vendor.name,
    contact_person: props.vendor.contact_person,
    phone: props.vendor.phone,
    email: props.vendor.email,
    address: props.vendor.address,
})



function submit() {
    form.put(route('vendors.update',props.vendor.id))
}
</script>
