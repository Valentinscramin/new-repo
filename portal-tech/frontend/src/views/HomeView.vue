<template>
  <LoadingSpinner v-if="loading" message="Carregando produtos..." />
  
  <!-- Error State -->
  <div v-else-if="error" class="min-h-screen bg-netflix-black pt-32 pb-12">
    <div class="max-w-4xl mx-auto px-4 text-center">
      <div class="glass-effect rounded-lg p-12">
        <svg class="w-20 h-20 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h2 class="text-2xl font-bold text-white mb-3">Erro ao carregar produtos</h2>
        <p class="text-gray-400 mb-2">{{ error }}</p>
        <p class="text-gray-500 text-sm mb-6">Verifique se o servidor backend está rodando em http://localhost:8000</p>
        <button @click="() => window.location.reload()" class="netflix-button-primary">
          Tentar Novamente
        </button>
      </div>
    </div>
  </div>
  
  <div v-else>
    <!-- Hero Banner Netflix Style -->
    <HeroBanner
      title="Descubra os <span class='text-netflix-red'>Melhores Produtos</span> de 2026"
      description="Compare preços, veja reviews reais e encontre as melhores ofertas em produtos de tecnologia. Sua decisão de compra nunca foi tão fácil."
      :buttons="[
        { text: 'Explorar Agora', variant: 'primary', action: 'explore', icon: 'PlayIcon' },
        { text: 'Saiba Mais', variant: 'secondary', action: 'info' }
      ]"
      @button-click="handleHeroAction"
    />

    <!-- Products Carousels -->
    <div class="bg-netflix-black py-8">
      <div class=" mx-auto px-4 md:px-8">
        <!-- Category Filter Badge -->
        <div v-if="selectedCategoryName" class="mb-6 flex items-center justify-center" style="margin-bottom:20px;">
          <div class="inline-flex items-center gap-3 glass-effect border border-netflix-red/30 rounded-full px-6 py-3">
            <div class="flex items-center gap-2">
              <div class="w-2 h-2 bg-netflix-red rounded-full animate-pulse"></div>
              <span class="text-gray-400 text-sm font-medium">Comparando:</span>
              <span class="text-white font-bold text-sm">{{ selectedCategoryName }}</span>
            </div>
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
        
        <!-- Filter Tabs -->
        <div class="flex flex-wrap gap-3 mb-6">
          <button 
            @click="selectedCarousel = 'featured'"
            :class="[
              'px-6 py-3 rounded-lg text-sm font-semibold transition-all',
              selectedCarousel === 'featured' 
                ? 'bg-netflix-red text-white shadow-lg shadow-netflix-red/50' 
                : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white'
            ]"
          >
            🌟 Destaque
          </button>
          <button 
            @click="selectedCarousel = 'best-rated'"
            :class="[
              'px-6 py-3 rounded-lg text-sm font-semibold transition-all',
              selectedCarousel === 'best-rated' 
                ? 'bg-netflix-red text-white shadow-lg shadow-netflix-red/50' 
                : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white'
            ]"
          >
            ⭐ Mais Avaliados
          </button>
          <button 
            @click="selectedCarousel = 'best-price'"
            :class="[
              'px-6 py-3 rounded-lg text-sm font-semibold transition-all',
              selectedCarousel === 'best-price' 
                ? 'bg-netflix-red text-white shadow-lg shadow-netflix-red/50' 
                : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white'
            ]"
          >
            💰 Melhores Ofertas
          </button>
          <button 
            @click="selectedCarousel = 'recent'"
            :class="[
              'px-6 py-3 rounded-lg text-sm font-semibold transition-all',
              selectedCarousel === 'recent' 
                ? 'bg-netflix-red text-white shadow-lg shadow-netflix-red/50' 
                : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white'
            ]"
          >
            🆕 Recentes
          </button>
        </div>
      </div>

      <!-- Featured Products -->
      <ProductCarousel
        v-if="selectedCarousel === 'featured' && featuredProducts.length"
        title="Produtos em Destaque"
        :products="featuredProducts"
        @product-click="openProductModal"
      />

      <!-- Best Rated -->
      <ProductCarousel
        v-if="selectedCarousel === 'best-rated' && bestRatedProducts.length"
        title="Mais Bem Avaliados"
        :products="bestRatedProducts"
        @product-click="openProductModal"
      />

      <!-- Best Prices -->
      <ProductCarousel
        v-if="selectedCarousel === 'best-price' && bestPriceProducts.length"
        title="Melhores Ofertas"
        :products="bestPriceProducts"
        @product-click="openProductModal"
        :showMoreLink="true"
        @show-more="scrollToOffers"
      />

      <!-- Recently Added -->
      <ProductCarousel
        v-if="selectedCarousel === 'recent' && recentProducts.length"
        title="Adicionados Recentemente"
        :products="recentProducts"
        @product-click="openProductModal"
      />
    </div>

    <!-- Comparison Section -->
    <section id="compare" class="py-16" style="margin-top: 20px;">
      <div class="mb-8 text-center mx-auto px-4 md:px-8">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-3">Comparação Detalhada</h2>
        <p class="text-gray-400 text-lg">
          {{ comparisonCount > 0 ? 'Veja lado a lado as especificações dos produtos selecionados' : 'Selecione produtos para comparar' }}
        </p>
      </div>
      
      <!-- Empty State -->
      <div v-if="comparisonCount === 0" class="mx-auto px-4 md:px-8" style="margin-top: 20px;">
        <div class="glass-effect rounded-lg p-12 text-center">
          <center><svg class="w-20 h-20 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg></center>
          <h3 class="text-xl font-bold text-white mb-2">Nenhum produto para comparar</h3>
          <p class="text-gray-400 mb-6">Clique no botão "Comparar" nos produtos para adicionar à comparação</p>
          <button @click="scrollToProducts" class="netflix-button-primary" style="margin-bottom: 20px; margin-top: 20px;">
            Ver Produtos
          </button>
        </div>
      </div>
      
      <!-- Comparison Table with Selected Products -->
      <div v-else class="w-full px-4 md:px-8">
        <ComparisonTable 
          :products="selectedProducts" 
          :interactive="true"
          @remove-product="handleRemoveProduct"
          @add-more="scrollToProducts"
        />
      </div>

      <!-- Ranking Summary (igual à página de comparação) -->
      <div v-if="comparisonCount >= 2" class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 w-full px-4 md:px-8">
        <div 
          v-for="product in selectedProducts" 
          :key="'rank-' + product.id"
          class="glass-effect rounded-lg p-6 relative overflow-hidden"
          :class="{
            'border-2 border-yellow-500/50 bg-yellow-900/10': product.rank === 1,
            'border border-gray-700': product.rank !== 1
          }"
        >
          <!-- Badge de Ranking -->
          <div class="absolute top-4 right-4">
            <div class="text-3xl" :title="getRankBadge(product.rank).text">
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
                <div class="bg-green-500 h-1.5 rounded-full transition-all" :style="{ width: `${formatScore(product.breakdown.priceScore)}%` }"></div>
              </div>
            </div>

            <div class="space-y-1">
              <div class="flex justify-between items-center text-xs">
                <span class="text-gray-500">⭐ Avaliação</span>
                <span class="text-gray-300">{{ formatScore(product.breakdown.ratingScore) }}/100</span>
              </div>
              <div class="w-full bg-gray-700 rounded-full h-1.5">
                <div class="bg-blue-500 h-1.5 rounded-full transition-all" :style="{ width: `${formatScore(product.breakdown.ratingScore)}%` }"></div>
              </div>
            </div>

            <div class="space-y-1">
              <div class="flex justify-between items-center text-xs">
                <span class="text-gray-500">🔧 Especificações</span>
                <span class="text-gray-300">{{ formatScore(product.breakdown.specsScore) }}/100</span>
              </div>
              <div class="w-full bg-gray-700 rounded-full h-1.5">
                <div class="bg-purple-500 h-1.5 rounded-full transition-all" :style="{ width: `${formatScore(product.breakdown.specsScore)}%` }"></div>
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
      
      <!-- Info Bar -->
      <div v-if="comparisonCount > 0" class="mt-6 w-full px-4 md:px-8" style="margin-top:20px;">
        <div class="glass-effect rounded-lg p-4">
          <div class="flex flex-col md:flex-row items-center gap-5 justify-between">
            <div class="flex items-center gap-3">
              <div class="bg-netflix-red/20 text-netflix-red p-2 rounded-full">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                  <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <div class="text-left">
                <p class="text-white font-semibold">{{ comparisonCount }} produto{{ comparisonCount > 1 ? 's' : '' }} selecionado{{ comparisonCount > 1 ? 's' : '' }}</p>
                <p class="text-gray-400 text-sm">Máximo: 3 produtos</p>
              </div>
            </div>
            <div class="flex gap-3 md:ml-auto">
              <router-link to="/compare" class="netflix-button-primary text-sm">
                Ver Comparação Completa
              </router-link>
              <button 
                @click="handleClearComparison" 
                class="netflix-button-secondary text-sm bg-red-900/20 hover:bg-red-900/40 text-red-400"
              >
                Limpar
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Where to Buy Section -->
    <section id="offers" class="py-16 bg-netflix-black-light" style="margin-top: 20px;">
      <div class="w-full flex justify-center">
        <div class="max-w-5xl w-full px-4 md:px-8">
          <h2 class="text-3xl font-bold text-white mb-8 text-center" style="margin-bottom: 20px;">Onde Comprar Mais Barato</h2>
          
          <!-- Indicador do produto vencedor -->
          <div v-if="winnerProduct && comparisonCount >= 2 && displayBestPrices.length > 0" class="mb-6 flex justify-center">
            <div class="inline-flex items-center gap-4 glass-effect border border-yellow-500/30 rounded-lg px-6 py-4">
              <img 
                v-if="winnerProduct.image" 
                :src="winnerProduct.image" 
                :alt="winnerProduct.name || winnerProduct.title" 
                class="w-12 h-12 object-cover rounded-md ring-2 ring-yellow-500"
              />
              <div>
                <div class="flex items-center gap-2">
                  <span class="text-yellow-400 text-lg">🏆</span>
                  <span class="text-yellow-400 font-bold text-sm">1º LUGAR</span>
                </div>
                <p class="text-white font-semibold">{{ winnerProduct.name || winnerProduct.title }}</p>
                <p class="text-gray-400 text-xs">Ofertas deste produto nos marketplaces</p>
              </div>
            </div>
          </div>

          <div v-if="displayBestPrices.length === 0" class="text-center text-gray-400 py-8">
            Nenhuma oferta disponível no momento
          </div>
          <div v-else class="space-y-3 mx-auto">
          <div 
            v-for="(item, idx) in displayBestPrices" 
            :key="idx" 
            :class="[
              'glass-effect rounded-lg p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 transition-all',
              idx === 0 ? 'border border-green-700/40 bg-green-900/10' : ''
            ]"
          >
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <span class="text-lg font-semibold text-white">{{ item.marketplace }}</span>
                <span v-if="idx === 0" class="text-xs bg-netflix-red text-white px-2 py-1 rounded font-semibold">MELHOR PREÇO</span>
              </div>
              <div class="text-xs text-gray-400 mt-0.5">
                <span v-if="item.supplier" class="mr-2">Fornecedor: {{ item.supplier }}</span>
                <span class="text-gray-500">• Preço exclusivo deste marketplace</span>
              </div>
              <div v-if="idx > 0 && item.savingsFromPrevious > 0" class="text-xs text-red-400 mt-1">
                +R$ {{ item.savingsFromPrevious.toFixed(2).replace('.', ',') }} que a opção anterior
              </div>
              <div v-if="idx > 0 && item.savingsFromBest > 0" class="text-xs text-yellow-500 mt-0.5">
                +R$ {{ item.savingsFromBest.toFixed(2).replace('.', ',') }} que o melhor preço
              </div>
              <div v-if="idx === 0 && displayBestPrices.length > 1" class="text-xs text-green-400 mt-1">
                Economize até R$ {{ displayBestPrices[displayBestPrices.length - 1].savingsFromBest.toFixed(2).replace('.', ',') }} comparado ao mais caro
              </div>
            </div>
            <div class="flex items-center gap-4">
              <span 
                :class="[
                  'text-2xl font-bold',
                  idx === 0 ? 'text-green-400' : 'text-white'
                ]"
              >
                {{ item.priceFormatted }}
              </span>
              <a 
                v-if="item.link" 
                :href="item.link" 
                target="_blank" 
                class="netflix-button-primary text-sm"
              >
                Ver Oferta →
              </a>
            </div>
          </div>
        </div>
      </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="py-12 mx-auto px-4" style="margin-top: 20px;">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
        <div class="p-6">
          <div class="text-4xl font-bold text-netflix-red mb-2">27+</div>
          <div class="text-gray-400">Modelos Analisados</div>
        </div>
        <div class="p-6">
          <div class="text-4xl font-bold text-netflix-red mb-2">1200+</div>
          <div class="text-gray-400">Reviews Verificadas</div>
        </div>
        <div class="p-6">
          <div class="text-4xl font-bold text-netflix-red mb-2">100%</div>
          <div class="text-gray-400">Atualizado Este Mês</div>
        </div>
      </div>
    </section>

    <!-- Final CTA -->
    <section class="py-20 mx-auto px-4 text-center" style="margin-top: 20px;">
      <h3 class="text-3xl md:text-4xl font-bold text-white mb-4">
        Pronto para Encontrar o Produto Perfeito?
      </h3>
      <p class="text-gray-400 text-lg mb-8">
        Junte-se a milhares de compradores inteligentes que economizam com nossas comparações
      </p>
      <button 
        @click="scrollToTop"
        class="netflix-button-primary text-lg px-10 py-4"
      style="margin-top: 20px; margin-bottom: 20px;">
        Começar Agora
      </button>
    </section>
  </div>

  <!-- Product Modal -->
  <ProductModal
    :isOpen="isModalOpen"
    :product="selectedProduct"
    :products="currentProductsList"
    :currentIndex="selectedProductIndex"
    @close="closeProductModal"
    @navigate="handleNavigateProduct"
    @add-to-favorites="handleAddToFavorites"
    @add-to-compare="handleAddToCompare"
  />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { getHomeData as getHomeDataApi, getAllProducts } from '../services/api'
