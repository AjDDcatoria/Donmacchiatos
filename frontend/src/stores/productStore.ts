import { defineStore } from 'pinia'
import type { ProductCartTypes, ProductsTypes } from '@/types/ProductTypes'

export const useProductStore = defineStore('products', {
  state: () => ({
    products: [] as ProductsTypes[] | undefined,
    cart: [] as ProductCartTypes[] | undefined,
  }),

  actions: {
    storeAllProducts(products: ProductsTypes[]) {
      this.products = products
    },

    getProducts(): ProductsTypes[] {
      return this.products || []
    },

    addProducts(product: ProductsTypes) {
      if (!this.products) {
        this.products = []
      }
      this.products.unshift(product)
    },

    addToCart(product: ProductCartTypes) {
      if (!this.cart) {
        this.cart = []
      }

      const existed = this.cart?.some(item => item.id == product.id)

      if (!existed) {
        product.quantity = 1
        this.cart.unshift(product)
      }
    },

    getCart(): ProductCartTypes[] {
      return this.cart || []
    },

    updateQuantity(id: string, operation: '-' | '+') {
      this.cart?.map(product => {
        if (product.id == id && product.quantity) {
          if (operation == '+') {
            product.quantity += 1
          } else {
            if (product.quantity <= 1) {
              this.cart = this.cart?.filter(product => product.id !== id)
            } else {
              product.quantity -= 1
            }
          }
        }
      })
    },

    getCartSize(): number {
      return this.cart?.length || 0
    },

    getTotalPrice(): string {
      let total = 0

      this.cart?.forEach(item => {
        if (item.quantity && item.price) {
          total += item.quantity * item.price
        }
      })

      return total.toFixed(2)
    },

    clearCart(): void {
      this.cart = undefined
    },
  },
})
