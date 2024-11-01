<script setup lang="ts">
import Card from 'primevue/card'
import Button from 'primevue/button'
import { onMounted, ref } from 'vue'
import { useProductStore } from '@/stores/productStore'
import { getAllProducts } from '@/services/api/productService'
import type { ProductsTypes, SelectedPaymentTypes } from '@/types/ProductTypes'
import { useToast } from 'vue-toastification'
import Dialog from 'primevue/dialog'
import Select from 'primevue/select'
import FloatLabel from 'primevue/floatlabel'
import Textarea from 'primevue/textarea'
import CartList from '@/components/ui/list/CartList.vue'
import { useProduct } from '@/composable/useProduct'
import type { FormOrderTypes } from '@/types/inputs'

const products = ref<ProductsTypes[]>()
const visibleDialog = ref<boolean>(false)
const selectedPayment = ref<SelectedPaymentTypes>()
const orderMessage = ref<string>('')
const selectPayment = ref<SelectedPaymentTypes[]>([
  { name: 'Cash on delivery', code: 'COD' },
])

const productsStore = useProductStore()
const toast = useToast()
const { orderProduct, errorMessage, successMessage } = useProduct()

onMounted(async () => {
  const _products = productsStore.getProducts()

  if (!_products.length) {
    const { payload, error } = await getAllProducts()

    if (!error && payload && Array.isArray(payload)) {
      productsStore.storeAllProducts(payload as ProductsTypes[])
    } else {
      toast.error("Something wen't wrong!")
    }
  }
  products.value = productsStore.getProducts()
})

const placeOrder = async () => {
  const productsInCart = productsStore.getCartSize()
  if (productsInCart) {
    visibleDialog.value = true
  }
}

const handleOrder = async () => {
  const formData: FormOrderTypes = {
    cart: productsStore.getCart(),
    payment: selectedPayment.value,
    message: orderMessage.value,
  }
  visibleDialog.value = false

  await orderProduct(formData)

  if (errorMessage.value) {
    toast.error(errorMessage.value)
    errorMessage.value = null
    return
  }

  if (successMessage.value) {
    toast.success(successMessage.value)
    successMessage.value = null
    productsStore.clearCart()
    return
  }
}
</script>

<template>
  <div class="w-full py-4 px-5 flex gap-3 justify-center relative">
    <div class="flex gap-4 w-[76rem] relative">
      <div class="flex flex-col gap-5 w-full">
        <div class="flex flex-col gap-4">
          <div>
            <span class="text-xl font-bold">Explore our Menu</span>
          </div>
          <div>
            <Button
              label="Milktea"
              class="!rounded-full"
              severity="warn"
              size="small"
            />
          </div>
        </div>
        <div class="grid gap-5 w-full max-w-[56rem]">
          <Card
            v-for="product in products"
            :key="product.id"
            class="w-full overflow-hidden product-card"
          >
            <template #header>
              <div
                class="h-[16rem] sm:h-[12rem] overflow-hidden product-header"
              >
                <img
                  :src="product.image_url"
                  class="object-cover h-full w-full"
                />
              </div>
            </template>
            <template #title> {{ product.name }} </template>
            <template #content>
              <div class="flex gap-2 items-enter w-full product-footer">
                <div class="w-1/2">
                  <span class="text-lg font-bold text-green-600 product-price">
                    ₱{{ product.price.toFixed(2) }}
                  </span>
                </div>
                <Button
                  @click="productsStore.addToCart(product)"
                  label="Add cart"
                  class="w-1/2 h-9"
                  outlined
                />
              </div>
            </template>
          </Card>
        </div>
      </div>
      <Card
        id="cart-sidebar"
        class="sticky !top-16 right-0 h-[37rem] w-[30rem]"
      >
        <template #title> My cart </template>
        <template #content>
          <div id="cart-content" class="h-[26rem] pt-5">
            <CartList class="!h-[25rem] cart-list" />
          </div>
        </template>
        <template #footer>
          <div>
            <div
              class="h-12 text-xl font-bold flex items-center justify-between"
            >
              <div>Total</div>
              <span class="text-green-600">
                ₱{{ productsStore.getTotalPrice() }}
              </span>
            </div>
            <Button label="Place order" class="w-full" @click="placeOrder" />
          </div>
        </template>
      </Card>
    </div>
  </div>
  <Dialog
    v-model:visible="visibleDialog"
    header="Place order"
    modal
    class="w-[25rem]"
  >
    <div class="flex flex-col gap-2 h-[16rem]">
      <div class="flex flex-col gap-3">
        <Select
          v-model="selectedPayment"
          :options="selectPayment"
          optionLabel="name"
          placeholder="Payment method"
          checkmark
          :highlightOnSelect="false"
          class="w-full"
        />
        <FloatLabel variant="in">
          <Textarea
            id="over_label"
            v-model="orderMessage"
            rows="5"
            cols="30"
            style="resize: none"
            class="w-full"
          />
          <label for="in_label">Message</label>
        </FloatLabel>
      </div>
      <div>
        <Button
          label="Order!"
          severity="info"
          class="float-right"
          @click="handleOrder"
        />
      </div>
    </div>
  </Dialog>
</template>

<style scoped>
div.grid {
  grid-template-columns: repeat(auto-fit, minmax(14rem, 0.4fr));
}

@media (max-width: 1300px) {
  #cart-sidebar {
    height: 33rem;
  }

  #cart-content {
    height: 22rem;
  }
  .cart-list {
    height: 21rem !important;
  }
}

@media (max-width: 751px) {
  div.grid {
    grid-template-columns: repeat(auto-fit, minmax(14rem, 0.8fr));
  }
}

@media (max-width: 510px) {
  div.grid {
    grid-template-columns: repeat(auto-fit, minmax(13rem, 1fr));
  }
}

@media (max-width: 1200px) {
  #cart-sidebar {
    display: none;
  }
  div.grid {
    max-width: 100%;
  }
}
</style>