import { useUIStore } from '../stores/ui'
import { useComparisonStore } from '../stores/comparison'

import HeroBanner from '../components/HeroBanner.vue'
import ProductCarousel from '../components/ProductCarousel.vue'
import ProductCard from '../components/ProductCard.vue'
import ProductModal from '../components/ProductModal.vue'
import ComparisonTable from '../components/ComparisonTable.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import { rankProducts, formatScore, getRankBadge } from '../utils/productRanking'

const router = useRouter()
const uiStore = useUIStore()
const comparisonStore = useComparisonStore()

const loading = ref(true)
const error = ref(null)
const sampleProducts = ref([])
const comparisonRows = ref([])
const bestPrices = ref([])
const allProducts = ref([])
const selectedCarousel = ref('featured') // Filtro dos carrosséis (featured, best-rated, best-price, recent)

// Comparison store data
const rawSelectedProducts = computed(() => comparisonStore.selectedProducts)
const comparisonCount = computed(() => comparisonStore.comparisonCount)
const selectedCategoryName = computed(() => comparisonStore.selectedCategoryName)

// Aplicar ranqueamento aos produtos selecionados (igual à página de comparação)
const selectedProducts = computed(() => {
  const products = rawSelectedProducts.value
  if (products.length > 0) {
    const ranked = rankProducts(products)
    return ranked.map(item => ({
      ...item.product,
      rank: item.rank,
      score: item.score,
      breakdown: item.breakdown
    }))
  }
  return products
})

