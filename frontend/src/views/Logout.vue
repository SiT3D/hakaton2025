<template>
  <div>Выход...</div>
</template>

<script setup>
import { onMounted } from "vue"
import { useRouter } from "vue-router"

const router = useRouter()

onMounted(() => {
  localStorage.removeItem("token")
  router.push("/login")   // редирект
})
</script>
