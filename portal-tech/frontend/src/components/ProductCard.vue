<template>
  <div 
    class="netflix-card group cursor-pointer"
    @click="handleClick"
    @mouseenter="isHovering = true"
    @mouseleave="isHovering = false"
  >
    <!-- Image Container -->
    <div class="relative aspect-video bg-gray-900 overflow-hidden rounded-t-xl">
      <img 
        v-if="product.image"
        :src="product.image" 
        :alt="product.title || product.name" 
        class="w-full h-full object-contain"
      />
      <div v-else class="w-full h-full flex items-center justify-center text-gray-600">
        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>

      <!-- Badge -->
      <div 
        v-if="product.badge" 
        class="absolute left-2 top-2 bg-netflix-red text-white text-xs font-semibold px-3 py-1 rounded shadow-lg"
      >
        {{ product.badge }}
      </div>

      <!-- Comparison Badge -->
      <div 
        v-if="isInComparison" 
        class="absolute right-2 top-2 bg-green-600 text-white text-xs font-semibold px-3 py-1 rounded shadow-lg flex items-center gap-1"
      >
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
          <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
          <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
        </svg>
        Na Comparação
      </div>

      <!-- Hover Play Icon -->
      <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all duration-300 flex items-center justify-center">
        <div 
          class="opacity-0 group-hover:opacity-100 transition-all duration-300"
        >
          <div class="w-12 h-12 rounded-full bg-white/90 flex items-center justify-center">
            <svg class="w-6 h-6 text-netflix-black ml-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Gradient Overlay -->
      <div class="absolute inset-0 bg-gradient-to-t from-netflix-black-light via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </div>

    <!-- Info Container -->
    <div class="p-3 md:p-4 bg-netflix-black-light transition-all duration-300">
      <!-- Title -->
      <h3 class="font-bold text-base md:text-lg mb-2 text-white line-clamp-2 group-hover:text-netflix-red transition-colors">
        {{ product.title || product.name }}
      </h3>

      <!-- Rating and Price Row -->
      <div class="flex items-center justify-between mb-3">
        <!-- Rating -->
        <div v-if="product.rating" class="flex items-center gap-1">
          <svg class="w-4 h-4 text-netflix-red" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          <span class="text-white font-semibold text-sm">{{ product.rating }}</span>
        </div>

        <!-- Price -->
        <div v-if="displayPrice" class="text-netflix-red font-bold text-lg">
          R$ {{ formatPrice(displayPrice) }}
        </div>
      </div>

      <!-- Expanded Info on Hover -->
      <div 
        class="overflow-hidden transition-all duration-300"
        :class="isHovering ? 'max-h-40 opacity-100' : 'max-h-0 opacity-0'"
      >
        <p v-if="product.description" class="text-gray-300 text-sm mb-3 line-clamp-2">
          {{ product.description }}
        </p>
        
        <!-- Action Buttons -->
        <div class="flex gap-2">
          <button 
            @click.stop="$emit('preview', product)"
            class="flex-1 bg-white hover:bg-gray-200 text-netflix-black font-semibold text-sm py-2 px-3 rounded transition-colors flex items-center justify-center gap-1"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
            </svg>
            Preview
          </button>
          <button 
            @click.stop="toggleComparison"
            :disabled="!canAddToComparison && !isInComparison"
            :class="[
              'p-2 rounded transition-colors',
              isInComparison 
                ? 'bg-netflix-red hover:bg-red-600 text-white' 
                : canAddToComparison
                  ? 'bg-netflix-gray-dark hover:bg-gray-600 text-white'
                  : 'bg-gray-800 text-gray-600 cursor-not-allowed'
            ]"
            :title="isInComparison ? 'Remover da comparação' : 'Adicionar à comparação'"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </button>
          <button 
            @click.stop="$emit('add-to-favorites', product)"
            class="bg-netflix-gray-dark hover:bg-gray-600 text-white p-2 rounded transition-colors"
            title="Adicionar aos favoritos"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Default CTA slot (shown when not hovering) -->
      <div 
        v-if="!isHovering"
        class="transition-all duration-300"
      >
        <slot name="cta">
          <button 
            class="w-full bg-netflix-gray-dark hover:bg-gray-600 text-white font-semibold py-2 rounded transition-colors"
          >
            Ver Detalhes
          </button>
        </slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useComparisonStore } from '../stores/comparison'

const props = defineProps({
  product: { 
    type: Object, 
    default: () => ({ 
      title: 'Produto', 
      name: 'Produto',
      rating: '4.5', 
      image: '/img/monitor-1.svg', 
      badge: '',
      price: null,
      description: ''
    }) 
  }
})

defineEmits(['preview', 'add-to-favorites', 'click'])

const comparisonStore = useComparisonStore()
const isHovering = ref(false)

const isInComparison = computed(() => {
  return comparisonStore.isProductSelected(props.product.id)
})

const canAddToComparison = computed(() => {
  return comparisonStore.canAddMore || isInComparison.value
})

const toggleComparison = () => {
  if (isInComparison.value) {
    comparisonStore.removeProduct(props.product.id)
  } else {
    const added = comparisonStore.addProduct(props.product)
    if (!added) {
      // Show a notification that max products reached
      alert('Você já selecionou 3 produtos para comparação. Remova um produto antes de adicionar outro.')
    }
  }
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

const handleClick = () => {
  // Click handler can be used for navigation or opening modal
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