// Produto vencedor (rank === 1)
const winnerProduct = computed(() => {
  return selectedProducts.value.find(p => p.rank === 1) || selectedProducts.value[0] || null
})

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

// Computed product groups (para carrosséis) - Limitado a 6 produtos cada
// Filtrados por categoria se houver comparação ativa
const featuredProducts = computed(() => {
  const filtered = filterByCategory(sampleProducts.value)
  return filtered.slice(0, 6)
})

const bestRatedProducts = computed(() => {
  const filtered = filterByCategory(sampleProducts.value)
  return filtered
    .filter(p => p.rating >= 4.5)
    .sort((a, b) => parseFloat(b.rating) - parseFloat(a.rating))
    .slice(0, 6)
})

const bestPriceProducts = computed(() => {
  const filtered = filterByCategory(sampleProducts.value)
  return filtered
    .filter(p => p.price)
    .sort((a, b) => parseFloat(a.price) - parseFloat(b.price))
    .slice(0, 6)
})

const recentProducts = computed(() => {
  const filtered = filterByCategory(sampleProducts.value)
  return filtered.slice().reverse().slice(0, 6)
})

// Computed para exibir as ofertas do produto vencedor (winner) ou bestPrices padrão
// Sempre ordenado por preço (do menor para o maior), com economia entre opções
const displayBestPrices = computed(() => {
  let rawOffers = []

  // Se há produtos selecionados para comparação, usa as ofertas do vencedor (produto com rank === 1)
  if (winnerProduct.value && winnerProduct.value.offers && winnerProduct.value.offers.length > 0) {
    rawOffers = [...winnerProduct.value.offers].map(offer => ({
      marketplace: offer.marketplace || 'Loja',
      supplier: offer.supplier || null,
      price: parseFloat(offer.price),
      link: offer.url || offer.affiliateLink || '#'
    }))
  }

  // Fallback: usa bestPrices da API
  if (rawOffers.length === 0) {
    rawOffers = bestPrices.value.map(item => ({
      marketplace: item.marketplace,
      supplier: item.supplier || null,
      price: typeof item.price === 'number' ? item.price : parseFloat(String(item.price).replace(/[^\d.,]/g, '').replace(',', '.')),
      link: item.link || '#'
    }))
  }

  // Ordenar por preço (menor para maior)
  rawOffers.sort((a, b) => a.price - b.price)

  // Calcular economia entre opções
  const bestPrice = rawOffers.length > 0 ? rawOffers[0].price : 0
  return rawOffers.map((offer, index) => ({
    ...offer,
    priceFormatted: `R$ ${offer.price.toFixed(2).replace('.', ',')}`,
    savingsFromBest: index > 0 ? (offer.price - bestPrice) : 0,
    savingsFromPrevious: index > 0 ? (offer.price - rawOffers[index - 1].price) : 0,
  }))
})

