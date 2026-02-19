<template>
  <div class="min-h-screen bg-netflix-black pt-20 pb-12">
    <!-- Header -->
    <div class=" mx-auto px-4 md:px-8 mb-8" style="margin-top: 60px;">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Comparação de Produtos</h1>
          <p class="text-gray-400">Compare especificações e encontre o melhor produto para você</p>
        </div>
        <router-link 
          to="/" 
          class="netflix-button-secondary text-sm"
        >
          ← Voltar
        </router-link>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="comparisonCount === 0" class="mx-auto px-4 md:px-8 py-20">
      <div class="glass-effect rounded-lg p-12 text-center">
        <center><svg class="w-24 h-24 mx-auto text-gray-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg></center>
        <h2 class="text-2xl font-bold text-white mb-3">Nenhum produto selecionado</h2>
        <p class="text-gray-400 mb-6">Selecione produtos da página inicial para começar a comparar</p>
        <router-link to="/" class="netflix-button-primary inline-block">
          Explorar Produtos
        </router-link>
      </div>
    </div>

    <!-- Comparison Content -->
    <div v-else>
      <!-- Info Bar -->
      <div class="px-4 md:px-8 mb-6" style="margin-bottom: 20px; margin-top:20px;">
        <div class="glass-effect rounded-lg p-4 flex flex-col md:flex-row items-center gap-4">
          <div class="flex items-center gap-3">
            <div class="bg-netflix-red/20 text-netflix-red p-2 rounded-full">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
              </svg>
            </div>
            <div>
              <p class="text-white font-semibold">{{ comparisonCount }} produto{{ comparisonCount > 1 ? 's' : '' }} selecionado{{ comparisonCount > 1 ? 's' : '' }}</p>
              <p class="text-gray-400 text-sm">Máximo: 3 produtos</p>
            </div>
          </div>
          <div class="flex gap-3 md:ml-auto">
            <button 
              v-if="isAuthenticated"
              @click="handleSaveComparison" 
              :disabled="saving || comparisonCount < 2"
              class="netflix-button-secondary text-sm flex items-center gap-2"
            >
              <svg v-if="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
              </svg>
              <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ saving ? 'Salvando...' : 'Salvar Comparação' }}
            </button>
            <button 
              @click="handleClearAll" 
              class="netflix-button-secondary text-sm bg-red-900/20 hover:bg-red-900/40 text-red-400 flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Limpar Tudo
            </button>
          </div>
        </div>
      </div>

      <!-- Add More Products Hint -->
      <div v-if="comparisonCount < 3" class="px-4 md:px-8 mb-6">
        <div class="bg-blue-900/20 border border-blue-700/30 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <div>
              <p class="text-blue-400 font-semibold text-sm">Adicione mais produtos</p>
              <p class="text-gray-400 text-sm mt-1">Você pode comparar até 3 produtos simultaneamente. Volte à página inicial para adicionar mais.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Comparison Table -->
      <div class="w-full">
        <ComparisonTable 
          :products="selectedProducts" 
          :interactive="true"
          @remove-product="handleRemoveProduct"
          @add-more="$router.push('/')"
        />
      </div>

      <!-- Ranking Summary -->
      <div v-if="comparisonCount >= 2" class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 w-full px-4 md:px-8">
        <div 
          v-for="product in selectedProducts" 
          :key="product.id"
          class="glass-effect rounded-lg p-6 relative overflow-hidden"
          :class="{
            'border-2 border-yellow-500/50 bg-yellow-900/10': product.rank === 1,
            'border border-gray-700': product.rank !== 1
          }"
        >
          <!-- Badge de Ranking -->
          <div class="absolute top-4 right-4">
            <div 
              class="text-3xl"
              :title="getRankBadge(product.rank).text"
            >
              {{ getRankBadge(product.rank).emoji }}
            </div>
          </div>

          <!-- Informações do Produto -->
          <div class="flex items-start gap-4 mb-4">
            <img 
              v-if="product.image"
              :src="product.image" 
              :alt="product.name || product.title"
              class="w-16 h-16 object-cover rounded-md"
            />
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1">
                <span 
                  class="px-2 py-0.5 rounded text-xs font-bold"
                  :class="{
                    'bg-yellow-500 text-black': product.rank === 1,
                    'bg-gray-600 text-white': product.rank === 2,
                    'bg-orange-700 text-white': product.rank === 3
                  }"
                >
                  {{ getRankBadge(product.rank).text }}
                </span>
              </div>
              <h4 class="text-white font-bold mb-1 text-sm">{{ product.name || product.title }}</h4>
              <div class="flex items-center gap-2 flex-wrap">
                <span v-if="product.rating" class="text-xs text-gray-400">⭐ {{ product.rating }}</span>
                <span v-if="getLowestPrice(product)" class="text-netflix-red font-semibold text-sm">
                  R$ {{ formatPrice(getLowestPrice(product)) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Score Breakdown -->
          <div v-if="product.breakdown" class="space-y-2 mt-4 pt-4 border-t border-gray-700">
            <div class="flex justify-between items-center">
              <span class="text-xs text-gray-400">Score Total</span>
              <span class="text-sm font-bold text-white">{{ formatScore(product.breakdown.totalScore) }}/100</span>
            </div>
            
            <div class="space-y-1">
              <div class="flex justify-between items-center text-xs">
                <span class="text-gray-500">💰 Preço</span>
                <span class="text-gray-300">{{ formatScore(product.breakdown.priceScore) }}/100</span>
              </div>
              <div class="w-full bg-gray-700 rounded-full h-1.5">
                <div 
                  class="bg-green-500 h-1.5 rounded-full transition-all"
                  :style="{ width: `${formatScore(product.breakdown.priceScore)}%` }"
                ></div>
              </div>
            </div>

            <div class="space-y-1">
              <div class="flex justify-between items-center text-xs">
                <span class="text-gray-500">⭐ Avaliação</span>
                <span class="text-gray-300">{{ formatScore(product.breakdown.ratingScore) }}/100</span>
              </div>
              <div class="w-full bg-gray-700 rounded-full h-1.5">
                <div 
                  class="bg-blue-500 h-1.5 rounded-full transition-all"
                  :style="{ width: `${formatScore(product.breakdown.ratingScore)}%` }"
                ></div>
              </div>
            </div>

            <div class="space-y-1">
              <div class="flex justify-between items-center text-xs">
                <span class="text-gray-500">🔧 Especificações</span>
                <span class="text-gray-300">{{ formatScore(product.breakdown.specsScore) }}/100</span>
              </div>
              <div class="w-full bg-gray-700 rounded-full h-1.5">
                <div 
                  class="bg-purple-500 h-1.5 rounded-full transition-all"
                  :style="{ width: `${formatScore(product.breakdown.specsScore)}%` }"
                ></div>
              </div>
            </div>
          </div>

          <!-- Recomendação -->
          <div v-if="product.rank === 1" class="mt-4 pt-4 border-t border-yellow-500/30">
            <div class="flex items-center gap-2 text-yellow-400 text-xs font-semibold">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <span>MELHOR CUSTO-BENEFÍCIO</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Product Details Cards (Mobile-friendly alternative) -->
      <div class="mt-8 md:hidden">
        <h3 class="text-xl font-bold text-white mb-4">Detalhes dos Produtos</h3>
        <div class="space-y-4">
          <div 
            v-for="product in selectedProducts" 
            :key="product.id"
            class="glass-effect rounded-lg p-4"
          >
            <div class="flex items-start gap-4 mb-4">
              <img 
                v-if="product.image"
                :src="product.image" 
                :alt="product.name || product.title"
                class="w-20 h-20 object-cover rounded-md"
              />
              <div class="flex-1">
                <h4 class="text-white font-bold mb-1">{{ product.name || product.title }}</h4>
                <div class="flex items-center gap-2">
                  <span v-if="product.rating" class="text-sm text-gray-400">⭐ {{ product.rating }}</span>
                  <span v-if="getLowestPrice(product)" class="text-netflix-red font-semibold">R$ {{ formatPrice(getLowestPrice(product)) }}</span>
                </div>
              </div>
              <button 
                @click="handleRemoveProduct(product.id)"
                class="text-red-400 hover:text-red-300 p-1"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div v-if="product.description" class="text-gray-400 text-sm">
              {{ product.description }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useComparisonStore } from '../stores/comparison'
import ComparisonTable from '../components/ComparisonTable.vue'
import * as api from '../services/api'
import { rankProducts, formatScore, getRankBadge } from '../utils/productRanking'

const router = useRouter()
const comparisonStore = useComparisonStore()
const saving = ref(false)

const selectedProducts = computed(() => {
  const products = comparisonStore.selectedProducts
  
  // Se temos produtos para comparar, aplicar ranqueamento
  if (products.length > 0) {
    const ranked = rankProducts(products)
    // Retornar produtos ordenados por ranking
    return ranked.map(item => ({
      ...item.product,
      rank: item.rank,
      score: item.score,
      breakdown: item.breakdown
    }))
  }
  
  return products
})

const comparisonCount = computed(() => comparisonStore.comparisonCount)

const isAuthenticated = computed(() => {
  return localStorage.getItem('token') !== null
})

const handleRemoveProduct = (productId) => {
  comparisonStore.removeProduct(productId)
}

const handleClearAll = () => {
  if (confirm('Tem certeza que deseja limpar toda a comparação?')) {
    comparisonStore.clearAll()
  }
}

const handleSaveComparison = async () => {
  if (comparisonCount.value < 2) {
    alert('Você precisa de pelo menos 2 produtos para salvar uma comparação.')
    return
  }

  saving.value = true
  try {
    await comparisonStore.saveComparison(api)
    alert('Comparação salva com sucesso!')
    router.push('/dashboard/comparisons')
  } catch (error) {
    console.error('Erro ao salvar comparação:', error)
    alert('Erro ao salvar comparação. Por favor, tente novamente.')
  } finally {
    saving.value = false
  }
}

const formatPrice = (price) => {
  if (typeof price === 'number') {
    return price.toFixed(2).replace('.', ',')
  }
  return price
}

const getLowestPrice = (product) => {
  if (!product) return null
  
  // Preço vem apenas das ofertas
  if (product.offers && Array.isArray(product.offers) && product.offers.length > 0) {
    const prices = product.offers.map(offer => offer.price).filter(price => price !== null && price !== undefined)
    if (prices.length > 0) {
      return Math.min(...prices)
    }
  }
  
  // Fallback ao campo price retornado pela API (já é o menor preço das ofertas)
  return product.price || null
}
</script>

<style scoped>
/* Component-specific styles handled by Tailwind */
</style>
