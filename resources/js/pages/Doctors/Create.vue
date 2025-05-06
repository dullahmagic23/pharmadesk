<template>
    <AppLayout>
        <Head title="Register Doctors"/>
        <div class="container p-6">
            <h1 class="text-3xl my-3">Register a Doctor</h1>
            <form @submit.prevent="submit">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <Label for="first_name">First Name</Label>
                        <Input id="first_name" v-model="form.first_name" required />
                    </div>
                    <div>
                        <Label for="last_name">Last Name</Label>
                        <Input id="last_name" v-model="form.last_name" required />
                    </div>
                    <div>
                        <Label for="email"> Email</Label>
                        <Input id="email" type="email" v-model="form.email" required />
                    </div>
                    <div>
                        <Label for="phone">Phone</Label>
                        <Input id="phone" v-model="form.phone" />
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
                        <Input id="date_of_birth" type="date" v-model="form.date_of_birth" />
                    </div>
                    <div class="col-span-2">
                        <Label for="specialization">Specialization</Label>
                        <Input id="specialization" v-model="form.specialization" />
                    </div>
                    <div class="col-span-2">
                        <Label for="address">Address</Label>
                        <textarea v-model="form.address" class="w-full rounded border p-2" rows="3" />
                    </div>
                </div>

                <div class="mt-4">
                    <Button class="btn btn-primary" :disabled="form.processing">
                        {{ submitLabel }}
                    </Button>
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
import { Button } from '@/components/ui/button/index.js';


const props = defineProps({
    doctor: Object,
    submitLabel: { type: String, default: 'Save' },
    action: String,
});

const form = useForm({
    first_name: '',
    last_name:  '',
    email: '',
    phone: '',
    gender: '',
    date_of_birth:  '',
    specialization: '',
    address: '',
});

function submit() {
    if (props.doctor) {
        form.put(`/doctors/${props.doctor.id}`);
    } else {
        form.post('/doctors');
    }
}
</script>
