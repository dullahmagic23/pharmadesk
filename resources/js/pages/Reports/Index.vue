<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="System Reports"/>
        <div class="p-6 bg-white rounded-xl shadow space-y-8">
            <h1 class="text-3xl font-extrabold text-gray-800">System Reports</h1>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card
                    title="Sales Report"
                    icon="receipt"
                    to="/reports/sales"
                    class="hover:shadow-lg transition-shadow duration-300"
                />
                <Card
                    title="Purchases Report"
                    icon="shopping-cart"
                    to="/reports/purchases"
                    class="hover:shadow-lg transition-shadow duration-300"
                />
                <Card
                    title="Stock Report"
                    icon="package"
                    to="/reports/stock"
                    class="hover:shadow-lg transition-shadow duration-300"
                />

<!--                <Card -->
<!--                    title="Inventory Movement" -->
<!--                    icon="shuffle" -->
<!--                    to="/reports/inventories" -->
<!--                    class="hover:shadow-lg transition-shadow duration-300"-->
<!--                    :bgColor="'bg-blue-100'"-->
<!--                    :textColor="'text-blue-800'"-->
<!--                />-->
                <Card
                    title="Low Stock"
                    icon="trending-down"
                    to="/reports/low-stock"
                    class="hover:shadow-lg transition-shadow duration-300"
                />
                <Card
                    title="Expired Medicines"
                    icon="alert-triangle"
                    to="/reports/expired"
                    class="hover:shadow-lg transition-shadow duration-300"
                />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import Card from './Card.vue'
import { Head } from '@inertiajs/vue3'
const breadcrumbs = [
    {title:'System Reports',href:'/reports'}
]
</script>

<style scoped>
h1 {
    text-align: center;
    margin-bottom: 1rem;
}

.grid {
    align-items: start;
}

.Card {
    border-radius: 0.75rem;
    overflow: hidden;
    background-color: #f9fafb;
    transition: transform 0.3s ease;
}

.Card:hover {
    transform: translateY(-5px);
}
</style>
