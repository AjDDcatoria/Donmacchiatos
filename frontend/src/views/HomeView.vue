<script setup lang="ts">
import BaseLayout from '@/components/partials/BaseLayout.vue'
import HeaderView from '@/components/partials/HeaderView.vue'
import { onMounted, onUnmounted, ref, watch } from 'vue'
import Drawer from 'primevue/drawer'
import Button from 'primevue/button'
import ToggleSwitch from 'primevue/toggleswitch'
import { usePrimeVue } from 'primevue/config'
import { useThemeStore } from '@/stores/themeStore'
import Badge from 'primevue/badge'
import { useRoute, useRouter } from 'vue-router'
import SidebarView from '@/components/partials/SidebarView.vue'
import { adminNavigations, customerNavigations } from '@/lib/constans/navlist'
import type { NavigationTypes } from '@/types/components'
import { darkHeaderClass, lightHeaderClass } from '@/lib/constans/classlist'
import { useUserStore } from '@/stores/userStore'
import ProfilePicture from '@/components/ui/ProfilePicture.vue'
import AppTitle from '@/components/ui/AppTitle.vue'
import SpeedDial from 'primevue/speeddial'
import { useProductStore } from '@/stores/productStore'
import CartList from '@/components/ui/list/CartList.vue'
import type { FormOrderTypes } from '@/types/inputs'
import type { SelectedPaymentTypes } from '@/types/ProductTypes'
import { useProduct } from '@/composable/useProduct'
import { useToast } from 'vue-toastification'
import Dialog from 'primevue/dialog'
import Select from 'primevue/select'
import FloatLabel from 'primevue/floatlabel'
import Textarea from 'primevue/textarea'
import { useAuth } from '@/composable/useAuth'

const router = useRouter()
const route = useRoute()
const themeStore = useThemeStore()
const currentTheme = usePrimeVue()
const visibleRight = ref<boolean>(false)
const visibleBottom = ref<boolean>(false)
const selectedPayment = ref<SelectedPaymentTypes>()
const visibleDialog = ref<boolean>(false)
const orderMessage = ref<string>('')
const dark_mode = ref<boolean>(false)
const userRole = ref<string>('')
const navigationList = ref<NavigationTypes[]>(customerNavigations)
const items = ref([
  {
    label: 'Cart',
    icon: 'pi pi-shopping-cart',
    command: () => (visibleBottom.value = true),
  },
  {
    label: 'Orders',
    icon: 'pi pi-shopping-bag',
    command: () => console.log('Open Orders'),
  },
])
const selectPayment = ref<SelectedPaymentTypes[]>([
  { name: 'Cash on delivery', code: 'cod' },
])

const userStore = useUserStore()
const productsStore = useProductStore()
const toast = useToast()
const { orderProduct, errorMessage, successMessage } = useProduct()
const { requestLogout, message } = useAuth()

onMounted(() => {
  // Set userRole if null set empty string.
  userRole.value = userStore.getUserRole() || ''

  // Scroll event to handle header theme.
  document.addEventListener('scroll', updateHeaderOnScroll)

  // Set Navigation list for sidebar drawer.
  navigationList.value = customerNavigations

  /**
   * Check if the current user is admin
   * - if true add the dashboard navigation list.
   * - else regular navigation list for customer role.
   * */
  if (userRole.value === 'admin') {
    navigationList.value = [
      ...customerNavigations.slice(0, 1),
      adminNavigations[0],
      ...customerNavigations.slice(1),
    ]
  }
})

onUnmounted(() => {
  document.removeEventListener('scroll', updateHeaderOnScroll)
})

// Watch switching themes.
watch(dark_mode, isDarkMode => {
  // Set the switched theme from options in PRESET.
  currentTheme.config.theme.options.darkModeSelector = isDarkMode

  // Set theme to store to access to entire code base.
  themeStore.setTheme(isDarkMode ? 'dark' : 'light')

  document.body.classList.toggle('bg-slate-100', !isDarkMode)

  setHeaderStyles(isDarkMode)
})

// Handle toggle or switching theme light to dark <->.
const toggleDarkMode = () => {
  const header = document.querySelector('header') as HTMLElement

  dark_mode.value = !dark_mode.value

  if (!dark_mode.value) {
    header.classList.remove(...darkHeaderClass)
  }
}

// Set Header styles base on the current theme.
const setHeaderStyles = (isDarkMode: boolean) => {
  const header = document.querySelector('header') as HTMLElement

  // Check if the page is scrolled
  if (window.scrollY > 0) {
    // If true set Header to medium transparent.
    header.classList.toggle('dark-mode', isDarkMode)
    header.classList.add(...(isDarkMode ? darkHeaderClass : lightHeaderClass))
  } else {
    // else remove the styles.
    header.classList.remove(...darkHeaderClass, ...lightHeaderClass)
  }
}

const updateHeaderOnScroll = () => setHeaderStyles(dark_mode.value)

/**
 * Checks if there are products in the cart, and if so, opens the order confirmation dialog.
 */
const placeOrder = async () => {
  const productsInCart = productsStore.getCartSize()
  if (productsInCart) {
    visibleDialog.value = true
  }
}

/**
 * Handles the order submission by creating the form data and sending it.
 * Displays success or error notifications based on the response and resets relevant states.
 */
