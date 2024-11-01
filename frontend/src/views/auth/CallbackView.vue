<script setup lang="ts">
import { getUser } from '@/services/api/userService'
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import { ref } from 'vue'

const router = useRouter()
const userStore = useUserStore()
const pathTo = ref<string>('home')

onMounted(async () => {
  const { payload } = await getUser()
  if (payload) {
    if (payload.is_setup === 1) {
      userStore.setUser(payload)
      pathTo.value = payload.role == 'admin' ? 'admin.dashboard' : 'home'
    } else if (payload.is_setup == 0) {
      pathTo.value = 'user.setup'
    }
  }
  await router.push({ name: pathTo.value })
})
</script>

<template>
  <main>Loading</main>
</template>
