<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
    <div class="min-h-screen flex flex-col items-center justify-center p-4 bg-gray-50">
        <Head title="Log in" />

        <div class="w-full max-w-sm sm:max-w-md md:max-w-lg mx-auto">
            <div class="bg-white rounded-xl shadow-2xl p-8 sm:p-10 border border-gray-100">
                <div class="flex flex-col items-center space-y-4 mb-2 border-b border-gray-200 pb-4 text-center">
                    <img src="/assests/logo.png" alt="PharmaDesk Logo" class="h-32 object-contain" />
                </div>
                <div class="text-center mb-6">
                   <p class="text-sm font-bold">For Technical Support Call: +255625243136</p>
                    <p class="text-sm text-gray-500 mt-1">Enter your credentials to access your account</p>
                </div>

                <div v-if="status" class="text-sm text-green-600 text-center font-semibold mb-4">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <Label for="email" class="block text-sm font-medium text-gray-700">Email address</Label>
                        <div class="relative mt-1">
                            <Mail class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                            <Input
                                id="email"
                                type="email"
                                required
                                autofocus
                                autocomplete="email"
                                v-model="form.email"
                                placeholder="name@example.com"
                                class="pl-10 pr-4 py-2 w-full transition-all duration-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <Label for="password" class="text-sm font-medium text-gray-700">Password</Label>
                            <TextLink
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-xs text-blue-600 hover:text-blue-800"
                            >
                                Forgot password?
                            </TextLink>
                        </div>
                        <div class="relative mt-1">
                            <Lock class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                            <Input
                                id="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                v-model="form.password"
                                placeholder="••••••••"
                                class="pl-10 pr-4 py-2 w-full transition-all duration-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div class="flex items-center space-x-2">
                            <Checkbox id="remember" v-model="form.remember" class="w-4 h-4 rounded" />
                            <Label for="remember" class="text-sm text-gray-700 cursor-pointer">Remember me</Label>
                        </div>
                    </div>

                    <Button
                        type="submit"
                        class="w-full h-12 text-lg font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow-md transition-all duration-200"
                        :disabled="form.processing"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    >
                        <LoaderCircle v-if="form.processing" class="h-5 w-5 mr-2 animate-spin" />
                        <span v-else>Log in</span>
                    </Button>
                </form>
                <div class="flex justify-center">
                    <p  class="text-gray-600 mt-4">&copy; {{new Date().getFullYear()}} Royal Tech Services </p>
                </div>
            </div>
        </div>
    </div>
</template>
