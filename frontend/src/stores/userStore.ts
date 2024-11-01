import { defineStore } from 'pinia'
import type { UserDetailsTypes, UserTypes } from '@/types/usertypes'
import CRYPTO from '@/helpers/crypto'

export const useUserStore = defineStore('session', {
  state: () => ({
    token: undefined as string | undefined,
    user: undefined as string | undefined,
  }),

  actions: {
    setToken(token: string): void {
      this.token = CRYPTO.encrypt(token, import.meta.env.VITE_APP_TOKEN_SECRETE)
    },

    setUser(userData: UserTypes): void {
      this.user = CRYPTO.encrypt(
        JSON.stringify(userData),
        import.meta.env.VITE_APP_USER_SECRETE,
      )
    },

    isAuthenticated(): boolean {
      return this.token !== undefined
    },

    getToken(): string | undefined {
      if (this.token !== undefined) {
        return CRYPTO.decrypt(
          this.token,
          import.meta.env.VITE_APP_TOKEN_SECRETE,
        )
      }
      return undefined
    },

    getUser(): UserTypes | null {
      if (this.user) {
        const originalData = CRYPTO.decrypt(
          this.user,
          import.meta.env.VITE_APP_USER_SECRETE,
        )
        return JSON.parse(originalData)
      }
      return null
    },

    getUserRole(): string | null {
      const currentUser = this.getUser()
      if (currentUser) {
        return currentUser?.role
      }
      return null
    },

    getUserDetails(): UserDetailsTypes | null {
      const currentUser = this.getUser()
      if (currentUser && currentUser.details) {
        return currentUser.details
      }
      return null
    },

    getUserAvatar(): string | null {
      const currentUser: UserTypes | null = this.getUser()
      if (currentUser && currentUser.details?.avatar) {
        return currentUser.details.avatar
      }
      return null
    },

    getFirstname(): string {
      const currentUser = this.getUser()
      if (currentUser && currentUser.details) {
        return currentUser.details.firstname
      }
      return ''
    },

    getFullname(): string | null {
      const currentUser = this.getUser()
      if (currentUser && currentUser.details?.fullname) {
        return currentUser.details.fullname
      }
      return null
    },

    clear(): void {
      localStorage.removeItem('DonMacchiatos')
      this.user = undefined
      this.token = undefined
    },
  },

  persist: {
    key: 'DonMacchiatos',
    storage: window.localStorage,
  },
})
