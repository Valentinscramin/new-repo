<template>
  <div class="min-h-screen bg-netflix-black pt-20 pb-12">
    <LoadingSpinner v-if="loading" message="Carregando produtos..." />
    
    <div v-else>
      <!-- Header -->
      <div class=" mx-auto px-4 md:px-8 mb-8" style="margin-top: 60px;">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Catálogo de Produtos</h1>
            <p class="text-gray-400">Explore todos os produtos disponíveis</p>
          </div>
          
          <router-link 
            to="/" 
            class="netflix-button-secondary text-sm mt-4 md:mt-0 inline-block"
          >
            ← Voltar
          </router-link>
        </div>
      </div>

      <!-- Category Filter Badge -->
      <div class=" mx-auto px-4 md:px-8 mb-6">
        <div v-if="selectedCategoryName" class="flex items-center justify-center">
          <div class="inline-flex items-center gap-3 glass-effect border border-netflix-red/30 rounded-full px-6 py-3">
            <div class="flex items-center gap-2">
              <div class="w-2 h-2 bg-netflix-red rounded-full animate-pulse"></div>
              <span class="text-gray-400 text-sm font-medium">Comparando:</span>
              <span class="text-white font-bold text-sm">{{ selectedCategoryName }}</span>
            </div>
            <span class="text-gray-500 text-xs">{{ filteredProducts.length }} produto{{ filteredProducts.length !== 1 ? 's' : '' }}</span>
            <button 
              @click="handleClearComparison"
              class="text-red-400 hover:text-red-300 transition-colors text-xs flex items-center gap-1"
              title="Limpar comparação"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              Limpar
            </button>
          </div>
        </div>
      </div>

      <!-- Filters Section -->
      <div class=" mx-auto px-4 md:px-8 mb-8">
        <div class="glass-effect rounded-lg p-6">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Category Filters (if needed in future) -->
            <div class="flex-1">
              <label class="text-gray-400 text-sm mb-2 block">Ordenar por:</label>
              <div class="flex flex-wrap gap-2">
                <button 
                  @click="selectedFilter = 'all'"
                  :class="[
                    'px-4 py-2 rounded-lg text-sm font-medium transition-all',
                    selectedFilter === 'all' 
                      ? 'bg-netflix-red text-white shadow-lg shadow-netflix-red/30' 
                      : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white'
                  ]"
                >
                  Todos
                </button>
                <button 
                  @click="selectedFilter = 'best-rated'"
                  :class="[
                    'px-4 py-2 rounded-lg text-sm font-medium transition-all',
                    selectedFilter === 'best-rated' 
                      ? 'bg-netflix-red text-white shadow-lg shadow-netflix-red/30' 
                      : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white'
                  ]"
                >
                  ⭐ Mais Avaliados
                </button>
                <button 
                  @click="selectedFilter = 'best-price'"
                  :class="[
                    'px-4 py-2 rounded-lg text-sm font-medium transition-all',
                    selectedFilter === 'best-price' 
                      ? 'bg-netflix-red text-white shadow-lg shadow-netflix-red/30' 
                      : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white'
                  ]"
                >
                  💰 Melhores Preços
                </button>
              </div>
            </div>

            <!-- Results Count -->
            <div class="text-right">
              <p class="text-gray-400 text-sm">
                Exibindo <span class="text-white font-semibold">{{ filteredProducts.length }}</span> produtos
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Products Grid -->
      <div class=" mx-auto px-4 md:px-8">
        <div v-if="filteredProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <ProductCard
            v-for="product in displayedProducts"
            :key="product.id || product.title"
            :product="product"
            @preview="openProductModal"
            @add-to-favorites="handleAddToFavorites"
          />
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-20">
          <div class="glass-effect rounded-lg p-12 inline-block">
            <center>
              <svg class="w-24 h-24 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
            </center>
            <h3 class="text-2xl font-bold text-white mb-2">Nenhum produto encontrado</h3>
            <p class="text-gray-400 mb-6">Tente ajustar os filtros ou volte à página inicial</p>
            <router-link to="/" class="netflix-button-primary">
              Voltar à Home
            </router-link>
          </div>
        </div>

        <!-- Load More Button -->
        <div v-if="hasMoreProducts" class="text-center mt-8">
          <button 
            @click="loadMore"
            class="netflix-button-secondary"
          >
            Carregar Mais Produtos
          </button>
        </div>
      </div>

      <!-- Comparison CTA -->
      <div v-if="comparisonCount > 0" class=" mx-auto px-4 md:px-8 mt-12" style="margin-top:20px; margin-bottom:20px;">
        <div class="glass-effect rounded-lg p-6 flex flex-col md:flex-row items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="bg-netflix-red/20 text-netflix-red p-3 rounded-full">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
              </svg>
            </div>
            <div class="text-left">
              <p class="text-white font-semibold text-lg">{{ comparisonCount }} produto{{ comparisonCount > 1 ? 's' : '' }} selecionado{{ comparisonCount > 1 ? 's' : '' }}</p>
              <p class="text-gray-400 text-sm">Pronto para comparar especificações</p>
            </div>
          </div>
          <router-link to="/compare" class="netflix-button-primary">
            Ver Comparação →
          </router-link>
        </div>
      </div>
    </div>

    <!-- Product Modal -->
    <ProductModal
      :isOpen="isModalOpen"
      :product="selectedProduct"
      :products="displayedProducts"
      :currentIndex="selectedProductIndex"
      @close="closeProductModal"
      @navigate="handleNavigateProduct"
      @add-to-favorites="handleAddToFavorites"
      @add-to-compare="handleAddToCompare"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { getAllProducts } from '../services/api'
