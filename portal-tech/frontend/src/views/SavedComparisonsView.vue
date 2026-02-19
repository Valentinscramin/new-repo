<template>
  <div class="min-h-screen bg-netflix-black pt-20 pb-12">
    <!-- Header -->
    <div class=" mx-auto px-4 md:px-8 mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Minhas Comparações</h1>
          <p class="text-gray-400">Histórico de comparações salvas</p>
        </div>
        <router-link 
          to="/dashboard" 
          class="netflix-button-secondary text-sm"
        >
          ← Voltar
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class=" mx-auto px-4 md:px-8">
      <div class="flex items-center justify-center py-20">
        <svg class="w-12 h-12 text-netflix-red animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="savedComparisons.length === 0" class="max-w-4xl mx-auto px-4 md:px-8 text-center py-20">
      <div class="glass-effect rounded-lg p-12">
        <svg class="w-24 h-24 mx-auto text-gray-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h2 class="text-2xl font-bold text-white mb-3">Nenhuma comparação salva</h2>
        <p class="text-gray-400 mb-6">Você ainda não salvou nenhuma comparação de produtos</p>
        <router-link to="/compare" class="netflix-button-primary inline-block">
          Criar Comparação
        </router-link>
      </div>
    </div>

    <!-- Comparisons List -->
    <div v-else class=" mx-auto px-4 md:px-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="comparison in savedComparisons" 
          :key="comparison.id"
          class="glass-effect rounded-lg overflow-hidden hover:border-netflix-red/50 transition-all duration-300 group"
        >
          <!-- Comparison Header -->
          <div class="bg-netflix-black-light p-4 border-b border-gray-800">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-netflix-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-white font-semibold text-sm">{{ getProductCount(comparison) }} produtos</span>
              </div>
              <span class="text-gray-400 text-xs">{{ formatDate(comparison.createdAt) }}</span>
            </div>
          </div>

          <!-- Products Preview -->
          <div class="p-4">
            <div class="space-y-3 mb-4">
              <div 
                v-for="product in getProducts(comparison)" 
                :key="product.id"
                class="flex items-center gap-3"
              >
                <img 
                  v-if="product.image"
                  :src="product.image" 
                  :alt="product.name || product.title"
                  class="w-12 h-12 object-cover rounded-md"
                />
                <div class="flex-1 min-w-0">
                  <p class="text-white text-sm font-medium truncate">{{ product.name || product.title }}</p>
                  <div class="flex items-center gap-2 text-xs">
                    <span v-if="product.rating" class="text-gray-400">⭐ {{ product.rating }}</span>
                    <span v-if="product.price" class="text-netflix-red font-semibold">R$ {{ formatPrice(product.price) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
              <button 
                @click="handleViewComparison(comparison)"
                class="flex-1 netflix-button-primary text-sm py-2 flex items-center justify-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Ver
              </button>
              <button 
                @click="handleDeleteComparison(comparison.id)"
                class="netflix-button-secondary bg-red-900/20 hover:bg-red-900/40 text-red-400 text-sm py-2 px-3"
                title="Excluir comparação"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useComparisonStore } from '../stores/comparison'
import * as api from '../services/api'

const router = useRouter()
const comparisonStore = useComparisonStore()
const loading = ref(true)
const savedComparisons = ref([])

onMounted(async () => {
  await loadComparisons()
})

const loadComparisons = async () => {
  loading.value = true
  try {
    const result = await comparisonStore.fetchSavedComparisons(api)
    savedComparisons.value = result
  } catch (error) {
    console.error('Erro ao carregar comparações:', error)
    alert('Erro ao carregar comparações. Por favor, tente novamente.')
  } finally {
    loading.value = false
  }
}

const getProducts = (comparison) => {
  // Backend can return products in different formats
  if (comparison.products && Array.isArray(comparison.products)) {
    return comparison.products
  }
  // Legacy format with productA and productB
  const products = []
  if (comparison.productA) products.push(comparison.productA)
  if (comparison.productB) products.push(comparison.productB)
  if (comparison.productC) products.push(comparison.productC)
  return products.filter(p => p !== null && p !== undefined)
}

const getProductCount = (comparison) => {
  return getProducts(comparison).length
}

const formatDate = (dateString) => {
  if (!dateString) return 'Data desconhecida'
  const date = new Date(dateString)
  return date.toLocaleDateString('pt-BR', { 
    day: '2-digit', 
    month: 'short', 
    year: 'numeric' 
  })
}

const formatPrice = (price) => {
  if (typeof price === 'number') {
    return price.toFixed(2).replace('.', ',')
  }
  return price
}

const handleViewComparison = (comparison) => {
  const products = getProducts(comparison)
  comparisonStore.loadComparison(products)
  router.push('/compare')
}

const handleDeleteComparison = async (comparisonId) => {
  if (!confirm('Tem certeza que deseja excluir esta comparação?')) {
    return
  }

  try {
    await comparisonStore.deleteComparison(api, comparisonId)
    savedComparisons.value = savedComparisons.value.filter(c => c.id !== comparisonId)
    alert('Comparação excluída com sucesso!')
  } catch (error) {
    console.error('Erro ao excluir comparação:', error)
    alert('Erro ao excluir comparação. Por favor, tente novamente.')
  }
}
</script>

<style scoped>
/* Component-specific styles handled by Tailwind */
</style>
