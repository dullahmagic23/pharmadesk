<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Lock, Mail, Eye, EyeOff, CheckCircle2 } from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface Props {
    token: string;
    email: string;
}

const props = defineProps<Props>();

const showPassword = ref(false);
const showConfirm = ref(false);
const passwordResetSuccess = ref(false);

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

// Password strength indicator
const passwordStrength = computed(() => {
    const pwd = form.password;
    if (!pwd) return { level: 0, text: '', color: '' };

    let strength = 0;
    if (pwd.length >= 8) strength++;
    if (pwd.length >= 12) strength++;
    if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) strength++;
    if (/[0-9]/.test(pwd)) strength++;
    if (/[^a-zA-Z0-9]/.test(pwd)) strength++;

    const levels = [
        { level: 0, text: '', color: '' },
        { level: 1, text: 'Weak', color: 'bg-red-500' },
        { level: 2, text: 'Fair', color: 'bg-amber-500' },
        { level: 3, text: 'Good', color: 'bg-yellow-500' },
        { level: 4, text: 'Strong', color: 'bg-emerald-500' },
        { level: 5, text: 'Very Strong', color: 'bg-emerald-600' },
    ];

    return levels[strength] || levels[5];
});

const passwordsMatch = computed(() => {
    return form.password && form.password_confirmation && form.password === form.password_confirmation;
});

