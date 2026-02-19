<template>
  <Teleport to="body">
    <Transition name="modal">
      <div 
        v-if="isOpen"
        class="modal-backdrop"
        @click.self="closeModal"
      >
        <!-- Previous Product Button -->
        <button
          v-if="hasPrevious"
          @click="navigatePrevious"
          class="fixed left-4 top-1/2 -translate-y-1/2 z-50 w-12 h-12 rounded-full bg-gray-800/90 hover:bg-gray-700 flex items-center justify-center transition-all hover:scale-110 shadow-lg"
          title="Produto anterior (←)"
        >
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        <!-- Next Product Button -->
        <button
          v-if="hasNext"
          @click="navigateNext"
          class="fixed right-4 top-1/2 -translate-y-1/2 z-50 w-12 h-12 rounded-full bg-gray-800/90 hover:bg-gray-700 flex items-center justify-center transition-all hover:scale-110 shadow-lg"
          title="Próximo produto (→)"
        >
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>

        <div 
          class="relative bg-netflix-black-light rounded-lg max-w-6xl w-full max-h-[95vh] overflow-hidden animate-scale-up flex flex-col"
          @click.stop
        >
          <!-- Top Bar: Badges + Close -->
          <div class="flex items-center justify-between px-5 py-3 border-b border-gray-800 flex-shrink-0">
            <div class="flex items-center gap-2">
              <!-- Comparar Badge -->
              <div 
                v-if="isInComparison"
                class="bg-green-600 px-3 py-1 rounded-full text-white text-xs font-semibold flex items-center gap-1"
              >
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Comparar
              </div>
            </div>
            <!-- Close Button -->
            <button 
              @click="closeModal"
              class="w-9 h-9 rounded-full bg-gray-700 hover:bg-gray-600 flex items-center justify-center transition-colors"
            >
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Main Content: Product + Selected Sidebar -->
          <div class="flex flex-1 overflow-hidden">
            <!-- Left: Product Details -->
            <div class="flex-1 overflow-y-auto p-5">
              <!-- Centered Product Image -->
              <div class="w-full flex justify-center mb-4">
                <div class="w-72 h-72 bg-gray-900 rounded-lg overflow-hidden">
                  <img 
                    v-if="product.image"
                    :src="product.image"
                    :alt="product.name"
                    class="w-full h-full object-contain"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center text-gray-600">
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Product Info below Image -->
              <div class="mb-4">
                <h2 class="text-2xl font-bold text-white mb-1">{{ product.name }}</h2>
                <div class="flex items-center gap-3 text-sm text-gray-400 mb-2">
                  <span v-if="product.category">{{ product.category.name }}</span>
                  <span v-if="product.rating" class="flex items-center gap-1 text-yellow-400">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    {{ product.rating }}
                  </span>
                </div>
                <div v-if="displayPrice" class="mb-2">
                  <span class="text-netflix-red text-2xl font-bold">R$ {{ formatPrice(displayPrice) }}</span>
                  <span v-if="product.oldPrice" class="text-gray-500 text-sm line-through ml-2">R$ {{ formatPrice(product.oldPrice) }}</span>
                </div>
                <p v-if="product.description" class="text-gray-400 text-sm leading-relaxed">
                  {{ product.description }}
                </p>
              </div>

              <!-- Technical Specifications with internal scroll -->
              <div v-if="specificationsList.length" class="mb-4">
                <h3 class="text-base font-semibold text-white mb-2">Características Técnicas</h3>
                <div class="max-h-48 overflow-y-auto pr-1 custom-scrollbar">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-1">
                    <div 
                      v-for="(spec, index) in specificationsList" 
                      :key="index"
                      class="flex items-center py-1.5 border-b border-gray-800 text-sm"
                    >
                      <span class="text-gray-400 min-w-[120px]">{{ spec.key }}</span>
                      <span class="text-white font-medium">{{ spec.value }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Offers -->
              <div v-if="sortedOffers.length" class="mb-4">
                <h3 class="text-base font-semibold text-white mb-2">Onde Comprar</h3>
                <div class="space-y-1.5">
                  <div 
                    v-for="(offer, index) in sortedOffers" 
                    :key="index"
                    :class="[
                      'flex justify-between items-center px-3 py-2 rounded',
                      index === 0 ? 'bg-green-900/20 border border-green-700/40' : 'bg-netflix-gray-dark/30'
                    ]"
                  >
                    <div>
                      <div class="flex items-center gap-2">
                        <span class="text-white font-medium text-sm">{{ offer.marketplace || offer.supplier }}</span>
                        <span v-if="index === 0" class="text-[10px] bg-netflix-red text-white px-1.5 py-0.5 rounded font-semibold">MELHOR PREÇO</span>
                      </div>
                      <div class="text-xs text-gray-400">
                        <span v-if="offer.supplier" class="mr-2">Fornecedor: {{ offer.supplier }}</span>
                        <span>{{ offer.condition || 'Novo' }}</span>
                        <span class="text-gray-500 ml-2">• Preço cadastrado para este link</span>
                      </div>
                      <div v-if="index > 0 && offer.savingsFromPrevious" class="text-xs text-red-400 mt-0.5">
                        +R$ {{ formatPrice(offer.savingsFromPrevious) }} que a opção anterior
                      </div>
                      <div v-if="index > 0 && offer.savingsFromBest" class="text-xs text-yellow-500 mt-0.5">
                        +R$ {{ formatPrice(offer.savingsFromBest) }} que o melhor preço
                      </div>
                    </div>
                    <div class="text-right">
                      <div :class="['font-bold', index === 0 ? 'text-green-400' : 'text-netflix-red']">R$ {{ formatPrice(offer.price) }}</div>
                      <a 
                        v-if="offer.url"
                        :href="offer.url" 
                        target="_blank"
                        class="text-xs text-blue-400 hover:text-blue-300"
                      >
                        Ver oferta →
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action buttons -->
              <div class="flex gap-3 pt-2">
                <button 
                  @click="handleAddToFavorites"
                  class="flex-1 netflix-button-primary flex items-center justify-center gap-2 text-sm py-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                  Favoritos
                </button>
                <button 
                  @click="handleAddToCompare"
                  :disabled="!canAddToComparison && !isInComparison"
                  :class="[
                    'flex-1 flex items-center justify-center gap-2 text-sm py-2 rounded transition-colors',
                    isInComparison 
                      ? 'bg-green-600 hover:bg-green-700 text-white' 
                      : canAddToComparison
                        ? 'netflix-button-secondary'
                        : 'bg-gray-700 text-gray-500 cursor-not-allowed'
                  ]"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                  {{ isInComparison ? 'Remover da Comparação' : 'Comparar Produto' }}
                </button>
              </div>
            </div>

            <!-- Right Sidebar: Selected Products for Comparison -->
            <div class="w-56 flex-shrink-0 border-l border-gray-800 bg-netflix-black/50 p-4 overflow-y-auto hidden md:flex flex-col">
              <h4 class="text-sm font-semibold text-white mb-2">Produtos para Comparar</h4>
              
              <!-- Counter Badge -->
              <div class="mb-3 bg-netflix-red/20 border border-netflix-red/50 px-3 py-2 rounded-lg text-center">
                <div class="text-netflix-red text-lg font-bold">{{ comparisonCount }}/3</div>
                <div class="text-xs text-gray-400 mt-0.5">produtos selecionados</div>
              </div>
              
              <div v-if="selectedProducts.length === 0" class="text-xs text-gray-500 text-center py-6">
                Nenhum produto selecionado
              </div>

              <div class="space-y-3 flex-1">
                <div 
                  v-for="sp in selectedProducts" 
                  :key="sp.id"
                  class="flex items-center gap-2 bg-gray-800/60 rounded-lg p-2"
                >
                  <!-- Product thumbnail -->
                  <div class="w-10 h-10 flex-shrink-0 rounded overflow-hidden bg-gray-700">
                    <img 
                      v-if="sp.image"
                      :src="sp.image" 
                      :alt="sp.name" 
                      class="w-full h-full object-cover" 
                    />
                    <div v-else class="w-full h-full flex items-center justify-center text-gray-500">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                    </div>
                  </div>
                  <!-- Name + remove -->
                  <div class="flex-1 min-w-0">
                    <p class="text-xs text-white truncate">{{ sp.name }}</p>
                  </div>
                  <button 
                    @click.stop="handleRemoveFromComparison(sp.id)"
                    class="flex-shrink-0 w-6 h-6 rounded-full bg-red-600 hover:bg-red-500 flex items-center justify-center transition-colors"
                    title="Remover"
                  >
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Empty slots -->
              <div class="mt-2 space-y-2">
                <div 
                  v-for="n in emptySlots" 
                  :key="'slot-' + n"
                  class="flex items-center gap-2 border border-dashed border-gray-700 rounded-lg p-2"
                >
                  <div class="w-10 h-10 rounded bg-gray-800 flex items-center justify-center text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </div>
                  <span class="text-xs text-gray-600">Vazio</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { watch, computed } from 'vue'
