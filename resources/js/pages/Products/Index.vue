<script setup>
import { ref, computed } from 'vue'
import { Head, router,Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import FlashMessage from '@/components/FlashMessage.vue'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { PlusIcon, PencilIcon, TrashIcon, EyeIcon } from 'lucide-vue-next'

const props = defineProps({
  products: Array,
})

const search = ref('')

const filteredProducts = computed(() => {
  // Filtering products based on the name and category (case insensitive)
  return props.products.filter(p =>
    p.name.toLowerCase().includes(search.value.toLowerCase()) ||
    p.category.toLowerCase().includes(search.value.toLowerCase())
  )
})

const deleteProduct = (id) => {
  if (confirm('Are you sure you want to delete this product?')) {
    router.delete(route('products.destroy', id))
  }
}

const breadcrumbs = [
    {title:'Products',href:'/products'},
]
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Products" />
    <FlashMessage />

    <div class="p-6 space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Products</h1>
        <Button @click="$inertia.visit(route('products.create'))">
          <PlusIcon class="w-4 h-4 mr-2" />
          Add Product
        </Button>
      </div>

      <!-- Search Bar: Allow searching by name or category -->
      <div class="w-full max-w-md">
        <Input
          v-model="search"
          placeholder="Search by name or category..."
          class="mt-4"
        />
      </div>

      <!-- Product Table -->
      <div class="overflow-x-auto mt-6">
        <table class="min-w-full border border-gray-300 bg-white rounded shadow-sm">
          <thead>
            <tr class="bg-gray-100 text-left">
              <th class="px-4 py-2 border">#</th>
              <th class="px-4 py-2 border">Name</th>
              <th class="px-4 py-2 border">Category</th>
              <th class="px-4 py-2 border">Unit</th>
              <th class="px-4 py-2 border">Description</th>
              <th class="px-4 py-2 border text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(product, index) in filteredProducts" :key="product.id" class="hover:bg-gray-50">
              <td class="px-4 py-2 border">{{ index + 1 }}</td>
              <td class="px-4 py-2 border">{{ product.name }}</td>
              <td class="px-4 py-2 border">{{ product.category }}</td>
              <td class="px-4 py-2 border">{{ product.unit || '-' }}</td>
              <td class="px-4 py-2 border">{{ product.description || '-' }}</td>
              <td class="px-4 py-2 border text-center">
                <div class="flex justify-center gap-2">
                  <Button size="sm" variant="outline" @click="$inertia.visit(route('products.edit', product.id))">
                    <PencilIcon class="w-4 h-4" />
                  </Button>
                  <Link :href="`/products/${product.id}`">
                    <Button size="sm" variant="outline">
                      <EyeIcon class="w-4 h-4" />
                    </Button>
                  </Link>
                  <Button size="sm" variant="destructive" @click="deleteProduct(product.id)">
                    <TrashIcon class="w-4 h-4" />
                  </Button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredProducts.length === 0">
              <td colspan="6" class="px-4 py-4 text-center text-gray-500">No products found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
