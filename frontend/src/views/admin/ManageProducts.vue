<script setup lang="ts">
import Card from 'primevue/card'
import Button from 'primevue/button'
import { onMounted, reactive, ref } from 'vue'
import { DefaultProducts } from '@/types/defaults'
import { addProducts, getAllProducts } from '@/services/api/productService'
import type { ProductsTypes } from '@/types/ProductTypes'
import type { ProductInputsTypes } from '@/types/inputs'
import { useProductStore } from '@/stores/productStore'
import { useToast } from 'vue-toastification'
import ProductDialog from '@/components/ui/dialog/ProductDialog.vue'
import DialogImage from '@/components/ui/dialog/DialogImage.vue'
import InputNumber from 'primevue/inputnumber'
import InputText from 'primevue/inputtext'

const visibleAddProductDialog = ref<boolean>(false) // Add product Dialog ref
const productInput = reactive<ProductInputsTypes>({ ...DefaultProducts })
const productImage = ref<string | null>(null)
const products = ref<ProductsTypes[]>()

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
      toast.error("Something went wrong!")
    }
  }

  // Set the local variable 'products' to the products stored in the product store
  products.value = productStore.getProducts()
})

// Removed all inputs and product image
const removeInput = (): void => {
  productInput.name = ''
  productInput.image = null
  productInput.price = 0
  productImage.value = null
  visibleAddProductDialog.value = false
}

// Asynchronous function to handle the addition of a new product
const handleAddProduct = async (): Promise<void> => {
  // Close the dialog for adding a new product
  visibleAddProductDialog.value = false

  // Attempt to add the product using the addProducts API function
  const { payload, error } = await addProducts(productInput)

  // Check if there was no error and that the payload exists
  if (!error && payload) {
    // If the product was added successfully, store the new product in the product store
    productStore.addProducts(payload.data as ProductsTypes)
    // Display a success message to the user
    toast.success(payload.message)
  } else {
    // If there was an error, display an error message to the user
    toast.error("Something went wrong!")
  }

  // Clear the input fields and reset the product data
  removeInput()
}

// Function to handle file input changes, specifically for product image uploads
function onFileChange(event: Event): void {
  // Cast the event target to an HTMLInputElement to access its properties
  const target = event.target as HTMLInputElement
  // Retrieve the first file from the input's files (if any)
  const file = target.files?.[0]

  // Check if a file was selected
  if (file) {
    // Store the selected file in the product object for further processing
    productInput.image = file

    // Create a new FileReader instance to read the file's data
    const reader = new FileReader()

    // Define the onload callback for when the file has been read successfully
    reader.onload = () => {
      // Set the productImage reactive variable to the file's data URL (base64 encoded string)
      productImage.value = reader.result as string
    }
    // Initiate reading of the file as a Data URL
    reader.readAsDataURL(file)
  } else {
    // If no file was selected, reset the product image and product object
    productInput.image = null
    productImage.value = null
  }
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
          v-for="coffee in products"
          :key="coffee.id"
          class="w-full overflow-hidden"
        >
          <template #header>
            <div class="h-[12rem] overflow-hidden">
              <img
                :src="coffee?.image_url"
                class="object-cover h-full w-full"
                alt="coffee.png"
              />
            </div>
          </template>
          <template #title>{{ coffee.name }} </template>
          <template #footer>
            <div class="flex items-enter w-full">
              <div class="w-1/2">
                <span class="text-xl font-bold text-green-600">
                  ₱{{ coffee.price }}
                </span>
              </div>
              <Button label="Edit" class="w-1/2 h-9 text-xs" outlined />
            </div>
          </template>
        </Card>
      </div>
    </div>

    <!-- Add product dialog -->
    <ProductDialog
      v-model:visible="visibleAddProductDialog"
      header="Add Product"
      class="!flex flex-col"
    >
      <template #Image>
        <DialogImage
          :onDelete="() => (productImage = null)"
          :productImage="productImage"
          :onChange="onFileChange"
        />
      </template>
      <template #Input>
        <div class="w-full flex flex-col gap-4">
          <div class="flex flex-col gap-2">
            <label for="product-name">Product name</label>
            <InputText
              id="product-name"
              v-model="productInput.name"
              aria-describedby="username-help"
            />
            <small>Enter your new product name.</small>
          </div>
          <div class="flex flex-col gap-2">
            <label for="product-price">Product price</label>
            <InputNumber
              id="product-price"
              v-model="productInput.price"
              inputId="currency-ph"
              mode="currency"
              currency="PHP"
              locale="en-US"
              fluid
            />
            <small>Enter your product price.</small>
          </div>
        </div>
      </template>
      <template #Control>
        <Button
          label="Cancel"
          severity="danger"
          class="w-20"
          @click="removeInput"
        />
        <Button
          label="Add"
          severity="info"
          class="w-20"
          @click="handleAddProduct"
        />
      </template>
    </ProductDialog>
  </div>
</template>

<style scoped>
div.grid-layout {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(14rem, 0.5fr));
  grid-template-rows: auto;
}
</style>
