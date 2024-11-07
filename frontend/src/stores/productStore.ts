import { defineStore } from 'pinia'
import type { ProductCartTypes, ProductsTypes } from '@/types/ProductTypes'
import type { OrderTypes } from '@/composable/useProduct'

export const useProductStore = defineStore('products', {
  state: () => ({
    products: [] as ProductsTypes[],
    cart: [] as ProductCartTypes[],
    orders: [] as OrderTypes[],
  }),

  actions: {
    storeAllProducts(products: ProductsTypes[]) {
      this.products = products
    },

    getProducts(): ProductsTypes[] {
      return this.products
    },

    addProducts(product: ProductsTypes) {
      this.products.unshift(product)
    },

    editProduct(newProduct: ProductsTypes): void {
      const index = this.products.findIndex(p => p.id === newProduct.id)
      if (index !== -1) {
        this.products[index] = { ...this.products[index], ...newProduct }
      }
    },

    addToCart(product: ProductCartTypes) {
      const existed = this.cart?.some(item => item.id == product.id)

      if (!existed) {
        product.quantity = 1
        this.cart.unshift(product)
      }
    },

    getCart(): ProductCartTypes[] {
      return this.cart
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
      return this.cart?.length
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

    setOrders(orders: OrderTypes[]): void {
      this.orders = orders
    },

    getOrders(): OrderTypes[] {
      return this.orders
    },

    getOrderSize(): number {
      return this.orders.length
    },

    clearCart(): void {
      this.cart = []
    },
  },
})