import { useUIStore } from '../stores/ui'
import { useComparisonStore } from '../stores/comparison'

import ProductCard from '../components/ProductCard.vue'
import ProductModal from '../components/ProductModal.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'

const uiStore = useUIStore()
const comparisonStore = useComparisonStore()

const loading = ref(true)
const allProducts = ref([])
const selectedFilter = ref('all')
const itemsPerPage = 12
const currentPage = ref(1)

// Comparison store data
const comparisonCount = computed(() => comparisonStore.comparisonCount)
const selectedCategoryName = computed(() => comparisonStore.selectedCategoryName)

// Helper para filtrar produtos por categoria (quando há comparação ativa)
const filterByCategory = (products) => {
  if (!selectedCategoryName.value || products.length === 0) {
    return products
  }
  return products.filter(p => {
    const productCategory = p.category?.name || p.category?.id
    return productCategory === selectedCategoryName.value
  })
}

// Computed groups
const catalogBestRated = computed(() => {
  const filtered = filterByCategory(allProducts.value)
  return filtered
    .filter(p => p.rating >= 4.5)
    .sort((a, b) => parseFloat(b.rating) - parseFloat(a.rating))
})

const catalogBestPrice = computed(() => {
  const filtered = filterByCategory(allProducts.value)
  return filtered
    .filter(p => p.price)
    .sort((a, b) => parseFloat(a.price) - parseFloat(b.price))
})

// Filtered products
const filteredProducts = computed(() => {
  let products = []
  if (selectedFilter.value === 'best-rated') {
    products = catalogBestRated.value
  } else if (selectedFilter.value === 'best-price') {
    products = catalogBestPrice.value
  } else {
    products = filterByCategory(allProducts.value)
  }
  return products
})

// Paginated products
const displayedProducts = computed(() => {
  return filteredProducts.value.slice(0, currentPage.value * itemsPerPage)
})

const hasMoreProducts = computed(() => {
  return displayedProducts.value.length < filteredProducts.value.length
})

function loadMore() {
  currentPage.value++
}

// Modal state
const selectedProduct = ref(null)
const selectedProductIndex = ref(0)
const isModalOpen = computed(() => selectedProduct.value !== null)

const openProductModal = (product) => {
  selectedProduct.value = product
  // Find the index in the currently displayed products list
  selectedProductIndex.value = displayedProducts.value.findIndex(p => p.id === product.id)
  if (selectedProductIndex.value === -1) {
    selectedProductIndex.value = 0
  }
  uiStore.openProductModal(product)
}

const closeProductModal = () => {
  selectedProduct.value = null
  selectedProductIndex.value = 0
  uiStore.closeProductModal()
}

const handleNavigateProduct = (newIndex) => {
  if (newIndex >= 0 && newIndex < displayedProducts.value.length) {
    selectedProductIndex.value = newIndex
    selectedProduct.value = displayedProducts.value[newIndex]
  }
}

const handleAddToFavorites = (product) => {
  console.log('Add to favorites:', product)
  alert(`${product.name || product.title} adicionado aos favoritos!`)
}

const handleAddToCompare = (product) => {
  const added = comparisonStore.addProduct(product)
  if (added) {
    console.log('Product added to comparison:', product)
  } else {
    console.warn('Could not add product to comparison')
  }
}

const handleClearComparison = () => {
  if (confirm('Tem certeza que deseja limpar toda a comparação?')) {
    comparisonStore.clearAll()
  }
}

onMounted(async () => {
  loading.value = true
  try {
    const products = await getAllProducts()
    allProducts.value = Array.isArray(products) ? products : []
  } catch (error) {
    console.error('Erro ao carregar produtos:', error)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
/* View-specific adjustments handled by Tailwind utility classes */
</style>