import { useComparisonStore } from '../stores/comparison'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  product: {
    type: Object,
    default: () => ({})
  },
  products: {
    type: Array,
    default: () => []
  },
  currentIndex: {
    type: Number,
    default: 0
  }
})

const emit = defineEmits(['close', 'add-to-favorites', 'add-to-compare', 'navigate'])
const comparisonStore = useComparisonStore()

const isInComparison = computed(() => {
  return props.product?.id ? comparisonStore.isProductSelected(props.product.id) : false
})

const canAddToComparison = computed(() => {
  return comparisonStore.canAddMore || isInComparison.value
})

const comparisonCount = computed(() => comparisonStore.comparisonCount)
const selectedProducts = computed(() => comparisonStore.selectedProducts)
const emptySlots = computed(() => Math.max(0, 3 - comparisonStore.comparisonCount))

// Convert specifications object { key: value } into array [{ key, value }]
const specificationsList = computed(() => {
  const specs = props.product?.specifications
  if (!specs || typeof specs !== 'object') return []
  return Object.entries(specs).map(([key, value]) => ({ key, value }))
})

// Sort offers by price (cheapest first) and calculate savings between options
const sortedOffers = computed(() => {
  const offers = props.product?.offers
  if (!offers || !Array.isArray(offers) || offers.length === 0) return []
  
  const sorted = [...offers].sort((a, b) => parseFloat(a.price) - parseFloat(b.price))
  const bestPrice = parseFloat(sorted[0].price)
  
  return sorted.map((offer, index) => {
    const currentPrice = parseFloat(offer.price)
    const previousPrice = index > 0 ? parseFloat(sorted[index - 1].price) : null
    
    return {
      ...offer,
      savingsFromBest: index > 0 ? currentPrice - bestPrice : 0,
      savingsFromPrevious: previousPrice !== null ? currentPrice - previousPrice : 0,
    }
  })
})