// Modal state
const selectedProduct = ref(null)
const selectedProductIndex = ref(0)
const currentProductsList = ref([])
const isModalOpen = computed(() => selectedProduct.value !== null)

const openProductModal = (product) => {
  selectedProduct.value = product
  // Determine which carousel the product belongs to and set the current list
  // Check in order: featured, best-rated, best-price, recent
  if (featuredProducts.value.some(p => p.id === product.id)) {
    currentProductsList.value = featuredProducts.value
  } else if (bestRatedProducts.value.some(p => p.id === product.id)) {
    currentProductsList.value = bestRatedProducts.value
  } else if (bestPriceProducts.value.some(p => p.id === product.id)) {
    currentProductsList.value = bestPriceProducts.value
  } else if (recentProducts.value.some(p => p.id === product.id)) {
    currentProductsList.value = recentProducts.value
  } else {
    currentProductsList.value = [product]
  }
  selectedProductIndex.value = currentProductsList.value.findIndex(p => p.id === product.id)
  uiStore.openProductModal(product)
}

const closeProductModal = () => {
  selectedProduct.value = null
  currentProductsList.value = []
  selectedProductIndex.value = 0
  uiStore.closeProductModal()
}

const handleNavigateProduct = (newIndex) => {
  if (newIndex >= 0 && newIndex < currentProductsList.value.length) {
    selectedProductIndex.value = newIndex
    selectedProduct.value = currentProductsList.value[newIndex]
  }
}

