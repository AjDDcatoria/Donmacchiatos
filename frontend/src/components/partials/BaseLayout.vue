<script setup lang="ts">
import type { DefaultProps } from '@/types/components';
import { onMounted, ref } from 'vue';
import { usePrimeVue } from 'primevue/config';
import { useThemeStore } from '@/stores/themeStore';

const props = defineProps<DefaultProps>();
const themeStore = useThemeStore();
const dark_mode = ref<boolean>(false);

const currentTheme = usePrimeVue();

onMounted(() => {
  // Theme
  dark_mode.value = currentTheme.config.theme.options.darkModeSelector;
  const body = document.getElementsByTagName('body');
  if (!dark_mode.value) {
    body[0].classList.add('bg-slate-100');
    themeStore.setTheme('light');
  } else {
    themeStore.setTheme('dark');
  }
})
</script>

<template>
  <div :class="[props?.class]">
    <slot />
  </div>
</template>
