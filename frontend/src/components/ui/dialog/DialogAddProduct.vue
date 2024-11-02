<script setup lang="ts">
import { addProducts } from '@/services/api/productService'
import { useProductStore } from '@/stores/productStore'
import type { DefaultProps } from '@/types/components'
import { DefaultProducts } from '@/types/defaults'
import type { ProductInputsTypes } from '@/types/inputs'
import type { ProductsTypes } from '@/types/ProductTypes'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import InputNumber from 'primevue/inputnumber'
import InputText from 'primevue/inputtext'
import { ref } from 'vue'
import { useToast } from 'vue-toastification'

const props = defineProps<DefaultProps>()
const emit = defineEmits(['update:visible'])
const productInput = ref<ProductInputsTypes>({ ...DefaultProducts })
const productImage = ref<string | null>(null)

const toast = useToast()
const productStore = useProductStore()

function onFileChange(event: Event): void {
  // Cast the event target to an HTMLInputElement to access its properties
  const target = event.target as HTMLInputElement
  // Retrieve the first file from the input's files (if any)
  const file = target.files?.[0]

  // Check if a file was selected
  if (file) {
    // Store the selected file in the product object for further processing
    productInput.value.image = file

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
    productInput.value.image = null
    productImage.value = null
  }
}

// Asynchronous function to handle the addition of a new product
const handleAddProduct = async (): Promise<void> => {
  // Attempt to add the product using the addProducts API function
  const { payload, error } = await addProducts(productInput.value)

  // Check if there was no error and that the payload exists
  if (!error && payload) {
    // If the product was added successfully, store the new product in the product store
    productStore.addProducts(payload.data as ProductsTypes)
    // Display a success message to the user
    toast.success(payload.message)

    // Set Inputs to defaults
  } else {
    // If there was an error, display an error message to the user
    toast.error('Something went wrong!')
  }
  // close the dialog after the process
  closeDialog()
}

// Remove or set the input value to default then close the dialog
const closeDialog = () => {
  productInput.value = DefaultProducts
  productImage.value = null
  emit('update:visible', false)
}
</script>

<template>
  <Dialog modal :class="[props.class, 'w-[40rem] h-[25rem]']">
    <div class="flex flex-col gap-5">
      <div class="flex gap-6 h-[14rem]">
        <!-- Image -->
        <label
          for="dropzone-file"
          :class="[
            productImage ? '' : 'border-2 border-gray-300/80',
            'flex flex-col relative items-center w-[26rem] justify-center  border-dashed rounded-lg cursor-pointer overflow-hidden',
          ]"
        >
          <Button
            v-if="productImage"
            severity="secondary"
            icon="pi pi-times"
            aria-label="delete"
            @click="productImage = null"
            class="!absolute top-1 right-1 z-10 !h-8 !w-8"
            rounded
          />
          <img
            v-if="productImage"
            :src="productImage"
            class="h-full w-full object-cover"
          />
          <div
            v-else
            class="flex flex-col items-center justify-center pt-5 pb-6"
          >
            <svg
              class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 20 16"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
              />
            </svg>
            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
              <span class="font-semibold">Click to upload</span>
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG</p>
          </div>
          <input
            id="dropzone-file"
            type="file"
            class="hidden"
            @change="onFileChange"
            accept="image/*"
          />
        </label>
        <!-- Inputs -->
        <div class="w-full flex flex-col gap-4">
          <div class="flex flex-col gap-2">
            <label for="product-name">Product name</label>
            <InputText
              id="product-name"
              aria-describedby="username-help"
              v-model="productInput.name"
            />
            <small>Enter your new product name.</small>
          </div>
          <div class="flex flex-col gap-2">
            <label for="product-price">Product price</label>
            <InputNumber
              id="product-price"
              inputId="currency-ph"
              mode="currency"
              currency="PHP"
              locale="en-US"
              v-model="productInput.price"
              fluid
            />
            <small>Enter your product price.</small>
          </div>
        </div>
      </div>
      <div>
        <div class="flex gap-3 float-right">
          <Button
            label="Cancel"
            severity="danger"
            class="w-20"
            @click="closeDialog"
          />
          <Button
            label="Add"
            severity="info"
            class="w-20"
            @click="handleAddProduct"
          />
        </div>
      </div>
    </div>
  </Dialog>
</template>
