<template>
    <AppLayout title="Register User">
        <div class="container mx-auto bg-white p-6 rounded-xl shadow">
            <h1 class="text-2xl font-bold mb-6">Register New User</h1>

            <form @submit.prevent="form.put(route('users.update', user.id))">
                <!-- Name -->
                <div class="mb-4">
                    <Label for="name">Name</Label>
                    <Input v-model="form.name" type="text" id="name" class="mt-1 block w-full" />
                    <InputError :message="form.errors.name" />
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <Label for="email">Email</Label>
                    <Input v-model="form.email" type="email" id="email" class="mt-1 block w-full" />
                    <InputError :message="form.errors.email" />
                </div>
                <!-- Role -->
                <div class="mb-4">
                    <Label for="role">Role</Label>
                    <Select v-model="form.role" class="mt-1 block w-full">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select a role" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectLabel>Roles</SelectLabel>
                                <SelectItem v-for="role in roles" :key="role" :value="role.id" class="capitalize">
                                    {{ role.name }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.role" />
                </div>

                <div>
                    <Label for="is_active">{{ form.is_active?'Activated':'Disabled' }}
                        <input id="is_active" type="checkbox" v-model="form.is_active" />
                    </Label>
                </div>

                <!-- Submit -->
                <div class="mt-6">
                    <Button :disabled="form.processing">Update</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import InputError from '@/components/InputError.vue'
import { Select, SelectTrigger, SelectValue, SelectContent, SelectGroup, SelectLabel, SelectItem } from '@/components/ui/select'


const page = usePage()

let roles = page.props.roles
let user = page.props.user

const form = useForm({
    name: user.name,
    email: user.email,
    role: user.roles[0].id,
    is_active: Boolean(user.is_active)
})
</script>
