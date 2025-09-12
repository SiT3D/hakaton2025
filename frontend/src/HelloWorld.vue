<template>
  <div>
    <h2>Hello, {{ name }}!</h2>
  </div>
</template>

<script setup>

defineProps({
  name: String
})
</script>