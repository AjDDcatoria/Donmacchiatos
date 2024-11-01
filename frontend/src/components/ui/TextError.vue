<script setup lang="ts">
import type { TextProps } from '@/types/components'

const props = defineProps<TextProps>()
</script>

<template>
  <span :id="props.id" :class="[props.class, 'text-red-500 font-semibold']">
    {{ props.label }}
  </span>
</template>
