<script setup lang="ts">
import type { TextProps } from '@/types/components'
import {useRouter} from 'vue-router'
const props = defineProps<TextProps>()
const router = useRouter()
</script>

<template>
  <div @click="router.push({name:'home'})">
    <h1 :class="[props.class, 'text-3xl font-bold cursor-pointer font-pacifico']">Don macchiatos</h1>
    <small v-if="props.label" class="text-gray-400">{{ props.label }}</small>
  </div>
</template>