const handleAddToFavorites = (product) => {
  console.log('Add to favorites:', product)
  // Implement favorite logic here
  alert(`${product.name || product.title} adicionado aos favoritos!`)
}

const handleAddToCompare = (product) => {
  const added = comparisonStore.addProduct(product)
  if (added) {
    console.log('Product added to comparison:', product)
    // Optional: Show a success message or toast
  } else {
    console.warn('Could not add product to comparison')
  }
}

const handleRemoveProduct = (productId) => {
  comparisonStore.removeProduct(productId)
}

const handleClearComparison = () => {
  if (confirm('Tem certeza que deseja limpar toda a comparação?')) {
    comparisonStore.clearAll()
  }
}

const scrollToProducts = () => {
  document.querySelector('#products')?.scrollIntoView({ behavior: 'smooth' })
}

const handleHeroAction = (action) => {
  if (action === 'explore') {
    scrollToProducts()
  } else if (action === 'info') {
    document.querySelector('#offers')?.scrollIntoView({ behavior: 'smooth' })
  }
}

const scrollToOffers = () => {
  document.querySelector('#offers')?.scrollIntoView({ behavior: 'smooth' })
}

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const calculateSavings = (bestPrice, worstPrice) => {
  const best = parseFloat(bestPrice.replace(/[^\d,]/g, '').replace(',', '.'))
  const worst = parseFloat(worstPrice.replace(/[^\d,]/g, '').replace(',', '.'))
  const savings = worst - best
  return `R$ ${savings.toFixed(2).replace('.', ',')}`
}

