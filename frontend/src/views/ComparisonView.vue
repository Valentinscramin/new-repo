<template>
  <div class="min-h-screen bg-dark-bg py-12 px-4">
    <div class="max-w-7xl mx-auto">
      <!-- Loading State -->
      <div v-if="loading && !comparison" class="text-center py-20">
        <div class="inline-block animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-neon-green"></div>
        <p class="mt-4 text-xl text-gray-400">Analisando hardware e performance...</p>
      </div>

      <!-- Processing State -->
      <div v-else-if="comparison && comparison.status === 'processing'" class="text-center py-20">
        <div class="inline-block animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-neon-green"></div>
        <p class="mt-4 text-xl text-gray-400">Processando comparação...</p>
        <p class="mt-2 text-sm text-gray-500">Isso pode levar alguns minutos</p>
        <button @click="refresh" class="mt-6 btn-secondary">
          Atualizar Status
        </button>
      </div>

      <!-- Completed State -->
      <div v-else-if="comparison && comparison.status === 'completed'">
        <!-- Header -->
        <div class="mb-8">
          <router-link to="/" class="text-neon-green hover:underline mb-4 inline-block">
            ← Nova Comparação
          </router-link>
          <h1 class="text-4xl font-bold mb-2">Resultado da Comparação</h1>
          <p class="text-gray-400">{{ new Date(comparison.createdAt).toLocaleString('pt-BR') }}</p>
        </div>

        <!-- Winner Card -->
        <div v-if="winnerProduct" class="card border-2 border-neon-green shadow-lg shadow-neon-green/20 mb-8">
          <div class="flex items-center gap-4 mb-4">
            <span class="text-4xl">🏆</span>
            <h2 class="text-3xl font-bold text-neon-green">Melhor Escolha</h2>
          </div>
          <h3 class="text-2xl font-bold mb-2">{{ winnerProduct.name }}</h3>
          <p class="text-gray-400 mb-4">
            Baseado em performance, preço e especificações técnicas, este produto oferece o melhor equilíbrio entre custo e benefício.
          </p>
          <div class="flex gap-4 items-center">
            <span class="text-3xl font-bold text-neon-green">Score: {{ winnerProduct.score }}/100</span>
            <span v-if="winnerProduct.price" class="text-xl text-gray-300">
              {{ winnerProduct.currency }} {{ winnerProduct.price }}
            </span>
          </div>
        </div>

        <!-- Comparison Table -->
        <div class="card overflow-x-auto">
          <h2 class="text-2xl font-bold mb-6">Comparação Detalhada</h2>
          <table class="w-full">
            <thead>
              <tr class="border-b border-dark-border">
                <th class="text-left p-4 text-gray-400 font-semibold">Especificação</th>
                <th v-for="product in comparison.products" :key="product.id" 
                    class="text-left p-4 font-semibold"
                    :class="product.id === comparison.winnerId ? 'text-neon-green' : ''">
                  {{ product.name }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr class="border-b border-dark-border">
                <td class="p-4 text-gray-400">Marca</td>
                <td v-for="product in comparison.products" :key="product.id" class="p-4">
                  {{ product.brand || '-' }}
                </td>
              </tr>
              <tr class="border-b border-dark-border">
                <td class="p-4 text-gray-400">Preço</td>
                <td v-for="product in comparison.products" :key="product.id" class="p-4">
                  {{ product.price ? `${product.currency} ${product.price}` : '-' }}
                </td>
              </tr>
              <tr class="border-b border-dark-border">
                <td class="p-4 text-gray-400">Score</td>
                <td v-for="product in comparison.products" :key="product.id" class="p-4">
                  <span class="font-bold" :class="product.id === comparison.winnerId ? 'text-neon-green' : ''">
                    {{ product.score || '-' }}/100
                  </span>
                </td>
              </tr>
              <tr v-if="hasSpecs('processor')" class="border-b border-dark-border">
                <td class="p-4 text-gray-400">Processador</td>
                <td v-for="product in comparison.products" :key="product.id" class="p-4">
                  {{ product.specs?.processor || '-' }}
                </td>
              </tr>
              <tr v-if="hasSpecs('gpu')" class="border-b border-dark-border">
                <td class="p-4 text-gray-400">GPU</td>
                <td v-for="product in comparison.products" :key="product.id" class="p-4">
                  {{ product.specs?.gpu || '-' }}
                </td>
              </tr>
              <tr v-if="hasSpecs('ram')" class="border-b border-dark-border">
                <td class="p-4 text-gray-400">RAM</td>
                <td v-for="product in comparison.products" :key="product.id" class="p-4">
                  {{ product.specs?.ram || '-' }}
                </td>
              </tr>
              <tr v-if="hasSpecs('storage')" class="border-b border-dark-border">
                <td class="p-4 text-gray-400">Armazenamento</td>
                <td v-for="product in comparison.products" :key="product.id" class="p-4">
                  {{ product.specs?.storage || '-' }}
                </td>
              </tr>
              <tr v-if="hasSpecs('screen')" class="border-b border-dark-border">
                <td class="p-4 text-gray-400">Tela</td>
                <td v-for="product in comparison.products" :key="product.id" class="p-4">
                  {{ product.specs?.screen || '-' }}
                </td>
              </tr>
              <tr v-if="hasSpecs('weight')" class="border-b border-dark-border">
                <td class="p-4 text-gray-400">Peso</td>
                <td v-for="product in comparison.products" :key="product.id" class="p-4">
                  {{ product.specs?.weight || '-' }}
                </td>
              </tr>
              <tr v-if="hasSpecs('battery')" class="border-b border-dark-border">
                <td class="p-4 text-gray-400">Bateria</td>
                <td v-for="product in comparison.products" :key="product.id" class="p-4">
                  {{ product.specs?.battery || '-' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Strengths & Weaknesses -->
        <div class="grid md:grid-cols-3 gap-6 mt-8">
          <div v-for="product in comparison.products" :key="product.id" class="card">
            <h3 class="text-xl font-bold mb-4">{{ product.name }}</h3>
            
            <div class="mb-4">
              <h4 class="text-neon-green font-semibold mb-2">✓ Pontos Fortes</h4>
              <ul class="space-y-1 text-sm text-gray-300">
                <li v-for="(strength, idx) in product.strengths" :key="idx">• {{ strength }}</li>
              </ul>
            </div>
            
            <div>
              <h4 class="text-red-400 font-semibold mb-2">✗ Pontos Fracos</h4>
              <ul class="space-y-1 text-sm text-gray-300">
                <li v-for="(weakness, idx) in product.weaknesses" :key="idx">• {{ weakness }}</li>
              </ul>
            </div>

            <a v-if="product.url" :href="product.url" target="_blank" 
               class="mt-4 block text-center btn-secondary text-sm">
              Ver Produto
            </a>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-20">
        <p class="text-xl text-red-400">{{ error }}</p>
        <router-link to="/" class="mt-4 inline-block btn-primary">
          Voltar para Home
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useComparisonStore } from '@/stores/comparison'

const route = useRoute()
const comparisonStore = useComparisonStore()

const comparison = computed(() => comparisonStore.currentComparison)
const loading = ref(false)
const error = ref<string | null>(null)

const winnerProduct = computed(() => {
  if (!comparison.value) return null
  return comparison.value.products.find(p => p.id === comparison.value?.winnerId)
})

async function fetchComparison() {
  const id = parseInt(route.params.id as string)
  loading.value = true
  const result = await comparisonStore.fetchComparison(id)
  if (!result.success) {
    error.value = result.error || 'Failed to load comparison'
  }
  loading.value = false
}

function hasSpecs(key: string): boolean {
  return comparison.value?.products.some(p => p.specs?.[key]) || false
}

async function refresh() {
  await fetchComparison()
}

onMounted(() => {
  fetchComparison()
  
  // Auto-refresh if processing
  const interval = setInterval(() => {
    if (comparison.value?.status === 'processing') {
      fetchComparison()
    } else {
      clearInterval(interval)
    }
  }, 5000) // Check every 5 seconds
})
</script>
