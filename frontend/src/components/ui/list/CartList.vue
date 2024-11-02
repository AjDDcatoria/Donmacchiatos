<script setup lang="ts">
import { useProductStore } from '@/stores/productStore'
import Button from 'primevue/button'
import InputNumber from 'primevue/inputnumber'

const productsStore = useProductStore()
const props = defineProps<{ class?: string }>()
</script>

<template>
  <ul :class="[props.class, 'w-full h-[85%] overflow-x-auto']">
    <li
      v-for="cart in productsStore.getCart()"
      :key="cart.id"
      class="border-b border-gray-300/30"
    >
      <div class="h-[7rem] p-2 flex gap-3">
        <div class="h-full">
          <div class="h-[6rem] w-[7rem] overflow-hidden rounded-md">
            <img
              :src="cart.image_url as string"
              class="object-cover h-full w-full"
              alt="coffee png"
            />
          </div>
        </div>
        <div class="w-full">
          <div class="h-[4rem]">
            <div class="text-lg font-bold leading-5">
              {{ cart.name }} <br />
            </div>
            <p class="font-semibold text-green-600 !block sm:hidden">
              ₱{{ cart.price.toFixed(2) }}
            </p>
          </div>
          <div class="flex justify-center">
            <div class="flex gap-3">
              <Button
                label="-"
                @click="productsStore.updateQuantity(cart.id, '-')"
                rounded
                aria-label="Filter"
                class="!h-7 !w-7"
              />
              <InputNumber
                class="!w-16 !h-7"
                v-model="cart.quantity"
                :min="0"
                :max="100"
                fluid
              />
              <Button
                label="+"
                @click="productsStore.updateQuantity(cart.id, '+')"
                rounded
                aria-label="Filter"
                class="!h-7 !w-7"
              />
            </div>
          </div>
        </div>
      </div>
    </li>
  </ul>
</template>
