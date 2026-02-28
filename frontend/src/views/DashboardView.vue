<template>
  <div class="min-h-screen bg-dark-bg py-12 px-4">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-4xl font-bold mb-2">Dashboard</h1>
          <p class="text-gray-400">Suas comparações salvas</p>
        </div>
        <div class="flex gap-4">
          <router-link to="/" class="btn-primary">
            Nova Comparação
          </router-link>
          <button @click="handleLogout" class="btn-secondary">
            Sair
          </button>
        </div>
      </div>

      <!-- User Info -->
      <div class="card mb-8">
        <p class="text-gray-400">Conectado como: <span class="text-neon-green font-semibold">{{ authStore.user?.email }}</span></p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-20">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-neon-green"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="!comparisons || comparisons.length === 0" class="card text-center py-12">
        <p class="text-xl text-gray-400 mb-6">Você ainda não tem comparações salvas</p>
        <router-link to="/" class="btn-primary">
          Criar Primeira Comparação
        </router-link>
      </div>

      <!-- Comparisons List -->
      <div v-else class="space-y-4">
        <div v-for="comparison in comparisons" :key="comparison.id" class="card hover:border-neon-green/50 transition-colors cursor-pointer" @click="viewComparison(comparison.id)">
          <div class="flex justify-between items-center">
            <div>
              <h3 class="text-xl font-bold mb-2">Comparação #{{ comparison.id }}</h3>
              <p class="text-gray-400 text-sm">{{ new Date(comparison.createdAt).toLocaleString('pt-BR') }}</p>
              <p class="text-sm mt-2">
                <span :class="statusClass(comparison.status)" class="px-3 py-1 rounded-full text-xs font-semibold">
                  {{ statusText(comparison.status) }}
                </span>
              </p>
            </div>
            <div class="flex items-center gap-4">
              <span class="text-gray-400 text-sm">{{ comparison.productCount }} produtos</span>
              <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useComparisonStore } from '@/stores/comparison'

const router = useRouter()
const authStore = useAuthStore()
const comparisonStore = useComparisonStore()

const loading = ref(false)
const comparisons = computed(() => comparisonStore.comparisons)

function statusClass(status: string): string {
  switch (status) {
    case 'completed':
      return 'bg-neon-green/20 text-neon-green'
    case 'processing':
      return 'bg-yellow-500/20 text-yellow-400'
    case 'failed':
      return 'bg-red-500/20 text-red-400'
    default:
      return 'bg-gray-500/20 text-gray-400'
  }
}

function statusText(status: string): string {
  switch (status) {
    case 'completed':
      return 'Concluído'
    case 'processing':
      return 'Processando'
    case 'failed':
      return 'Falhou'
    default:
      return status
  }
}

function viewComparison(id: number) {
  router.push(`/comparison/${id}`)
}

function handleLogout() {
  authStore.logout()
  router.push('/')
}

onMounted(async () => {
  loading.value = true
  await comparisonStore.fetchUserComparisons()
  loading.value = false
})
</script>
