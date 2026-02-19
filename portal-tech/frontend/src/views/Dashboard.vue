<template>
  <div class="max-w-4xl mx-auto py-10" style="margin-top: 70px;">
    <h1 class="text-3xl font-bold mb-6">Dashboard</h1>
    <div class="flex gap-4 mb-6">
      <router-link to="/dashboard/favorites" class="px-4 py-2 bg-gray-800 rounded">Favorites</router-link>
      <router-link to="/dashboard/reviews" class="px-4 py-2 bg-gray-800 rounded">Reviews</router-link>
    </div>
    <p class="text-gray-400">Use the links above to manage your favorites and reviews.</p>
  </div>
</template>

<script setup>
</script>

<style scoped>
</style>
