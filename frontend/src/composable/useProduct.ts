import { createOrder, editProduct } from '@/services/api/productService'
import { useProductStore } from '@/stores/productStore'
import type { FormOrderTypes } from '@/types/inputs'
import type { ProductsTypes } from '@/types/ProductTypes'
import { AxiosError } from 'axios'
import { ref, type Ref } from 'vue'

type UseProductTypes = {
  orderProduct: (formData: FormOrderTypes) => Promise<void>
  updateProduct: (formData: ProductsTypes) => Promise<void>
  errorMessage: Ref<string | null>
  successMessage: Ref<string | null>
}

// The `useProduct` composable provides methods and reactive states to manage product-related operations
export function useProduct(): UseProductTypes {
  const errorMessage = ref<string | null>(null)
  const successMessage = ref<string | null>(null)
  const productStore = useProductStore()

  /**
   * `orderProduct` - Function to place an order for a product
   * Takes order data and sends it to the `createOrder` API. If successful, sets a success message;
   * otherwise, handles and displays any errors encountered.
   *
   * @param {FormOrderTypes} formData - The data required to place an order
   * @returns {Promise<void>} Resolves when the function completes
   */
  const orderProduct = async (formData: FormOrderTypes): Promise<void> => {
    // Send order data to the API, destructuring the result into payload (on success) and errors
    const { payload, error: errors } = await createOrder(formData)

    // Check if an error occurred during the API call
    if (errors) {
      // If the error is an AxiosError, process it
      if (errors instanceof AxiosError) {
        const _errors = errors.response?.data.errors as Record<string, string[]> // Extract error details

        // Loop through each error entry to update the error message with the first encountered error
        for (const message of Object.entries(_errors)) {
          message.forEach(message => {
            errorMessage.value = message[0] // Set the first error message found to errorMessage
          })
          return // Exit the loop after setting the error message
        }
      }
    }

    // If payload exists, the request was successful, so set the success message
    if (payload) {
      successMessage.value = payload.message
      return // Exit function after handling success
    }

    // Set a default error message if something went wrong and no payload was receive
    errorMessage.value = 'Something went wrong try again later!'
  }

  /**
   * `updateProduct` - Function to update product details
   * Sends updated product data to the `editProduct` API. If successful, updates the product store
   * and displays a success message; otherwise, handles any errors.
   *
   * @param {ProductsTypes} formData - The data required to update the product
   * @returns {Promise<void>} Resolves when the function completes
   */
  const updateProduct = async (formData: ProductsTypes): Promise<void> => {
    // Send updated product data to the API, destructuring the result into payload (on success) and errors
    const { payload, error: errors } = await editProduct(formData)

    // Check if an error occurred during the API call
    if (errors) {
      // If the error is an AxiosError, process it
      if (errors instanceof AxiosError) {
        const _errors = errors.response?.data.errors as Record<string, string[]> // Extract error details

        // Loop through each error entry to update the error message with the first encountered error
        for (const message of Object.entries(_errors)) {
          message.forEach(message => {
            errorMessage.value = message[0] // Set the first error message found to errorMessage
          })
          return // Exit the loop after setting the error message
        }
      }
    }
    // If payload exists, the request was successful
    if (payload) {
      productStore.editProduct(payload.data) // Update the product store with new product data
      successMessage.value = payload.message // Assign success message from payload
    }
  }
  // Return the composable's methods and reactive variables for use in components
  return { orderProduct, errorMessage, successMessage, updateProduct }
}
