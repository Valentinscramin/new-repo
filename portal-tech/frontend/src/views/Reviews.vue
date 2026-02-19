<template>
  <LoadingSpinner v-if="loading" message="Carregando reviews..." />
  <div v-else class="max-w-4xl mx-auto py-10" style="margin-top: 60px;">
    <h2 class="text-2xl font-semibold mb-4">My Reviews</h2>
    <div>
      <div v-if="items.length === 0" class="text-gray-400">You haven't written any reviews yet.</div>
      <ul class="space-y-3">
        <li v-for="it in items" :key="it.id" class="p-4 bg-gray-900 rounded">
          <div>Product ID: {{ it.product }}</div>
          <div>Rating: {{ it.rating }}</div>
          <div class="text-gray-400">{{ it.comment }}</div>
          <div class="text-sm text-gray-500">{{ it.createdAt }}</div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const items = ref([]);
const loading = ref(false);

async function load() {
  loading.value = true;
  try {
    const res = await api.get('/reviews');
    items.value = res.data;
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

onMounted(load);
</script>

<style scoped>
</style>
