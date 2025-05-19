<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Mail, Lock } from 'lucide-vue-next';

defineProps<{ status?: string; canResetPassword: boolean }>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>
<template>
    <div class="min-h-screen flex flex-col bg-gradient-to-br from-blue-50 to-white">
        <!-- Header -->
        <div class="py-8 text-center bg-blue-100 shadow-sm">
            <img src="/assests/logo.png" alt="PharmaDesk Logo" class="h-12 mx-auto object-contain" />
            <h1 class="mt-2 text-xl font-bold text-blue-700">PharmaDesk</h1>
        </div>

        <!-- Login Form Section -->
        <div class="flex flex-1 items-center justify-center px-4 sm:px-6">
            <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-xl w-full max-w-md p-8 space-y-6">
                
                <div class="text-center">
                    <h2 class="text-lg font-semibold text-gray-800">Welcome Back</h2>
                    <p class="text-sm text-gray-500">Enter your credentials to access your account</p>
                </div>

                <div v-if="status" class="text-sm text-green-600 text-center font-semibold">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email -->
                    <div>
                        <Label for="email" class="block mb-1 text-sm font-medium text-gray-700">Email address</Label>
                        <div class="relative">
                            <Mail class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                            <Input
                                id="email"
                                type="email"
                                required
                                autofocus
                                autocomplete="email"
                                v-model="form.email"
                                placeholder="email@example.com"
                                class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        <InputError :message="form.errors.email" class="mt-1" />
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <Label for="password" class="text-sm font-medium text-gray-700">Password</Label>
                            <TextLink
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-xs text-blue-600 hover:underline"
                            >
                                Forgot password?
                            </TextLink>
                        </div>
                        <div class="relative">
                            <Lock class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                            <Input
                                id="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                v-model="form.password"
                                placeholder="••••••••"
                                class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        <InputError :message="form.errors.password" class="mt-1" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center space-x-2">
                        <Checkbox id="remember" v-model="form.remember" class="rounded" />
                        <Label for="remember" class="text-sm text-gray-700 cursor-pointer">Remember me</Label>
                    </div>

                    <!-- Submit Button -->
                    <Button
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 font-semibold rounded-md shadow transition duration-200 ease-in-out transform hover:scale-105"
                        :disabled="form.processing"
                    >
                        <LoaderCircle v-if="form.processing" class="h-5 w-5 mr-2 animate-spin" />
                        Log in
                    </Button>
                </form>
            </div>
        </div>
    </div>
</template>
