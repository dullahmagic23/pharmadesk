<template>
  <AppLayout>
    <div class="container max-w-xl mx-auto p-6 space-y-6">
      <h1 class="flex  text-center text-2xl font-bold mb-4"><RecycleIcon class="mr-2"/> Stock Unit Conversion Tool</h1>
      <form @submit.prevent="convertStock" class="space-y-6 bg-white p-6 rounded shadow">
        <div>
          <label class="block mb-1 font-medium">Select Stock Item</label>
          <select v-model="form.selectedStockId" class="w-full border rounded px-3 py-2">
            <option value="" disabled>Select item</option>
            <option v-for="stock in stocks" :key="stock.id" :value="stock.id">
              {{ stock.name }}
            </option>
          </select>
            <InputError :message="form.errors.selectedStockId"/>
        </div>
        <div v-if="selectedStock">
          <div>
            <label class="block mb-1 font-medium">From Unit(Bulk Unit)</label>
            <select v-model="form.fromUnit" class="w-full border rounded px-3 py-2">
              <option value="" disabled>Select unit</option>
              <option v-for="unit in selectedStock.units" :key="unit.id" :value="unit.id">
                {{ unit.unit_name }}
              </option>
            </select>
              <InputError :message="form.errors.fromUnit"/>
          </div>
          <div>
            <label class="block mb-1 font-medium">To Unit(Smaller Unit)</label>
            <select v-model="form.toUnit" class="w-full border rounded px-3 py-2">
              <option value="" disabled>Select unit</option>
              <option v-for="unit in selectedStock.units" :key="unit.id" :value="unit.id">
                {{ unit.unit_name }}
              </option>
            </select>
              <InputError :message="form.errors.toUnit"/>
          </div>
          <div>
            <label class="block mb-1 font-medium">Conversion Rate</label>
            <input type="number" v-model.number="form.rate" min="0" class="w-full border rounded px-3 py-2" />
              <InputError :message="form.errors.rate"/>
          </div>
          <div>
            <label class="block mb-1 font-medium">Quantity</label>
            <input type="number" v-model.number="form.quantity" min="0" class="w-full border rounded px-3 py-2" />
              <InputError :message="form.errors.quantity"/>
          </div>
          <div>
            <label class="block mb-1 font-medium">Retail Price(Smaller Unit)</label>
            <input type="number" v-model.number="form.retail_price" min="0" class="w-full border rounded px-3 py-2" />
              <InputError :message="form.errors.retail_price"/>
          </div>
          <div>
            <label class="block mb-1 font-medium">Wholesale Price(Smaller Unit)</label>
            <input type="number" v-model.number="form.wholesale_price" min="0" class="w-full border rounded px-3 py-2" />
              <InputError :message="form.errors.wholesale_price"/>
          </div>
        </div>
        <Button  type="submit"  class="w-full">Convert</Button>
      </form>
      <div v-if="result !== null" class="mt-4 p-4 bg-blue-50 rounded">
        <span class="font-semibold">Result:</span>
        {{ amount }} {{ fromUnit }} = <span class="font-bold">{{ result }}</span> {{ toUnit }}
      </div>
    </div>
  </AppLayout>
</template>

<script  setup>
import { ref, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import { useForm } from '@inertiajs/vue3'
import { RecycleIcon } from 'lucide-vue-next'
import InputError from '@/components/InputError.vue';

const props = defineProps({
    stocks:Array
})

const form = useForm({
    selectedStockId: '',
    fromUnit: '',
    toUnit: '',
    rate: 0,
    quantity: 0,
    retail_price:0,
    wholesale_price:0
})
const result = ref(null)

const selectedStock = computed(() =>
  props.stocks.find(s => s.id === form.selectedStockId)
)

// const canConvert = computed(() =>
//   selectedStock.value &&
//   form.fromUnit &&
//   form.toUnit &&
//   form.rate > 0 &&
//   form.quantity > 0 &&
//   form.retail_price > 0 &&
//   form.wholesale_price > 0 &&
//   form.fromUnit !== form.toUnit
// )

function convertStock() {
//   if (!canConvert.value) return

  const from = selectedStock.value.units.find(u => u.id === form.fromUnit)
  const to = selectedStock.value.units.find(u => u.id === form.toUnit)
  if (!from || !to) {
    result.value = null
    return
  }
  form.post(route('stock_conversion.store'),{
    preserveScroll:true,
    preserveUrl:true,
    onProgress(){

    },
    onSuccess(){

    }
  })

}
</script>