const submit = () => {
    form.post(route('password.store'), {
        onSuccess: () => {
            passwordResetSuccess.value = true;
            form.reset('password', 'password_confirmation');
        },
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const toggleConfirm = () => {
    showConfirm.value = !showConfirm.value;
};
</script>

<template>
    <AuthLayout title="Create a new password" description="Enter a secure password to regain access to your account">
        <Head title="Reset password" />

        <!-- Success Message -->
        <div v-if="passwordResetSuccess" class="mb-6 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 flex gap-3">
            <CheckCircle2 class="h-5 w-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" />
            <div>
                <p class="font-semibold text-emerald-900 dark:text-emerald-100 text-sm">Password reset successful!</p>
                <p class="text-xs text-emerald-800 dark:text-emerald-200 mt-1">You can now log in with your new password</p>
            </div>
        </div>

        <!-- Info Box -->
        <div v-if="!passwordResetSuccess" class="mb-5 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 rounded-lg p-4">
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                    <Lock class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Create a strong password</p>
                    <p class="text-xs text-blue-800 dark:text-blue-200 mt-0.5">Use a mix of uppercase, lowercase, numbers, and symbols</p>
                </div>
            </div>
        </div>

        <form v-if="!passwordResetSuccess" @submit.prevent="submit" class="space-y-4">
            <!-- Email Field (Read-only) -->
            <div class="space-y-2">
                <Label for="email" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Email address</Label>
                <div class="relative group">
                    <Mail class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        v-model="form.email"
                        readonly
                        class="pl-10 pr-4 py-2.5 bg-gray-100 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 cursor-not-allowed"
                    />
                </div>
                <InputError :message="form.errors.email" class="text-xs mt-1" />
            </div>

            <!-- Password Field -->
            <div class="space-y-2">
                <Label for="password" class="text-sm font-semibold text-gray-700 dark:text-gray-300">New password</Label>
                <div class="relative group">
                    <Lock class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                    <Input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        autocomplete="new-password"
                        v-model="form.password"
                        autofocus
                        placeholder="••••••••"
                        class="pl-10 pr-11 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg transition-all duration-200 focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 dark:text-white dark:placeholder-gray-500 text-sm"
                    />
                    <button
                        type="button"
                        @click="togglePassword"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-0.5"
                        :aria-label="showPassword ? 'Hide password' : 'Show password'"
                    >
                        <Eye v-if="!showPassword" class="h-4 w-4" />
                        <EyeOff v-else class="h-4 w-4" />
                    </button>
                </div>
                <InputError :message="form.errors.password" class="text-xs mt-1" />

                <!-- Password Strength Indicator -->
                <div v-if="form.password" class="space-y-2">
                    <div class="flex gap-1">
                        <div class="flex-1 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div :class="[passwordStrength.color, 'h-full transition-all duration-300']" :style="{ width: `${(passwordStrength.level / 5) * 100}%` }"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-600 dark:text-gray-400">Password strength:</span>
                        <span v-if="passwordStrength.level > 0" :class="[
                            'text-xs font-semibold',
                            {
                                'text-red-600 dark:text-red-400': passwordStrength.level === 1,
                                'text-amber-600 dark:text-amber-400': passwordStrength.level === 2,
                                'text-yellow-600 dark:text-yellow-400': passwordStrength.level === 3,
                                'text-emerald-600 dark:text-emerald-400': passwordStrength.level >= 4,
                            }
                        ]">
                            {{ passwordStrength.text }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Confirm Password Field -->
            <div class="space-y-2">
                <Label for="password_confirmation" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Confirm password</Label>
                <div class="relative group">
                    <Lock class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                    <Input
                        id="password_confirmation"
                        :type="showConfirm ? 'text' : 'password'"
                        name="password_confirmation"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="••••••••"
                        class="pl-10 pr-11 py-2.5 bg-gray-50 dark:bg-gray-700/50 border transition-all duration-200"
                        :class="[
                            'border-gray-300 dark:border-gray-600 rounded-lg focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 dark:text-white dark:placeholder-gray-500 text-sm',
                            form.password_confirmation && !passwordsMatch ? 'border-red-300 dark:border-red-600' : '',
                            form.password_confirmation && passwordsMatch ? 'border-emerald-300 dark:border-emerald-600' : ''
                        ]"
                    />
                    <button
                        type="button"
                        @click="toggleConfirm"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-0.5"
                        :aria-label="showConfirm ? 'Hide password' : 'Show password'"
                    >
                        <Eye v-if="!showConfirm" class="h-4 w-4" />
                        <EyeOff v-else class="h-4 w-4" />
                    </button>
                </div>

                <!-- Password Match Indicator -->
                <div v-if="form.password_confirmation" class="flex items-center gap-2 text-xs">
                    <div :class="[
                        'w-4 h-4 rounded-full flex items-center justify-center',
                        passwordsMatch ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-red-100 dark:bg-red-900/30'
                    ]">
                        <span :class="[
                            'text-lg leading-none',
                            passwordsMatch ? 'text-emerald-600' : 'text-red-600'
                        ]">
                            {{ passwordsMatch ? '✓' : '✕' }}
                        </span>
                    </div>
                    <span :class="[
                        'font-medium',
                        passwordsMatch ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'
                    ]">
                        {{ passwordsMatch ? 'Passwords match' : 'Passwords do not match' }}
                    </span>
                </div>

                <InputError :message="form.errors.password_confirmation" class="text-xs mt-1" />
            </div>

            <!-- Submit Button -->
            <Button
                type="submit"
                :disabled="form.processing || !passwordsMatch || !form.password"
                class="w-full h-10 text-sm font-semibold bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-200 mt-4 disabled:opacity-75 disabled:cursor-not-allowed"
            >
                <LoaderCircle v-if="form.processing" class="h-4 w-4 mr-2 animate-spin" />
                <span>{{ form.processing ? 'Resetting...' : 'Reset password' }}</span>
            </Button>
        </form>

        <!-- Success State -->
        <div v-else class="text-center space-y-4">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <CheckCircle2 class="h-8 w-8 text-emerald-600 dark:text-emerald-400" />
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Password reset successful!</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Your password has been updated. You can now log in with your new password.</p>
            </div>

            <!-- Tips -->
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-lg p-3 text-left">
                <p class="text-xs font-medium text-amber-900 dark:text-amber-100 mb-2">Tips:</p>
                <ul class="text-xs text-amber-800 dark:text-amber-200 space-y-1">
                    <li>• Keep your password secure and unique</li>
                    <li>• Don't share it with anyone</li>
                    <li>• Update it periodically for security</li>
                </ul>
            </div>

            <!-- Back to Login -->
            <a href="/login" class="inline-block w-full h-10 flex items-center justify-center rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition-colors text-sm">
                Back to log in
            </a>
        </div>
    </AuthLayout>
</template>
