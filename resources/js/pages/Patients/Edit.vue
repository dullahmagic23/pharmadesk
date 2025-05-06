<template>
    <AppLayout title="Edit Patient">
        <Head title="Edit Patient" />
        <div class="container p-6">
            <h1 class="text-2xl font-semibold mb-4">Edit Patient</h1>
            <form @submit.prevent="form.put(`/patients/${patient.id}`)">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <Label for="first_name">First Name</Label>
                        <Input v-model="form.first_name" id="first_name" />
                        <p v-if="form.errors.first_name" class="text-sm text-red-500">{{ form.errors.first_name }}</p>
                    </div>

                    <div>
                        <Label for="last_name">Last Name</Label>
                        <Input v-model="form.last_name" id="last_name" />
                        <p v-if="form.errors.last_name" class="text-sm text-red-500">{{ form.errors.last_name }}</p>
                    </div>

                    <div>
                        <Label for="gender">Gender</Label>
                        <Select v-model="form.gender">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select gender" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectLabel>Genders</SelectLabel>
                                <SelectItem value="male">Male</SelectItem>
                                <SelectItem value="female">Female</SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.gender" class="text-sm text-red-500">{{ form.errors.gender }}</p>
                    </div>

                    <div>
                        <Label for="date_of_birth">Date of Birth</Label>
                        <Input type="date" v-model="form.date_of_birth" id="date_of_birth" />
                    </div>

                    <div>
                        <Label for="phone">Phone</Label>
                        <Input v-model="form.phone" id="phone" />
                    </div>

                    <div>
                        <Label for="email">Email</Label>
                        <Input v-model="form.email" id="email" />
                    </div>

                    <div class="col-span-2">
                        <Label for="address">Address</Label>
                        <Textarea v-model="form.address" id="address" />
                    </div>
                </div>

                <div class="mt-6">
                    <Button variant="default" type="submit" :disabled="form.processing">Save</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script lang="ts" setup>
import { useForm, Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem, SelectLabel } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';

const props = defineProps({
    patient: Object,
})

const form = useForm({
    first_name: props.patient.first_name,
    last_name: props.patient.last_name,
    gender: props.patient.gender,
    date_of_birth: props.patient.date_of_birth,
    phone: props.patient.phone,
    email: props.patient.email,
    address: props.patient.address,
})
</script>