// Navigation
const hasPrevious = computed(() => props.products.length > 0 && props.currentIndex > 0)
const hasNext = computed(() => props.products.length > 0 && props.currentIndex < props.products.length - 1)

const navigatePrevious = () => {
  if (hasPrevious.value) {
    emit('navigate', props.currentIndex - 1)
  }
}

const navigateNext = () => {
  if (hasNext.value) {
    emit('navigate', props.currentIndex + 1)
  }
}

const closeModal = () => {
  emit('close')
}

const formatPrice = (price) => {
  return parseFloat(price).toFixed(2).replace('.', ',')
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

const displayPrice = computed(() => {
  return getLowestPrice(props.product)
})

const handleAddToFavorites = () => {
  emit('add-to-favorites', props.product)
}

const handleAddToCompare = () => {
  if (isInComparison.value) {
    comparisonStore.removeProduct(props.product.id)
  } else {
    comparisonStore.addProduct(props.product)
  }
}

const handleRemoveFromComparison = (productId) => {
  comparisonStore.removeProduct(productId)
}

// Close on ESC key and navigate with arrow keys
watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    document.body.style.overflow = 'hidden'
    const handleKeydown = (e) => {
      if (e.key === 'Escape') {
        closeModal()
      } else if (e.key === 'ArrowLeft') {
        navigatePrevious()
      } else if (e.key === 'ArrowRight') {
        navigateNext()
      }
    }
    document.addEventListener('keydown', handleKeydown)
    return () => {
      document.removeEventListener('keydown', handleKeydown)
      document.body.style.overflow = ''
    }
  } else {
    document.body.style.overflow = ''
  }
})
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

.modal-enter-active .animate-scale-up,
.modal-leave-active .animate-scale-up {
  transition: transform 0.3s ease;
}

.modal-enter-from .animate-scale-up {
  transform: scale(0.9);
}

.modal-leave-to .animate-scale-up {
  transform: scale(0.9);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #555;
  border-radius: 2px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #777;
}
</style>
