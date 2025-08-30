<script lang="ts" setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, ref } from 'vue';
import { Input } from '@/components/ui/input';


interface Customer {
    id: number;
    name: string;
    phone: string;
    email: string;
}
const props = defineProps({ customers: Array<Customer> })

const search = ref('')

const filteredCustomers = computed(() => {
    if (search.value.length === 0) return props.customers;
    return props.customers?.filter(customer=>customer.name.toLowerCase().includes(search.value.toLowerCase()))
})

const breadcrumbs = [
    {title: 'Dashboard', href: '/dashboard'},
    {title: 'Customers', href: '/customers'},
]
</script>

<template>
   <AppLayout :breadcrumbs="breadcrumbs">
       <div class="controller p-6">
           <h1 class="text-2xl font-bold mb-4">Customers</h1>
           <div class="flex justify-between">
               <div class="flex mb-4">
                   <Input v-model="search" type="text" placeholder="Search customers..." class="input input-bordered w-64 mr-2" />
               </div>
               <Link :href="route('customers.create')" class="btn btn-primary mb-4">Add Customer</Link>
           </div>
           <table class="table-auto w-full border">
               <thead>
               <tr class="bg-gray-100">
                   <th class="p-2 text-left">Name</th>
                   <th class="p-2 text-left">Phone</th>
                   <th class="p-2 text-left">Email</th>
                   <th class="p-2 text-left">Actions</th>
               </tr>
               </thead>
               <tbody>
               <tr v-for="customer in filteredCustomers" :key="customer.id">
                   <td class="p-2">{{ customer.name }}</td>
                   <td class="p-2">{{ customer.phone }}</td>
                   <td class="p-2">{{ customer.email }}</td>
                   <td class="p-2 space-x-2">
                       <Link :href="`/customers/${customer.id}`" class="text-blue-600">View</Link>
                       <Link :href="`/customers/${customer.id}/edit`" class="text-green-600">Edit</Link>
                   </td>
               </tr>
               </tbody>
           </table>
       </div>
   </AppLayout>
</template>
