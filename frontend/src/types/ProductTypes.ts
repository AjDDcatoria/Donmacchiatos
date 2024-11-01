import type { Ref } from 'vue'
import type { FormOrderTypes } from './inputs'

export interface ProductsTypes {
  id: string
  image_url: string
  name: string
  price: number
}

export interface ProductCartTypes extends ProductsTypes {
  quantity?: number
}

export type UseProductTypes = {
  orderProduct: (formData: FormOrderTypes) => Promise<void>
  errorMessage: Ref<string | null>
  successMessage: Ref<string | null>
}

export type SelectedPaymentTypes = {
  name: string
  code: string
}
