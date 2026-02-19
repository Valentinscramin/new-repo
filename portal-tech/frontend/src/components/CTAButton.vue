<template>
  <button :class="buttonClass" @click="$emit('click')">
    <slot>{{ label }}</slot>
  </button>
</template>

<script setup>
const props = defineProps({ label: { type: String, default: 'Ação' }, variant: { type: String, default: 'primary' } })

const buttonClass = computed(() => {
  if (props.variant === 'primary') return 'w-full sm:w-auto bg-green-500 hover:bg-green-600 text-black px-6 py-3 rounded shadow-lg ring-1 ring-green-400/20 transition-all'
  if (props.variant === 'ghost') return 'w-full sm:w-auto bg-transparent border border-gray-700 text-white px-4 py-2 rounded'
  if (props.variant === 'danger') return 'w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded shadow-2xl ring-1 ring-red-500/20 transition-all'
  return 'w-full sm:w-auto px-4 py-2 rounded bg-gray-700 text-white'
})
</script>

<style scoped></style>
