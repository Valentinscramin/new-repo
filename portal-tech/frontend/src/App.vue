<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import MainLayout from './layouts/MainLayout.vue'
import AdminLayout from './layouts/AdminLayout.vue'

const route = useRoute()

const currentLayout = computed(() => {
  const layout = route.meta?.layout || 'main'
  if (layout === 'admin') return AdminLayout
  if (layout === 'none') return null
  return MainLayout
})
</script>

<template>
  <div id="app">
    <component v-if="currentLayout" :is="currentLayout">
      <router-view />
    </component>
    <router-view v-else />
  </div>
</template>

<style>
#app {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}
</style>
