<script setup lang="ts">
import { useThemeStore } from '@/stores/themeStore'
import Card from 'primevue/card'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'
import InputText from 'primevue/inputtext'
import { useProduct, type OrderTypes } from '@/composable/useProduct'
import { onMounted, ref } from 'vue'
import { useProductStore } from '@/stores/productStore'
import ProfilePicture from '@/components/ui/ProfilePicture.vue'
import Button from 'primevue/button'
import OverlayBadge from 'primevue/overlaybadge'
import Dialog from 'primevue/dialog'

const themeStore = useThemeStore()
const productStore = useProductStore()
const { fetchOrder } = useProduct()
const selectedOrder = ref<OrderTypes>()
const visibleDialogOrder = ref<boolean>(false)

onMounted(async () => {
  await fetchOrder('admin', 'pending')
  console.log(productStore.getOrders())
})
const openOrderDialog = (order: OrderTypes): void => {
  selectedOrder.value = order
  visibleDialogOrder.value = true
}
</script>

<template>
  <div>
    <Card class="!rounded-md col-span-3 overflow-hidden row-span-3">
      <template #title>
        <div class="h-full py-2 flex justify-between">
          <div>Customer Orders</div>
          <IconField class="!h-8">
            <InputText placeholder="Search" class="!h-8" />
            <InputIcon class="pi pi-search" />
          </IconField>
        </div>
      </template>
      <template #content>
        <div class="overflow-auto max-h-[32rem]">
          <table id="sales-table" class="w-full">
            <thead
              :class="[
                'z-10 sticky top-0',
                themeStore.getTheme() == 'dark' ? 'bg-[#18181b]' : 'bg-white',
              ]"
            >
              <tr>
                <td class="w-14">No.</td>
                <td>Name</td>
                <td>Payment</td>
                <td>Image</td>
                <td class="text-center w-20">Quantity</td>
                <td class="text-center">Total</td>
                <td>User</td>
                <td>Address</td>
                <td>Action</td>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(order, index) in productStore.getOrders()"
                :key="order.order_id"
                @click="openOrderDialog(order)"
                class="h-20 border-b border-gray-300/50 !text-sm cursor-pointer"
              >
                <td class="w-[1rem]">{{ index + 1 }}</td>
                <td>
                  <h2 class="font-semibold">
                    {{ order.order_items[0].product.name }}
                  </h2>
                </td>
                <td>{{ order.payment.toLocaleUpperCase() }}</td>
                <td>
                  <div class="w-fit">
                    <OverlayBadge
                      v-if="order.order_items.length > 1"
                      :value="order.order_items.length - 1"
                      size="small"
                      severity="danger"
                    />
                    <img
                      :src="`${order.order_items[0].product.image_url}`"
                      class="h-12 w-12 rounded-md"
                    />
                  </div>
                </td>
                <td class="text-center font-semibold">
                  {{ order.order_items[0].quantity }}
                </td>
                <td class="text-center font-bold text-green-600">
                  ₱{{ order.order_items[0].total_price }}
                </td>
                <td class="max-w-32">
                  <div class="h-full flex items-center gap-3">
                    <div class="h-12 w-12 overflow-hidden rounded">
                      <ProfilePicture
                        :image="order.user.avatar"
                        :alt="order.user.name"
                        class="!h-full !w-full !rounded-none"
                      />
                    </div>
                    <div>
                      <h2 class="font-semibold">{{ order.user.name }}</h2>
                    </div>
                  </div>
                </td>
                <td>Tandag city</td>
                <td class="w-[10rem]">
                  <div class="flex items-center gap-2 h-full">
                    <Button label="Accept" severity="success" size="small" />
                    <Button label="Decline" severity="danger" size="small" />
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </template>
    </Card>
    <Dialog v-model:visible="visibleDialogOrder" header="Order items" modal class="max-w-[30rem] w-full">
      <div class="mb-2 pb-2 border-b border-gray-300/30 flex flex-col gap-2 text-xs">
        <div class="flex">
          <label class="w-[4rem]">Name</label>
          <p id="name">: {{ selectedOrder?.user.name }}</p>
        </div>
        <div class="flex">
          <label class="w-[4rem]">Email</label>
          <p id="name">: {{ selectedOrder?.user.email }}</p>
        </div>
        <div class="flex">
          <label class="w-[4rem]">Address</label>
          <p id="name">: Tandag city</p>
        </div>
        <div class="flex">
          <label class="w-[4rem]">Message</label>
          <p id="name">: Hello admin this is a test message!</p>
        </div>
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
        <div class="float-right flex items-center gap-2">
          <Button label="Accept" severity="success"/>
          <Button label="Decline" severity="danger"/>
        </div>
      </div>
    </Dialog>
  </div>
</template>
