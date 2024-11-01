<script setup lang="ts">
import { adminNavigations } from '@/lib/constans/navlist'
import { useThemeStore } from '@/stores/themeStore'
import Button from 'primevue/button'
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const themeStore = useThemeStore()
const hoveredIndex = ref<number | null>(null)
</script>

<template>
  <aside
    v-if="route.path !== 'home'"
    class="h-full w-[18rem] fixed pt-6 pl-16 text-sm"
  >
    <nav>
      <ul class="cursor-pointer">
        <li
          v-for="(item, index) in adminNavigations"
          :key="index"
          class="h-10 flex items-center gap-3"
          @mouseover="hoveredIndex = index"
          @mouseleave="hoveredIndex = null"
          @click="router.push({ name: item.path })"
        >
          <Button
            :icon="item.icon"
            type="button"
            outlined
            aria-label="Filter"
            :class="[
              route.name === item.path
                ? `bg-${themeStore.getTheme()}`
                : hoveredIndex === index
                  ? `bg-${themeStore.getTheme()}`
                  : 'icons',
              '!h-8 !w-8 text-gray-700',
            ]"
          />
          {{ item.label }}
        </li>
      </ul>
    </nav>
  </aside>
</template>

<style scoped>
.icons {
  background-color: transparent;
  transition: background-color 0.3s ease;
}
.bg-light {
  background-color: white !important;
}

.bg-dark {
  background-color: rgb(71, 70, 70) !important;
}
</style>
