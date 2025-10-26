<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import currency from '@/modules/currecyFormatter'
import ConfirmDeleteDialog from '@/components/ConfirmDeleteDialog.vue'

const props = defineProps({
    payment: Object,
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Sales', href: '/sales' },
    { title: 'Payments', href: '/payments' },
    { title: `Payment #${props.payment.id}`, href: '#' },
]

const showDeleteConfirm = ref(false)
const copied = ref(false)

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

const formatDateShort = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

const getStatusConfig = (status) => {
    const config = {
        paid: {
            bg: 'bg-emerald-100 dark:bg-emerald-900/30',
            text: 'text-emerald-700 dark:text-emerald-300',
            icon: 'fa-check-circle',
            light: 'bg-emerald-50 dark:bg-emerald-900/10'
        },
        partial: {
            bg: 'bg-amber-100 dark:bg-amber-900/30',
            text: 'text-amber-700 dark:text-amber-300',
            icon: 'fa-hourglass-half',
            light: 'bg-amber-50 dark:bg-amber-900/10'
        },
        unpaid: {
            bg: 'bg-rose-100 dark:bg-rose-900/30',
            text: 'text-rose-700 dark:text-rose-300',
            icon: 'fa-exclamation-circle',
            light: 'bg-rose-50 dark:bg-rose-900/10'
        },
    }
    return config[status] || config.unpaid
}

const paymentProgress = computed(() => {
    const total = props.payment.sale.total
    const remaining = props.payment.sale.balance
    const paid = total - remaining
    return Math.round((paid / total) * 100)
})

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text)
    copied.value = true
    setTimeout(() => (copied.value = false), 2000)
}

