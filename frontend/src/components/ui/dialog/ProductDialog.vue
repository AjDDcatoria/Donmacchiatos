<script setup lang="ts">
import Dialog from 'primevue/dialog'

interface DialogProps {
  header: string
  class?: string
}

const props = defineProps<DialogProps>()
</script>

<template>
  <Dialog
    modal
    :header="props.header"
    :class="[props.class, 'w-[40rem] h-[25rem]']"
  >
    <div class="flex flex-col gap-5">
      <div class="flex gap-6 h-[14rem]">
        <slot name="Image" />
        <slot name="Input" />
      </div>
      <div>
        <div class="flex gap-3 float-right">
          <slot name="Control" />
        </div>
      </div>
    </div>
  </Dialog>
</template>
