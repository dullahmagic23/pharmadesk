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
    <div class="flex h-screen">
        <!-- Left: Form Section -->
        <div class="w-full md:w-1/2 flex items-center justify-center px-6 bg-white">
            <div class="w-full max-w-md space-y-8">
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-gray-900">Welcome to PharmaDesk</h1>
                    <p class="mt-1 text-sm text-gray-500">Enter your credentials to access your account.</p>
                </div>

                <div v-if="status" class="text-sm text-green-600 text-center font-semibold">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email -->
                    <div>
                        <Label for="email" class="block mb-2 text-sm font-semibold text-gray-700">Email address</Label>
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
                                class="pl-10 border-gray-300 focus:border-blue-500 transition duration-200"
                            />
                        </div>
                        <InputError :message="form.errors.email" class="mt-1" />
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <Label for="password" class="text-sm font-semibold">Password</Label>
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
                                class="pl-10 border-gray-300 focus:border-blue-500 transition duration-200"
                            />
                        </div>
                        <InputError :message="form.errors.password" class="mt-1" />
                    </div>

<!--                    &lt;!&ndash; Remember Me &ndash;&gt;-->
<!--                    <div class="flex items-center gap-2">-->
<!--                        <Checkbox id="remember" v-model="form.remember" />-->
<!--                        <Label for="remember" class="text-sm select-none text-gray-700 cursor-pointer">-->
<!--                            Remember me-->
<!--                        </Label>-->
<!--                    </div>-->

                    <!-- Submit -->
                    <Button
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 transition transform hover:scale-105
              font-semibold text-base py-3 shadow-sm rounded-lg flex items-center justify-center"
                        :disabled="form.processing"
                    >
                        <LoaderCircle v-if="form.processing" class="h-5 w-5 mr-2 animate-spin" />
                        Log in
                    </Button>
                </form>
            </div>
        </div>

        <!-- Right: Branding Section -->
        <div class="hidden md:flex w-1/2 items-center justify-center bg-gradient-to-br from-blue-50 to-blue-200">
            <div class="flex flex-col items-center text-center px-6">
                <img
                    src="/assests/logo.png"
                    alt="PharmaDesk Logo"
                    class="w-3/4 object-contain mb-6"
                />
<!--                <h2 class="text-3xl font-extrabold text-blue-700">PharmaDesk</h2></h2>-->
                <p class="mt-2 text-base text-blue-800 font-medium">Efficient Pharmacy Management</p>
            </div>
        </div>
    </div>
</template>

