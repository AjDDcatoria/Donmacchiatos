import { createOrder } from '@/services/api/productService'
import type { FormOrderTypes } from '@/types/inputs'
import type { UseProductTypes } from '@/types/ProductTypes'
import { AxiosError } from 'axios'
import { ref } from 'vue'

export function useProduct(): UseProductTypes {
  const errorMessage = ref<string | null>(null)
  const successMessage = ref<string | null>(null)

  const orderProduct = async (formData: FormOrderTypes) => {
    const { payload, error: errors } = await createOrder(formData)

    if (errors) {
      if (errors instanceof AxiosError) {
        const _errors = errors.response?.data.errors as Record<string, string[]>

        for (const message of Object.entries(_errors)) {
          message.forEach(message => {
            errorMessage.value = message[0]
          })
          return
        }
      }
    }

    if (payload) {
      successMessage.value = payload.message
      return
    }

    errorMessage.value = "Something went wrong try again later!"
  }

  return { orderProduct, errorMessage, successMessage }
}
