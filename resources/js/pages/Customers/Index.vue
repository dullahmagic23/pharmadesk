<script lang="ts" setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue';
defineProps({ customers: Array })
</script>

<template>
   <AppLayout>
       <div class="controller p-6">
           <div class="flex justify-between">
               <h1 class="text-2xl font-bold mb-4">Customers</h1>
               <Link :href="route('customers.create')" class="btn btn-primary mb-4">Add Customer</Link>
           </div>
           <table class="table-auto w-full border">
               <thead>
               <tr class="bg-gray-100">
                   <th class="p-2 text-left">Name</th>
                   <th class="p-2 text-left">Phone</th>
                   <th class="p-2 text-left">Email</th>
                   <th class="p-2 text-left">Address</th>
                   <th class="p-2 text-left">Actions</th>
               </tr>
               </thead>
               <tbody>
               <tr v-for="customer in customers" :key="customer.id">
                   <td class="p-2">{{ customer.name }}</td>
                   <td class="p-2">{{ customer.phone }}</td>
                   <td class="p-2">{{ customer.email }}</td>
                   <td class="p-2">{{ customer.address }}</td>
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
