<template>
  <div class="inline-flex items-baseline gap-2">
    <span class="text-2xl font-bold text-green-400 drop-shadow-[0_10px_24px_rgba(16,185,129,0.18)]">{{ price }}</span>
    <span class="text-sm text-gray-400">{{ currency }}</span>
  </div>
</template>

<script setup>
defineProps({ price: { type: [Number, String], default: '0' }, currency: { type: String, default: '€' } })
</script>

<style scoped></style>
