<template>
  <div class="py-8 md:py-12">
    <!-- Section title -->
    <h2 class="text-2xl md:text-3xl font-bold text-white mb-4 px-4 md:px-8">
      {{ title }}
    </h2>

    <!-- Carousel container -->
    <div class="relative group" style="max-height: 260px;">
      <!-- Left arrow -->
      <button 
        v-if="showArrows && canScrollLeft"
        @click="scroll('left')"
        class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-12 h-12 bg-black/70 hover:bg-black/90 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 ml-2"
      >
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <!-- Products scroll container -->
      <div 
        ref="scrollContainer"
        class="flex overflow-x-auto gap-2 md:gap-4 px-4 md:px-8 py-6 md:py-8 carousel-container no-scrollbar snap-x snap-mandatory"
        @scroll="checkScroll"
      >
        <div 
          v-for="(product, index) in products" 
          :key="product.id || index"
          class="flex-shrink-0 w-48 md:w-64 carousel-item snap-start"
        style="margin: 20px;">
          <slot :product="product" :index="index">
            <!-- Default product card -->
            <div 
              class="netflix-card cursor-pointer h-full group/card"
              @click="$emit('product-click', product)"
            >
              <!-- Image -->
              <div class="relative aspect-video bg-gray-800 overflow-hidden">
                <img 
                  v-if="product.image"
                  :src="product.image"
                  :alt="product.name"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full flex items-center justify-center text-gray-600">
                  <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
                
                <!-- Comparison Badge -->
                <div 
                  v-if="isInComparison(product.id)" 
                  class="absolute right-2 top-2 bg-green-600 text-white text-xs font-semibold px-2 py-1 rounded shadow-lg flex items-center gap-1"
                >
                  <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  Comparar
                </div>
                
                <!-- Hover overlay -->
                <div class="absolute inset-0 bg-black/0 group-hover/card:bg-black/40 transition-all duration-300 flex items-center justify-center">
                  <div class="opacity-0 group-hover/card:opacity-100 transition-opacity duration-300">
                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>

                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-netflix-black-light via-transparent to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-300"></div>
              </div>

              <!-- Info -->
              <div class="p-3 md:p-4">
                <h3 class="text-white font-semibold text-sm md:text-base mb-2 truncate">
                  {{ product.name }}
                </h3>
                
                <div class="flex items-center justify-between mb-2">
                  <div>
                    <div v-if="product.price" class="text-netflix-red font-bold text-base md:text-lg">
                      R$ {{ formatPrice(product.price) }}
                    </div>
                    <div v-if="product.rating" class="flex items-center gap-1 text-xs text-gray-400">
                      <svg class="w-3 h-3 text-netflix-red" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                      {{ product.rating }}
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 mt-3">
                  <button 
                    v-if="getBuyUrl(product)"
                    @click.stop="openBuyUrl(product)"
                    class="flex-1 bg-white hover:bg-gray-200 text-netflix-black font-semibold text-xs md:text-sm py-2 px-2 rounded transition-colors flex items-center justify-center gap-1"
                    title="Ir para o site de compra"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="hidden md:inline">Comprar</span>
                  </button>
                  <button 
                    @click.stop="toggleComparison(product)"
                    :disabled="!canAddToComparison(product.id)"
                    :class="[
                      'p-2 rounded transition-colors',
                      isInComparison(product.id)
                        ? 'bg-netflix-red hover:bg-red-600 text-white' 
                        : canAddToComparison(product.id)
                          ? 'bg-netflix-gray-dark hover:bg-gray-600 text-white'
                          : 'bg-gray-800 text-gray-600 cursor-not-allowed'
                    ]"
                    :title="isInComparison(product.id) ? 'Remover da comparação' : 'Adicionar à comparação'"
                  >
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </slot>
        </div>

        <!-- Show more card (optional) -->
        <div 
          v-if="showMoreLink"
          class="flex-shrink-0 w-48 md:w-64 carousel-item snap-start"
        >
          <div 
            class="h-full netflix-card cursor-pointer flex items-center justify-center"
            @click="$emit('show-more')"
          >
            <div class="text-center p-6">
              <svg class="w-12 h-12 text-netflix-red mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
              <div class="text-white font-semibold">Ver Mais</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right arrow -->
      <button 
        v-if="showArrows && canScrollRight"
        @click="scroll('right')"
        class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-12 h-12 bg-black/70 hover:bg-black/90 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 mr-2"
      >
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useComparisonStore } from '../stores/comparison'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  products: {
    type: Array,
    default: () => []
  },
  showArrows: {
    type: Boolean,
    default: true
  },
  showMoreLink: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['product-click', 'show-more', 'add-to-compare'])

const comparisonStore = useComparisonStore()
const scrollContainer = ref(null)
const canScrollLeft = ref(false)
const canScrollRight = ref(false)

const formatPrice = (price) => {
  return parseFloat(price).toFixed(2).replace('.', ',')
}

const isInComparison = (productId) => {
  return comparisonStore.isProductSelected(productId)
}

const canAddToComparison = (productId) => {
  return comparisonStore.canAddMore || isInComparison(productId)
}

const toggleComparison = (product) => {
  if (isInComparison(product.id)) {
    comparisonStore.removeProduct(product.id)
  } else {
    const added = comparisonStore.addProduct(product)
    if (!added) {
      alert('Você já selecionou 3 produtos para comparação. Remova um produto antes de adicionar outro.')
    } else {
      emit('add-to-compare', product)
    }
  }
}

const getBuyUrl = (product) => {
  // Check for buy URL in different possible properties
  if (product.url) return product.url
  if (product.buyUrl) return product.buyUrl
  if (product.link) return product.link
  if (product.offers && product.offers.length > 0 && product.offers[0].url) {
    return product.offers[0].url
  }
  return null
}

const openBuyUrl = (product) => {
  const url = getBuyUrl(product)
  if (url) {
    window.open(url, '_blank')
  }
}

const scroll = (direction) => {
  if (!scrollContainer.value) return
  
  const scrollAmount = scrollContainer.value.offsetWidth * 0.8
  const scrollTo = direction === 'left' 
    ? scrollContainer.value.scrollLeft - scrollAmount
    : scrollContainer.value.scrollLeft + scrollAmount
  
  scrollContainer.value.scrollTo({
    left: scrollTo,
    behavior: 'smooth'
  })
}

const checkScroll = () => {
  if (!scrollContainer.value) return
  
  const { scrollLeft, scrollWidth, clientWidth } = scrollContainer.value
  canScrollLeft.value = scrollLeft > 10
  canScrollRight.value = scrollLeft < scrollWidth - clientWidth - 10
}

onMounted(() => {
  checkScroll()
  window.addEventListener('resize', checkScroll)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkScroll)
})
</script>

<style scoped>
.carousel-container {
  scroll-padding: 0 2rem;
}

/* Smooth scrolling on touch devices */
@media (hover: none) {
  .carousel-container {
    -webkit-overflow-scrolling: touch;
  }
}
</style>
