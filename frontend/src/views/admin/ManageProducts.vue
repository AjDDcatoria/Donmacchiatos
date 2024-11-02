<script setup lang="ts">
import Card from 'primevue/card'
import Button from 'primevue/button'
import { onMounted, ref } from 'vue'
import { getAllProducts } from '@/services/api/productService'
import type { ProductsTypes } from '@/types/ProductTypes'
import { useProductStore } from '@/stores/productStore'
import { useToast } from 'vue-toastification'
import DialogAddProduct from '@/components/ui/dialog/DialogAddProduct.vue'
import DialogEditProduct from '@/components/ui/dialog/DialogEditProduct.vue'

const visibleAddProductDialog = ref<boolean>(false) // Add product Dialog ref
const visibleEditProductDialog = ref<boolean>(false) // Add product Dialog ref
const products = ref<ProductsTypes[]>()
const selectedProduct = ref<ProductsTypes>({
  id: '',
  image_url: '',
  name: '',
  price: 0,
})

const productStore = useProductStore()
const toast = useToast()

onMounted(async () => {
  // Retrieve the current list of products from the product store
  const _products = productStore.getProducts()

  // Check if the product list is empty
  if (!_products.length) {
    // If there are no products, fetch all products from the API
    const { payload, error } = await getAllProducts()

    // Check for errors in the response and ensure the payload is a valid array
    if (!error && payload && Array.isArray(payload)) {
      // If the response is valid, store the fetched products in the product store
      productStore.storeAllProducts(payload as ProductsTypes[])
    } else {
      // If there was an error or invalid response, display an error message to the user
      toast.error('Something went wrong!')
    }
  }

  // Set the local variable 'products' to the products stored in the product store
  products.value = productStore.getProducts()
})

const handleEditProduct = (candidateProduct: ProductsTypes): void => {
  selectedProduct.value = candidateProduct
  visibleEditProductDialog.value = true
}
</script>

<template>
  <div class="w-full flex justify-center py-3 pr-3">
    <div class="w-full max-w-[74rem] flex flex-col gap-5">
      <div>
        <Button
          label="Add product"
          class="float-right !rounded"
          severity="contrast"
          @click="visibleAddProductDialog = true"
        />
      </div>

      <!-- Products -->
      <div class="grid-layout gap-5">
        <Card
          v-for="product in products"
          :key="product.id"
          class="w-full overflow-hidden"
        >
          <template #header>
            <div class="h-[12rem] overflow-hidden">
              <img
                :src="product?.image_url as string"
                class="object-cover h-full w-full"
                alt="product.png"
              />
            </div>
          </template>
          <template #title>{{ product.name }} </template>
          <template #footer>
            <div class="flex items-enter w-full">
              <div class="w-1/2">
                <span class="text-xl font-bold text-green-600">
                  ₱{{ product.price }}
                </span>
              </div>
              <Button
                label="Edit"
                class="w-1/2 h-9 text-xs"
                @click="handleEditProduct(product)"
                outlined
              />
            </div>
          </template>
        </Card>
      </div>
    </div>

    <!-- Add product dialog -->
    <DialogAddProduct
      header="Add product"
      v-model:visible="visibleAddProductDialog"
    />

    <!-- Edit product dialog -->
    <DialogEditProduct
      v-if="visibleEditProductDialog"
      header="Edit product"
      :product="selectedProduct"
      v-model:visible="visibleEditProductDialog"
    />
  </div>
</template>

<style scoped>
div.grid-layout {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(14rem, 0.5fr));
  grid-template-rows: auto;
}
</style>
