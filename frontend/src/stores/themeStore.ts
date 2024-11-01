import { defineStore } from 'pinia'

export const useThemeStore = defineStore('theme', {
  state: () => ({
    theme: undefined as string | undefined,
  }),

  actions: {
    setTheme(mode: string) {
      this.theme = mode
    },

    getTheme() {
      return this.theme
    },
  },
})