const formatPrice = (price) => {
  if (typeof price === 'number') {
    return price.toFixed(2).replace('.', ',')
  }
  return price
}

const getLowestPrice = (product) => {
  if (!product) return null
  if (product.offers && Array.isArray(product.offers) && product.offers.length > 0) {
    const prices = product.offers.map(offer => offer.price).filter(price => price !== null && price !== undefined)
    if (prices.length > 0) return Math.min(...prices)
  }
  return product.price || null
}

onMounted(async () => {
  loading.value = true
  error.value = null
  try {
    console.log('🔄 Carregando dados da home...')
    
    // Buscar dados da home (para carrosséis e comparação)
    const data = await getHomeDataApi()
    console.log('✅ Dados recebidos da API /home:', {
      sampleProducts: data.sampleProducts?.length || 0,
      comparisonRows: data.comparisonRows?.length || 0,
      bestPrices: data.bestPrices?.length || 0
    })
    
    sampleProducts.value = data.sampleProducts || []
    comparisonRows.value = data.comparisonRows || []
    bestPrices.value = data.bestPrices || []

    // Buscar todos os produtos do backend para o catálogo
    console.log('🔄 Carregando todos os produtos...')
    const products = await getAllProducts()
    console.log('✅ Produtos carregados:', products?.length || 0)
    allProducts.value = Array.isArray(products) ? products : []
    
    console.log('✨ Carregamento concluído com sucesso!')
  } catch (err) {
    console.error('❌ Erro ao carregar dados:', err)
    error.value = err.message || 'Erro ao carregar produtos. Verifique se o backend está rodando.'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
/* View-specific adjustments handled by Tailwind utility classes */
</style>