const handleOrder = async () => {
  // Construct the form data for the order from the cart and selected options.
  const formData: FormOrderTypes = {
    cart: productsStore.getCart(),
    payment: selectedPayment.value,
    message: orderMessage.value,
  }

  // Close the confirmation dialog before submitting.
  visibleDialog.value = false

  // Send the order data to the `orderProduct` function.
  await orderProduct(formData)

  // If there's an error message, display it in a toast notification and clear the error.
  if (errorMessage.value) {
    toast.error(errorMessage.value)
    errorMessage.value = null
    return
  }

  // If the order is successful, display a success message, clear the cart, and reset order-related state.
  if (successMessage.value) {
    toast.success(successMessage.value)
    successMessage.value = null
    productsStore.clearCart()
    visibleBottom.value = false
    selectedPayment.value = undefined
    orderMessage.value = ''
    return
  }
}

/**
 * Logs the user out by calling the `requestLogout` function, clears the user store,
 * and displays a logout success message if available.
 */
const handleLogout = async () => {
  // Close the right-side drawer.
  visibleRight.value = false

  // Call the logout function and clear the user data from the store.
  await requestLogout()
  userStore.clear()

  // If there's a logout success message, display it in a toast notification.
  if (message.value) {
    toast.success(message.value)
  }
}
</script>

<template>
  <BaseLayout class="h-dvh flex justify-center relative mt-10">
    <!-- Header -->
    <HeaderView>
      <div>
        <AppTitle class="!text-lg font-normal sm:text-2xl" />
      </div>
      <div class="h-full flex items-center">
        <!-- Render profile if authenticated -->
        <div v-if="userStore.isAuthenticated()" class="flex items-center gap-3">
          <label for="drawer" class="font-semibold hidden sm:block">{{
            userStore.getFullname()
          }}</label>
          <button
            id="drawer"
            type="button"
            class="h-fit w-fit"
            @click="visibleRight = true"
          >
            <ProfilePicture
              :image="userStore.getUserAvatar()"
              :alt="userStore.getFirstname()"
            />
          </button>
        </div>

        <!-- Login btn to navigate login page -->
        <div v-else class="flex items-center gap-3">
          <Button
            label="LOGIN"
            size="small"
            class="!w-20 !rounded"
            @click="router.push({ name: 'login' })"
          />
        </div>
      </div>
    </HeaderView>
    <div class="relative w-full max-w-[84rem]">
      <!-- Admin sidebar -->
      <SidebarView
        v-if="route.name !== 'home' && userStore.getUserRole() == 'admin'"
      />

      <!-- Main content -->
      <main
        :class="[
          'mt-5',
          userStore.isAuthenticated() &&
            route.name !== 'home' &&
            userStore.getUserRole() == 'admin' &&
            'ml-[18rem]',
        ]"
      >
        <!-- Render children slot  -->
        <router-view />
        <SpeedDial
          :model="items"
          direction="up"
          class="!fixed !right-5 !bottom-5 visible sm:invisible"
          :buttonProps="{ severity: 'primary', rounded: true }"
        />
      </main>

      <!-- Side drawer  -->
      <Drawer
        v-model:visible="visibleRight"
        :header="`Hello ${userStore.getFirstname()}!`"
        position="right"
      >
        <!-- Navigation list  -->
        <nav class="h-full flex flex-col justify-between select-none">
          <ul>
            <li
              v-for="list in navigationList"
              :key="list.label"
              @click="router.push({ name: list.path })"
              class="w-full flex items-center border-b border-gray-300/30 h-12 px-2 cursor-pointer"
            >
              <span class="w-8">
                <i :class="['pi', list.icon]"></i>
              </span>
              <div
                class="flex items-center w-full justify-between h-full gap-3"
              >
                {{ list.label }}
                <Badge
                  v-if="list.badge"
                  :value="list.badge"
                  :severity="list.severity ?? 'danger'"
                  size="small"
                />
              </div>
            </li>

            <!-- Toggle theme  -->
            <li @click="toggleDarkMode" class="cursor-pointer">
              <div
                class="w-full flex items-center justify-between border-b border-gray-300/40 h-12 px-2"
              >
                <div>Dark mode</div>
                <ToggleSwitch
                  id="toggle-theme"
                  v-model="dark_mode"
                  class="!h-5"
                >
                  <template #handle="{ checked }">
                    <i
                      :class="[
                        '!text-xm pi',
                        { 'pi-sun': checked, 'pi-moon': !checked },
                      ]"
                    />
                  </template>
                </ToggleSwitch>
              </div>
            </li>
          </ul>

          <!-- Sign out button -->
          <Button
            label="Sign out"
            class="w-full"
            icon="pi pi-sign-out"
            @click="handleLogout"
          />
        </nav>
      </Drawer>

      <!-- Bottom drawer -->
      <Drawer
        v-model:visible="visibleBottom"
        header="My Cart"
        position="bottom"
        class="!h-[85%] rounded-t-3xl"
      >
        <CartList />
        <div>
          <div class="h-10 text-xl font-bold flex items-center justify-between">
            <div>Total</div>
            <span class="text-green-600">
              ₱{{ productsStore.getTotalPrice() }}
            </span>
          </div>
          <Button label="Place order" class="w-full" @click="placeOrder" />
        </div>
      </Drawer>

      <!-- Payment method & message dialog -->
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
              <label for="in_label">Leave a message (optional)</label>
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
    </div>
  </BaseLayout>
</template>
