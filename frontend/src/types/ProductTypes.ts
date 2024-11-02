export interface ProductsTypes {
  id: string
  image_url: string | File
  name: string
  price: number
}

export interface ProductCartTypes extends ProductsTypes {
  quantity?: number
}

export type SelectedPaymentTypes = {
  name: string
  code: string
}
