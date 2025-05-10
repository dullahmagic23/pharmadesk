<template>
    <Link :href="to">
    <div
        :class="`p-4 border rounded-xl shadow cursor-pointer flex items-center space-x-4 transition duration-200 ease-in-out transform hover:scale-105 ${bgColor} ${textColor}`" 
        @mouseover="hover = true" 
        @mouseleave="hover = false">
        <component :is="iconComponent" class="w-8 h-8 text-gray-600" />
        <span class="text-lg font-semibold text-gray-800">{{ title }}</span>
    </div>
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { FileText, ShoppingCart, Package, AlertTriangle, Shuffle, TrendingDown } from 'lucide-vue-next'

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    icon: String,
    to: String,
})

const iconMap = {
    'receipt': FileText,
    'shopping-cart': ShoppingCart,
    'package': Package,
    'alert-triangle': AlertTriangle,
    'shuffle': Shuffle,
    'trending-down': TrendingDown,
}

const iconComponent = iconMap[props.icon] || FileText
</script>
