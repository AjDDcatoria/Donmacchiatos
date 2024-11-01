<script setup lang="ts">
import Button from 'primevue/button'

interface DialogImageProps {
  productImage: string | null
  class?: string
  onDelete: () => void
  onChange: (e: Event) => void
}

const props = defineProps<DialogImageProps>()
</script>

<template>
  <div
    :class="[
      props.productImage ? '' : 'border-dashed border-2 border-gray-300/30',
      props.class,
      'w-[26rem] rounded-md relative flex',
    ]"
  >
    <Button
      v-if="props.productImage"
      severity="secondary"
      icon="pi pi-times"
      aria-label="delete"
      class="!absolute top-1 right-1 z-10 !h-8 !w-8"
      @click="props.onDelete"
      rounded
    />
    <img
      v-if="props.productImage"
      :src="props.productImage"
      alt="none"
      class="h-full w-full object-cover rounded-md"
    />
    <label
      v-else
      for="photo"
      class="h-full w-full flex flex-col relative gap-2 items-center justify-center cursor-pointer text-gray-300/80"
    >
      <i class="pi pi-camera !text-[2.4rem]"></i>
      <span class="block">Add Photo</span>
      <input
        type="file"
        id="photo"
        @change="onChange"
        accept="image/*"
        class="invisible absolute"
      />
    </label>
  </div>
</template>
