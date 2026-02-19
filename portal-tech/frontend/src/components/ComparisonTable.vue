<template>
  <div class="bg-gradient-to-b from-[#0b0f14]/60 to-[#0b0f14]/40 rounded-lg p-6 border border-gray-800" style="margin-bottom:20px;">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-700">
            <th class="py-4 px-6 text-left font-medium text-gray-400">Característica</th>
            <!-- Dynamic Product Headers -->
            <th 
              v-for="(product, idx) in displayProducts" 
              :key="'header-' + idx"
              :class="[
                'py-4 px-6 text-center font-medium relative',
                product && product.rank === 1 ? 'bg-gradient-to-b from-yellow-900/30 to-yellow-900/10 border-l-2 border-r-2 border-t-2 border-yellow-500/50' : ''
              ]"
            >
              <div v-if="product" class="flex flex-col items-center gap-2">
                <!-- Badge de Melhor Opção -->
                <div v-if="product.rank === 1" class="absolute -top-3 left-1/2 transform -translate-x-1/2 z-10">
                  <div class="bg-yellow-500 text-black text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1 shadow-lg">
                    <span>🏆</span>
                    <span>MELHOR OPÇÃO</span>
                  </div>
                </div>
                
                <img 
                  v-if="product.image" 
                  :src="product.image" 
                  :alt="product.name || product.title"
                  class="w-16 h-16 object-cover rounded-md"
                  :class="product.rank === 1 ? 'ring-2 ring-yellow-500' : ''"
                />
                <span class="text-white text-sm font-semibold">{{ product.name || product.title }}</span>
                
                <!-- Score do Produto -->
                <div v-if="product.score !== undefined" class="text-xs">
                  <span 
                    class="px-2 py-1 rounded font-bold"
                    :class="{
                      'bg-yellow-500/20 text-yellow-400': product.rank === 1,
                      'bg-gray-700 text-gray-300': product.rank !== 1
                    }"
                  >
                    Score: {{ formatScore(product.score) }}/100
                  </span>
                </div>
                
                <button 
                  v-if="interactive"
                  @click="$emit('remove-product', product.id)"
                  class="text-xs text-red-400 hover:text-red-300 transition-colors"
                >
                  Remover
                </button>
              </div>
              <div v-else class="py-8">
                <span class="text-gray-500 text-xs">Produto {{ getColumnLabel(idx) }}</span>
              </div>
            </th>
            <!-- Empty columns CTA -->
            <th 
              v-for="emptyIdx in emptyColumnsCount" 
              :key="'empty-' + emptyIdx"
              class="py-4 px-6 text-center"
            >
              <div class="flex flex-col items-center gap-2 py-4">
                <span class="text-gray-500 text-xs">Produto {{ getColumnLabel(displayProducts.length + emptyIdx - 1) }}</span>
                <button 
                  v-if="interactive"
                  @click="$emit('add-more')"
                  class="text-xs text-netflix-red hover:text-red-400 transition-colors"
                >
                  + Adicionar
                </button>
              </div>
            </th>
          </tr>
        </thead>
        <tbody class="text-gray-200">
          <tr v-for="(row, idx) in displayRows" :key="idx" class="border-b border-gray-800 last:border-0 hover:bg-white/2">
            <td class="py-4 px-6 text-left text-gray-300 font-medium">{{ row.feature }}</td>
            <td 
              v-for="(product, pIdx) in displayProducts" 
              :key="'cell-' + pIdx"
              :class="[
                'py-4 px-6 text-center',
                product && product.rank === 1 ? 'bg-yellow-900/10 border-l-2 border-r-2 border-yellow-500/50' : ''
              ]"
            >
              <span v-if="product">{{ formatValue(row.feature, row.values[pIdx]) }}</span>
              <span v-else class="text-gray-600">-</span>
            </td>
            <!-- Empty columns -->
            <td 
              v-for="emptyIdx in emptyColumnsCount" 
              :key="'empty-cell-' + emptyIdx"
              class="py-4 px-6 text-center text-gray-600"
            >
              -
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { formatScore } from '../utils/productRanking'

const props = defineProps({
  rows: { type: Array, default: () => [] },
  products: { type: Array, default: () => [] },
  interactive: { type: Boolean, default: false }
})

const emit = defineEmits(['remove-product', 'add-more'])

// Use products prop if available, otherwise fall back to static rows
const displayProducts = computed(() => {
  const prods = [...props.products]
  while (prods.length < 3) {
    prods.push(null)
  }
  return prods.slice(0, 3)
})

const emptyColumnsCount = computed(() => {
  return Math.max(0, 3 - props.products.length)
})

const displayRows = computed(() => {
  // If rows prop is provided (static data), use it
  if (props.rows && props.rows.length > 0) {
    return props.rows.map(row => ({
      feature: row.feature,
      values: [row.a, row.b, row.c]
    }))
  }

  // Otherwise, generate rows from products
  if (props.products.length === 0) {
    return []
  }

  // Collect all unique specification keys
  const allSpecs = new Set()
  props.products.forEach(product => {
    if (product.specifications) {
      Object.keys(product.specifications).forEach(key => allSpecs.add(key))
    }
  })

  // Add common fields
  const commonFields = ['Preço', 'Avaliação', 'Categoria']
  
  // Generate rows
  const rows = []

  // Common fields first
  commonFields.forEach(field => {
    const values = props.products.map(product => {
      if (field === 'Preço') return getLowestPrice(product)
      if (field === 'Avaliação') return product.rating || '-'
      if (field === 'Categoria') return product.category?.name || '-'
      return '-'
    })
    rows.push({ feature: field, values })
  })

  // Specifications
  allSpecs.forEach(spec => {
    const values = props.products.map(product => {
      const value = product.specifications?.[spec]
      return value !== undefined ? value : '-'
    })
    rows.push({ feature: spec, values })
  })

  return rows
})

function getColumnLabel(idx) {
  return ['A', 'B', 'C'][idx] || ''
}

function getLowestPrice(product) {
  if (!product) return '-'
  
  // Se o produto tem ofertas, pega o menor preço das ofertas
  if (product.offers && Array.isArray(product.offers) && product.offers.length > 0) {
    const prices = product.offers.map(offer => offer.price).filter(price => price !== null && price !== undefined)
    if (prices.length > 0) {
      return Math.min(...prices)
    }
  }
  
  // Caso contrário, use o preço do produto
  return product.price || '-'
}

function formatValue(feature, value) {
  if (!value || value === '-') return '-'
  
  if (feature === 'Avaliação' || feature === 'Rating') {
    return `⭐ ${value}`
  }
  
  if (feature === 'Preço' || feature === 'Price') {
    if (typeof value === 'number') {
      return `R$ ${value.toFixed(2).replace('.', ',')}`
    }
    return value
  }
  
  if (feature === 'Taxa de Atualização' || feature === 'Refresh Rate') {
    return `✓ ${value}`
  }
  
  return value
}
</script>

<style scoped>
.highlight {
  background: linear-gradient(90deg, rgba(16,185,129,0.08), rgba(16,185,129,0));
}
</style>