const handleDelete = () => {
    router.delete(`/sales-payments/${props.payment.id}`, {
        onSuccess: () => {
            router.visit('/payments')
        },
    })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Payment #${payment.id.slice(0, 8)}`" />

        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 p-4 md:p-8">
            <div class="max-w-5xl mx-auto space-y-6">
                <!-- Header with Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                <i class="fas fa-receipt text-indigo-600 dark:text-indigo-400 text-xl"></i>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Payment #{{ payment.id.slice(0,8) }}</h1>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(payment.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <Link
                            href="/sales-payments"
                            class="flex-1 sm:flex-none px-4 py-2.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg font-medium hover:bg-gray-100 dark:hover:bg-gray-700 transition border border-gray-200 dark:border-gray-700 text-center"
                        >
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </Link>
                        <ConfirmDeleteDialog
                            :title="`Delete Payment #${payment.id}`"
                            :description="`Are you sure you want to delete payment #${payment.id}? This action cannot be undone.`"
                            buttonLabel="Delete"
                            button-label-text-color="text-rose-600 hover:text-rose-700"
                            triggerVariant="ghost"
                            :onConfirm="handleDelete"
                        />
                    </div>
                </div>

                <!-- Quick Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div :class="[getStatusConfig(payment.sale.status).light, 'rounded-xl p-6 border border-gray-200 dark:border-gray-700']">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Status</p>
                        <div class="flex items-center gap-2">
                            <div :class="[getStatusConfig(payment.sale.status).bg, 'w-10 h-10 rounded-lg flex items-center justify-center']">
                                <i :class="['fas', getStatusConfig(payment.sale.status).icon, getStatusConfig(payment.sale.status).text, 'text-lg']"></i>
                            </div>
                            <span :class="[getStatusConfig(payment.sale.status).text, 'font-bold text-lg']">
                                {{ payment.sale.status.charAt(0).toUpperCase() + payment.sale.status.slice(1) }}
                            </span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Amount Paid</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ currency(payment.amount) }}</p>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-700 rounded-xl p-6 text-white shadow-lg">
                        <p class="text-xs font-semibold text-indigo-100 uppercase tracking-wide mb-2">Sale Total</p>
                        <p class="text-3xl font-bold">{{ currency(payment.sale.total) }}</p>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Payment Progress -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Progress</h3>
                                <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ paymentProgress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                                <div
                                    :style="{ width: `${paymentProgress}%` }"
                                    class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-full transition-all duration-500 rounded-full"
                                ></div>
                            </div>
                            <div class="flex justify-between mt-4 text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Paid: <span class="font-semibold text-gray-900 dark:text-white">{{ currency(payment.sale.total - payment.sale.balance) }}</span></span>
                                <span class="text-gray-600 dark:text-gray-400">Total: <span class="font-semibold text-gray-900 dark:text-white">{{ currency(payment.sale.total) }}</span></span>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Payment Details</h3>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Payment ID</p>
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-lg font-semibold text-gray-900 dark:text-white">#{{ payment.id.slice(0, 8) }}</p>
                                            <button
                                                @click="copyToClipboard(payment.id)"
                                                class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition"
                                                :title="copied ? 'Copied!' : 'Copy ID'"
                                            >
                                                <i :class="['fas', copied ? 'fa-check text-green-500' : 'fa-copy text-gray-500 dark:text-gray-400']"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Amount</p>
                                        <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ currency(payment.amount) }}</p>
                                    </div>
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Payment Method</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white capitalize">
                                            {{ payment.method || 'Not specified' }}
                                        </p>
                                    </div>
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Date</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ formatDateShort(payment.created_at) }}</p>
                                    </div>
                                </div>

                                <div v-if="payment.reference" class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Reference Number</p>
                                    <div class="flex items-center justify-between">
                                        <p class="text-gray-900 dark:text-white font-mono">{{ payment.reference }}</p>
                                        <button @click="copyToClipboard(payment.reference)" class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition">
                                            <i class="fas fa-copy text-gray-500 dark:text-gray-400"></i>
                                        </button>
                                    </div>
                                </div>

                                <div v-if="payment.notes" class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                    <p class="text-xs font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wide mb-2">
                                        <i class="fas fa-note-sticky mr-2"></i>Notes
                                    </p>
                                    <p class="text-gray-900 dark:text-white">{{ payment.notes }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Sale Information -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Related Sale</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <Link
                                    :href="`/sales/${payment.sale.id}`"
                                    class="p-4 bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-900/10 hover:from-indigo-100 hover:to-indigo-200 dark:hover:from-indigo-900/30 dark:hover:to-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg transition cursor-pointer"
                                >
                                    <p class="text-xs font-semibold text-indigo-700 dark:text-indigo-300 uppercase tracking-wide mb-2">Sale ID</p>
                                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">#{{ payment.sale.id.slice(0,8) }}</p>
                                </Link>
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Sale Amount</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ currency(payment.sale.total) }}</p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Sale Date</p>
                                    <p class="text-gray-900 dark:text-white font-semibold">{{ formatDateShort(payment.sale.created_at) }}</p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Remaining</p>
                                    <p :class="[payment.sale.balance > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400', 'text-2xl font-bold']">
                                        {{ currency(payment.sale.balance || 0) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Customer Info -->
                    <div class="space-y-6">
                        <!-- Customer Card -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="h-1 bg-gradient-to-r from-indigo-400 to-indigo-600"></div>
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                                    <i class="fas fa-user-circle text-indigo-600 dark:text-indigo-400"></i>
                                    Customer Information
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Name</p>
                                        <p class="text-gray-900 dark:text-white font-semibold text-lg">{{ payment.sale.buyer.name }}</p>
                                    </div>
                                    <div v-if="payment.sale.buyer.email" class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Email</p>
                                        <a :href="`mailto:${payment.sale.buyer.email}`" class="text-indigo-600 dark:text-indigo-400 hover:underline break-all">
                                            {{ payment.sale.buyer.email }}
                                        </a>
                                    </div>
                                    <div v-if="payment.sale.buyer.phone" class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Phone</p>
                                        <a :href="`tel:${payment.sale.buyer.phone}`" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                            {{ payment.sale.buyer.phone }}
                                        </a>
                                    </div>
                                    <div v-if="payment.sale.buyer.address" class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Address</p>
                                        <p class="text-gray-900 dark:text-white text-sm">{{ payment.sale.buyer.address }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Info -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                                <i class="fas fa-clock text-amber-600 dark:text-amber-400"></i>
                                Timeline
                            </h3>
                            <div class="space-y-4">
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mb-2">
                                            <i class="fas fa-check text-emerald-600 dark:text-emerald-400"></i>
                                        </div>
                                        <div class="w-0.5 h-12 bg-gray-200 dark:bg-gray-700"></div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">Created</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(payment.created_at) }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                            <i class="fas fa-pen text-indigo-600 dark:text-indigo-400"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">Last Updated</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(payment.updated_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
