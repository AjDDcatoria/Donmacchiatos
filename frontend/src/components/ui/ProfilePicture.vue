<script setup lang="ts">
import Avatar from "primevue/avatar";

const props = defineProps<{image:string | null,alt:string}>();
</script>

<template>
  <Avatar v-if="props.image" :image="props.image" class="mr-2" size="normal" shape="circle" />
  <Avatar v-else :label="props.alt[0]" class="mr-2 !shadow !border" size="normal" style="background-color: #ece9fc; color: #2a1261" shape="circle" />
</template>

