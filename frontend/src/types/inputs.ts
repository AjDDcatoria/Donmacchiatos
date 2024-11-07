import type { ProductCartTypes, SelectedPaymentTypes } from './ProductTypes'

export interface VerificationTypes {
  email: string
  verification_code?: string
  verification_id?: string | null
}

export interface ProductInputsTypes {
  id?: string
  image?: File | null
  name: string
  price: number
}

export type FormOrderTypes = {
  cart: ProductCartTypes[]
  message: string
  payment: SelectedPaymentTypes | string | undefined
}
