<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Mail, Lock, Phone, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{ status?: string; canResetPassword: boolean }>();

const showPassword = ref(false);

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

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center p-3 sm:p-4 bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 relative overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-200 dark:bg-blue-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-20 -translate-y-1/2 -translate-x-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-200 dark:bg-indigo-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-20 translate-y-1/2 translate-x-1/2"></div>

        <Head title="Log in" />

        <div class="w-full max-w-sm relative z-10">
            <!-- Main Card -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-2xl p-4 sm:p-8 border border-white/20 dark:border-gray-700/20">
                <!-- Logo Section - Compact -->
                <div class="flex flex-col items-center space-y-2 mb-4">
                    <div class="relative w-14 sm:w-16 h-14 sm:h-16 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg">
                        <img src="/assests/logo.png" alt="PharmaDesk Logo" class="h-10 sm:h-12 object-contain opacity-90" />
                    </div>
                    <div class="text-center">
                        <h1 class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-white">PharmaDesk</h1>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Inventory System</p>
                    </div>
                </div>

                <!-- Status Message -->
                <div v-if="status" class="mb-3 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50">
                    <p class="text-xs sm:text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ status }}</p>
                </div>

                <!-- Support Info - Compact Mobile -->
                <div class="sm:hidden bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 rounded p-2.5 mb-3 flex items-center gap-2">
                    <Phone class="h-4 w-4 text-blue-600 dark:text-blue-400 flex-shrink-0" />
                    <p class="text-xs font-semibold text-blue-600 dark:text-blue-400">+255 625 243 136</p>
                </div>

                <!-- Support Info - Desktop -->
                <div class="hidden sm:block bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 rounded-lg p-3 mb-5">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                            <Phone class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Technical Support</p>
                            <p class="text-sm font-bold text-blue-600 dark:text-blue-400">+255 625 243 136</p>
                        </div>
                    </div>
                </div>

                <!-- Welcome Text -->
                <div class="text-center mb-4">
                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Sign in to your account</p>
                </div>

                <!-- Login Form -->
                <form @submit.prevent="submit" class="space-y-3">
                    <!-- Email Field -->
                    <div>
                        <Label for="email" class="block text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Email address</Label>
                        <div class="relative group">
                            <Mail class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                            <Input
                                id="email"
                                type="email"
                                required
                                autofocus
                                autocomplete="email"
                                v-model="form.email"
                                placeholder="you@example.com"
                                class="pl-9 pr-4 py-2.5 w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg transition-all duration-200 focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 dark:text-white dark:placeholder-gray-500 text-sm"
                            />
                        </div>
                        <InputError :message="form.errors.email" class="mt-1 text-xs" />
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <Label for="password" class="text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300">Password</Label>
                            <TextLink
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors"
                            >
                                Forgot?
                            </TextLink>
                        </div>
                        <div class="relative group">
                            <Lock class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                            <Input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                autocomplete="current-password"
                                v-model="form.password"
                                placeholder="••••••••"
                                class="pl-9 pr-11 py-2.5 w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg transition-all duration-200 focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 dark:text-white dark:placeholder-gray-500 text-sm"
                            />
                            <button
                                type="button"
                                @click="togglePassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-0.5"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                            >
                                <Eye v-if="!showPassword" class="h-4 w-4" />
                                <EyeOff v-else class="h-4 w-4" />
                            </button>
                        </div>
                        <InputError :message="form.errors.password" class="mt-1 text-xs" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center space-x-2 pt-0.5">
                        <Checkbox id="remember" v-model="form.remember" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                        <Label for="remember" class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                            Keep me signed in
                        </Label>
                    </div>

                    <!-- Submit Button -->
                    <Button
                        type="submit"
                        class="w-full h-10 sm:h-11 text-sm sm:text-base font-semibold bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 mt-4 disabled:opacity-75 disabled:cursor-not-allowed"
                        :disabled="form.processing"
                    >
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 mr-2 animate-spin inline" />
                        <span>{{ form.processing ? 'Signing in...' : 'Sign in' }}</span>
                    </Button>
                </form>

                <!-- Divider -->
                <div class="relative my-3">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">Secure</span>
                    </div>
                </div>

                <!-- Security Notice -->
                <div class="bg-gray-50 dark:bg-gray-700/30 rounded p-2.5 text-center">
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                        Data encrypted and secure
                    </p>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        &copy; {{ new Date().getFullYear() }}
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Royal Tech Services</span>
                    </p>
                </div>
            </div>

            <!-- Bottom Support Message - Desktop Only -->
            <div class="hidden sm:block mt-4 text-center">
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    Need help?
                    <TextLink href="mailto:support@royaltech.com" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                        Contact support
                    </TextLink>
                </p>
            </div>
        </div>
    </div>
</template>
