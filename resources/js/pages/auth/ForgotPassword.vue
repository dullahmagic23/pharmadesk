<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Mail, ArrowLeft, CheckCircle2 } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const emailSubmitted = ref(false);

const submit = () => {
    form.post(route('password.email'), {
        onSuccess: () => {
            emailSubmitted.value = true;
        }
    });
};
</script>

<template>
    <AuthLayout title="Reset your password" description="We'll send you a link to reset your password">
        <Head title="Forgot password" />

        <!-- Success Message -->
        <div v-if="status || emailSubmitted" class="mb-6 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 flex gap-3">
            <CheckCircle2 class="h-5 w-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" />
            <div>
                <p class="font-semibold text-emerald-900 dark:text-emerald-100 text-sm">Check your email</p>
                <p class="text-xs text-emerald-800 dark:text-emerald-200 mt-1">{{ status || 'We\'ve sent a password reset link to your email address. Please check your inbox and spam folder.' }}</p>
            </div>
        </div>

        <div v-if="!emailSubmitted" class="space-y-5">
            <!-- Info Box -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 rounded-lg p-4">
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                        <Mail class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Enter your email address</p>
                        <p class="text-xs text-blue-800 dark:text-blue-200 mt-0.5">We'll send you a secure link to reset your password</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-4">
                <div class="space-y-2">
                    <Label for="email" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Email address</Label>
                    <div class="relative group">
                        <Mail class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            autocomplete="email"
                            v-model="form.email"
                            autofocus
                            placeholder="you@example.com"
                            class="pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg transition-all duration-200 focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 dark:text-white dark:placeholder-gray-500 text-sm"
                        />
                    </div>
                    <InputError :message="form.errors.email" class="text-xs mt-1" />
                </div>

                <!-- Submit Button -->
                <Button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full h-10 text-sm font-semibold bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-200 disabled:opacity-75 disabled:cursor-not-allowed"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 mr-2 animate-spin" />
                    <span>{{ form.processing ? 'Sending...' : 'Send reset link' }}</span>
                </Button>
            </form>

            <!-- Divider -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">Or</span>
                </div>
            </div>

            <!-- Back to Login -->
            <TextLink
                :href="route('login')"
                class="w-full h-10 flex items-center justify-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 font-medium transition-colors text-sm"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to log in
            </TextLink>
        </div>

        <!-- Post-Submit State -->
        <div v-else class="space-y-4 text-center">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <CheckCircle2 class="h-8 w-8 text-emerald-600 dark:text-emerald-400" />
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Check your email</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    We've sent a password reset link to <span class="font-semibold text-gray-900 dark:text-white">{{ form.email }}</span>
                </p>
            </div>

            <!-- Tips -->
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-lg p-3 text-left">
                <p class="text-xs font-medium text-amber-900 dark:text-amber-100 mb-2">Tips:</p>
                <ul class="text-xs text-amber-800 dark:text-amber-200 space-y-1">
                    <li>• Check your spam/junk folder if you don't see the email</li>
                    <li>• The link expires in 24 hours</li>
                    <li>• Click the link to set a new password</li>
                </ul>
            </div>

            <!-- Back Button -->
            <TextLink
                :href="route('login')"
                class="w-full h-10 flex items-center justify-center gap-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium transition-colors text-sm"
            >
                <ArrowLeft class="h-4 w-4" />
                Return to log in
            </TextLink>
        </div>
    </AuthLayout>
</template>
