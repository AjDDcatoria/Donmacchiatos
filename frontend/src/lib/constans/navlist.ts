import type { NavigationTypes } from '@/types/components'

export const customerNavigations: NavigationTypes[] = [
  {
    icon: 'pi-user',
    label: 'Profile',
    path: 'customer.profile',
    badge: null,
    severity: null,
  },
  // {
  //   icon: 'pi-envelope',
  //   label: 'Message',
  //   path: 'customer.message',
  //   badge: 0,
  //   severity: null,
  // },
  // {
  //   icon: 'pi-shopping-cart',
  //   label: 'Cart',
  //   path: 'customer.cart',
  //   badge: 0,
  //   severity: 'info',
  // },
  {
    icon: 'pi-shopping-bag',
    label: 'Orders',
    path: 'customer.orders',
    badge: 0,
    severity: 'success',
  },
]

export const adminNavigations: NavigationTypes[] = [
  { icon: 'pi pi-objects-column', label: 'Dashboard', path: 'admin.dashboard' },
  { icon: 'pi pi-cart-plus', label: 'Orders', path: 'admin.ordes' },
  // { icon: 'pi pi-inbox', label: 'Messages', path: 'admin.messages' },
  { icon: 'pi pi-shopping-bag', label: 'Products', path: 'admin.products' },
  { icon: 'pi pi-users', label: 'Users', path: 'admin.manageUser' },
]
