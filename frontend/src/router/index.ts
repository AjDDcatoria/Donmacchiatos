import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import LoginView from '@/views/auth/LoginView.vue'
import CallbackView from '@/views/auth/CallbackView.vue'
import SetupView from '@/views/customer/SetupView.vue'
import DashBoard from '@/views/admin/DashBoard.vue'
import ManageProducts from '@/views/admin/ManageProducts.vue'
import { useUserStore } from '@/stores/userStore'
import ProductsView from '@/views/customer/ProductsView.vue'
import ProfileView from '@/views/customer/ProfileView.vue'
import OrdersView from '@/views/customer/OrdersView.vue'
import CustomerOrders from '@/views/admin/CustomerOrders.vue'

const routes = [
  {
    path: '/',
    name: 'homeLayout',
    component: HomeView,
    meta: { setup: true },
    children: [
      {
        path: '',
        name: 'home',
        component: ProductsView,
      },
      {
        path: '/profile',
        name: 'customer.profile',
        component: ProfileView,
        meta: { auth: true },
      },
      {
        path: '/orders',
        name: 'customer.orders',
        component: OrdersView,
        meta: { auth: true },
      },
      // Admin routes
      {
        path: '/admin',
        name: 'admin.dashboard',
        component: DashBoard,
        meta: { admin: true, auth: true },
      },
      {
        path: '/admin/products',
        name: 'admin.products',
        component: ManageProducts,
        meta: { admin: true, auth: true },
      },
      {
        path: '/admin/orders',
        name: 'admin.ordes',
        component: CustomerOrders,
        meta: { admin: true, auth: true },
      },
    ],
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { guest: true },
  },
  {
    path: '/callback',
    name: 'callback',
    component: CallbackView,
    meta: { auth: true },
  },
  {
    path: '/user/setup',
    name: 'user.setup',
    component: SetupView,
    meta: { auth: true, not_setup: true },
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

router.beforeEach((to, from, next) => {
  const userStore = useUserStore()
  const current_user = userStore.getUser()

  if (to.meta.auth && !userStore.isAuthenticated()) {
    return next({ name: 'login' })
  }

  if (to.meta.guest && userStore.isAuthenticated()) {
    return next({ name: 'home' })
  }

  if (
    to.meta.setup &&
    userStore.isAuthenticated() &&
    current_user?.is_setup !== 1
  ) {
    return next({ name: 'user.setup' })
  }
  if (to.meta.not_setup && current_user?.is_setup == 1) {
    return next({ name: 'home' })
  }

  if (to.meta.admin && current_user?.role !== 'admin') {
    return next({ name: 'home' })
  }

  next()
})

export default router
