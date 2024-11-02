<script setup lang="ts">
import { useProduct } from '@/composable/useProduct'
import type { DefaultProps } from '@/types/components'
import type { ProductsTypes } from '@/types/ProductTypes'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import InputNumber from 'primevue/inputnumber'
import InputText from 'primevue/inputtext'
import { onMounted, reactive, ref } from 'vue'
import { useToast } from 'vue-toastification'

interface DialogEditProps extends DefaultProps {
  product: ProductsTypes
}

const props = defineProps<DialogEditProps>()
const emit = defineEmits(['update:visible'])
const imageUrl = ref<string | null>(null)
const productData = reactive<ProductsTypes>({ ...props.product })

const toast = useToast()
const { updateProduct, successMessage, errorMessage } = useProduct()

// Initialize product image on mount
onMounted(() => {
  imageUrl.value = props.product.image_url as string
})

/**
 * `handleEditProduct` - Function to handle the product edit process
 * Calls `updateProduct` with `productData`, displays appropriate notifications based on success or failure,
 * and then closes the edit dialog after the process
 *
 * @async
 * @returns {Promise<void>} Resolves when the function completes
 */
const handleEditProduct = async (): Promise<void> => {
  // Call `updateProduct` with `productData` to send the updated product details to the API
  await updateProduct(productData)

  // Check if `successMessage` contains a message, indicating a successful update
  if (successMessage.value) {
    toast.success(successMessage.value)
    successMessage.value = null
  }

  // Check if `errorMessage` contains a message, indicating an error during the update process
  if (errorMessage.value) {
    toast.error(errorMessage.value)
    errorMessage.value = null
  }
  // Emit an event to signal that the edit dialog should be closed
  emit('update:visible', false)
}

function onFilechange(event: Event): void {
  // Cast the event target to an HTMLInputElement to access its properties
  const target = event.target as HTMLInputElement
  // Retrieve the first file from the input's files (if any)
  const file = target.files?.[0]
  // Check if a file was selected
  if (file) {
    // Create a new FileReader instance to read the file's data
    const reader = new FileReader()
    productData.image_url = file as File

    // Define the onload callback for when the file has been read successfully
    reader.onload = () => {
      // Set the productImage reactive variable to the file's data URL (base64 encoded string)
      imageUrl.value = reader.result as string
    }
    // Initiate reading of the file as a Data URL
    reader.readAsDataURL(file)
  } else {
    // If no file was selected, reset the product image and product object
    imageUrl.value = null
    productData.image_url = ''
  }
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
            imageUrl ? '' : 'border-2 border-gray-300/80',
            'flex flex-col relative items-center w-[26rem] justify-center  border-dashed rounded-lg cursor-pointer overflow-hidden',
          ]"
        >
          <Button
            v-if="imageUrl"
            severity="secondary"
            icon="pi pi-times"
            aria-label="delete"
            @click="imageUrl = null"
            class="!absolute top-1 right-1 z-10 !h-8 !w-8"
            rounded
          />
          <img
            v-if="imageUrl"
            :src="imageUrl"
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
            @change="onFilechange"
            accept="image/*"
          />
        </label>
        <!-- Inputs -->
        <div class="w-full flex flex-col gap-4">
          <div class="flex flex-col gap-2">
            <label for="product-name">Product name</label>
            <InputText
              id="product-name"
              v-model="productData.name"
              aria-describedby="username-help"
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
              v-model="productData.price"
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
            @click="emit('update:visible', false)"
          />
          <Button
            label="Edit"
            severity="info"
            class="w-20"
            @click="handleEditProduct"
          />
        </div>
      </div>
    </div>
  </Dialog>
</template>
