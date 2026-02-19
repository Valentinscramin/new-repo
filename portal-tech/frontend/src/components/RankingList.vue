<template>
  <div class="space-y-3">
    <div v-for="(item, idx) in items" :key="idx" class="flex items-center justify-between p-3 rounded-lg border border-gray-800" :class="idx === 0 ? 'bg-gradient-to-r from-green-900/30 to-transparent ring-1 ring-green-400/20' : 'bg-gradient-to-b from-[#071027]/60 to-transparent'">
      <div class="flex items-center gap-3">
        <div :class="['w-10 h-10 rounded flex items-center justify-center text-sm font-bold', idx === 0 ? 'bg-green-500 text-black shadow-lg' : 'bg-white/5 text-gray-200']">{{ idx+1 }}</div>
        <div>
          <div class="font-semibold text-gray-100">{{ item.title }}</div>
          <div class="text-xs text-gray-400">{{ item.subtitle }}</div>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <div class="text-sm text-yellow-400 font-semibold">{{ item.rating }} ★</div>
        <button class="text-xs bg-white/5 px-3 py-1 rounded border border-gray-700">Ir</button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({ items: { type: Array, default: () => [] } })
</script>

<style scoped>
.rank-badge {
  box-shadow: 0 6px 18px rgba(16,185,129,0.08);
}
</style>
