<script setup lang="ts">
import { useThemeStore } from '@/stores/themeStore';
import { usePrimeVue } from 'primevue/config';
import { onUnmounted, ref, watch } from 'vue';

const props = defineProps<{ class?: string }>();
const currentTheme = usePrimeVue();
const themeStore = useThemeStore();
const dark_mode = ref<boolean>(false);

const darkClasses = [
  'border-b',
  'border-gray-400/30',
  'bg-zinc-950/60',
  'backdrop-blur-sm',
];

onUnmounted(() => {
  document.removeEventListener('scroll', updateHeaderOnScroll);
});

watch(dark_mode, isDarkMode => {
  currentTheme.config.theme.options.darkModeSelector = isDarkMode;
  themeStore.setTheme(isDarkMode ? 'dark' : 'light');

  document.body.classList.toggle('bg-slate-100', !isDarkMode);

  setHeaderStyles(isDarkMode);
});

const lightClasses = ['bg-white/90', 'backdrop-blur-sm', 'border-b'];

const setHeaderStyles = (isDarkMode: boolean) => {
  const header = document.querySelector('header') as HTMLElement;

  if (window.scrollY > 0) {
    header.classList.toggle('dark-mode', isDarkMode);
    header.classList.add(...(isDarkMode ? darkClasses : lightClasses));
  } else {
    header.classList.remove(...darkClasses, ...lightClasses);
  }
}

const updateHeaderOnScroll = () => setHeaderStyles(dark_mode.value);
</script>

<template>
  <header
    :class="[
      props.class,
      'transition-all duration-300 flex justify-center ease h-14 w-full top-0 z-10 fixed',
    ]"
  >
    <div
      class="h-full flex items-center w-full px-5 justify-between max-w-[78rem]"
    >
      <slot />
    </div>
  </header>
</template>

<style scoped></style>
