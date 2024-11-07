<script setup lang="ts">
import { useProduct, type OrderTypes } from '@/composable/useProduct'
import { useProductStore } from '@/stores/productStore'
import Button from 'primevue/button'
import Card from 'primevue/card'
import Dialog from 'primevue/dialog'
import OverlayBadge from 'primevue/overlaybadge'
import { onMounted, ref } from 'vue'

const { fetchOrder } = useProduct()
const ordersRef = ref<OrderTypes[]>()
const productStore = useProductStore()
const windowWidth = ref<number>(0)
const visibleDialogOrder = ref<boolean>(false)
const selectedOrder = ref<OrderTypes>()

/**
 * Event listener to update `windowWidth` whenever the window is resized.
 *
 * @event window.addEventListener('resize')
 * Updates the reactive `windowWidth` variable with the current window width
 * whenever a resize event occurs.
 */
window.addEventListener('resize', () => {
  windowWidth.value = window.innerWidth
})

/**
 * Lifecycle hook that runs when the component is mounted.
 *
 * Sets the initial `windowWidth` to the current window width, checks if any
 * orders are already stored in `productStore`, and if none are found,
 * fetches all customer orders and updates the `ordersRef` reactive variable
 * with the fetched orders.
 *
 * @async
 * @returns {Promise<void>}
 */
onMounted(async () => {
  windowWidth.value = window.innerWidth

  if (!productStore.getOrderSize()) {
    await fetchOrder('customer', 'all')
  }
  ordersRef.value = productStore.getOrders()
})

/**
 * Opens a dialog displaying details of the specified order.
 *
 * @function openOrderDialog
 * Sets the `selectedOrder` reactive variable to the provided `order`
 * and changes `visibleDialogOrder` to true to make the dialog visible.
 *
 * @param {OrderTypes} order - The order object to be displayed in the dialog.
 * @returns {void}
 */
const openOrderDialog = (order: OrderTypes): void => {
  selectedOrder.value = order
  visibleDialogOrder.value = true
}
</script>

<template>
  <div class="h-svh flex justify-center">
    <div class="max-w-[78rem] pt-5 h-full w-full px-5 card pb-5">
      <Card class="h-full">
        <template #title>
          <div class="flex items-center gap-3">
            <Button icon="pi pi-shopping-bag" aria-label="Filter" size="small" severity="warn"/>
            <p>My Orders</p>
          </div>
        </template>
        <template #content>
          <table class="w-full">
            <thead class="h-10">
              <tr>
                <th></th>
                <th class="text-left">Name</th>
                <th v-if="windowWidth > 900">
                  <td>Message</td>
                </th>
                <th v-if="windowWidth > 550">Quantity</th>
                <th v-if="windowWidth > 700">Total</th>
                <th class="text-center">Option</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="order in ordersRef"
                :key="order.order_id"
                v-on:click="openOrderDialog(order)"
                class="h-20 border-b border-gray-300/40 cursor-pointer"
              >
                <td class="w-[100px]">
                  <div class="w-fit rounded-md">
                    <OverlayBadge
                      v-if="order.order_items.length > 1" :value="order.order_items.length - 1"
                      size="small"
                      severity="danger"
                    />
                    <img
                      :src="`${order.order_items[0].product.image_url}`"
                      class="h-16 w-20 object-cover rounded-md"
                    />
                  </div>
                </td>
                <td>
                  <div class="h-20 pt-2 leading-3">
                    <div class="font-bold text-sm">
                      {{ order.order_items[0].product.name }}
                      <span class="text-green-600">
                        ₱{{ order.order_items[0].product.price }}
                      </span>
                    </div>
                    <div class="text-sm font-semibold flex gap-2">
                      <span>{{ order.status }}</span>
                    </div>
                  </div>
                </td>
                <td th v-if="windowWidth > 900">{{ order.message ?? 'None' }}</td>
                <th v-if="windowWidth > 550">{{ order.order_items[0].quantity }}</th>
                <th v-if="windowWidth > 700">₱{{ order.grand_total }}</th>
                <th>
                  <Button
                    icon="pi pi-ellipsis-v"
                    severity="secondary"
                    rounded
                    aria-label="Option"
                  />
                </th>
              </tr>
            </tbody>
          </table>
        </template>
      </Card>
    </div>
    <Dialog v-model:visible="visibleDialogOrder" header="Order items" modal class="max-w-[30rem] w-full">
      <div class="mb-2 pb-2 border-b border-gray-300/30">
        <Button
          :label="selectedOrder?.status"
          size="small"
          rounded
          :severity="selectedOrder?.status === 'accepted' ? 'success' :
                    selectedOrder?.status === 'canceled' ||
                    selectedOrder?.status === 'declined' ? 'danger' :
                    'warn'"
        />
      </div>
      <div class="max-h-[20rem] max-w-[28rem] overflow-y-auto">
        <table class="w-full h-full">
        <thead>
          <th></th>
          <th>
            <td class="px-2">Name</td>
          </th>
          <th>Quantity</th>
          <th>Total</th>
        </thead>
        <tbody>
          <tr v-for="item in selectedOrder?.order_items" :key="item.id" class="h-16">
            <td class="w-[4.5rem]">
              <img
                :src="`${item.product.image_url}`"
                class="h-14 w-16 object-cover rounded-md"
                />
            </td>
            <td class="px-2 text-sm">
              <div class="h-14">
                <p class="font-bold">{{ item.product.name }} <br>
                  <span class="text-green-600 font-semibold">₱{{ item.product.price }}</span>
                </p>
              </div>
            </td>
            <th>{{ item.quantity }}</th>
            <th>₱{{ item.total_price }}</th>
          </tr>
        </tbody>
        </table>
      </div>
      <div class="flex justify-between items-center h-14 px-1 font-bold text-xl">
        <p>Payment ({{ selectedOrder?.payment.toUpperCase() }})</p>
        <label class="font-bold text-green-600">₱{{ selectedOrder?.grand_total }}</label>
      </div>
      <div>
        <div class="float-right">
          <Button
            :label="selectedOrder?.status === 'canceled' ? 'Re order' : 'Cancel order'"
            :severity="selectedOrder?.status === 'canceled' ? 'success' : 'danger'"
            :disabled="selectedOrder?.status === 'declined' || selectedOrder?.status == 'accepted'"
          />
        </div>
      </div>
    </Dialog>
  </div>
</template>
