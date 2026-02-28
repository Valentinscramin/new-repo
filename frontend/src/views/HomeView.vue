<template>
  <div class="min-h-screen bg-dark-bg">
    <!-- Hero Section -->
    <header class="relative overflow-hidden py-20 px-4">
      <div class="absolute inset-0 bg-gradient-to-br from-neon-green/10 via-transparent to-neon-blue/10"></div>
      <div class="relative max-w-6xl mx-auto text-center">
        <h1 class="text-5xl md:text-7xl font-bold mb-6 bg-gradient-to-r from-neon-green to-neon-blue bg-clip-text text-transparent">
          Compare. Analise. Escolha o Melhor Setup.
        </h1>
        <p class="text-xl md:text-2xl text-gray-400 mb-8">
          Comparador inteligente para gamers e nômades digitais.
        </p>
        <div class="flex gap-4 justify-center">
          <button v-if="!isAuthenticated" @click="router.push('/register')" class="btn-primary">
            Começar Agora
          </button>
          <button v-if="isAuthenticated" @click="scrollToComparison" class="btn-primary">
            Nova Comparação
          </button>
        </div>
      </div>
    </header>

    <!-- Comparison Form Section -->
    <section ref="comparisonSection" class="py-16 px-4">
      <div class="max-w-4xl mx-auto">
        <div class="card">
          <h2 class="text-3xl font-bold mb-8 text-center">Compare 3 Produtos</h2>
          
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <div v-for="(url, index) in urls" :key="index" class="space-y-2">
              <label :for="`url-${index}`" class="block text-sm font-medium text-gray-300">
                Link Produto {{ index + 1 }}
              </label>
              <input
                :id="`url-${index}`"
                v-model="urls[index]"
                type="url"
                placeholder="https://exemplo.com/produto"
                class="input-field"
                required
              />
            </div>

            <div v-if="error" class="p-4 bg-red-500/10 border border-red-500 rounded-lg text-red-400">
              {{ error }}
            </div>

            <button 
              type="submit" 
              :disabled="loading"
              class="w-full btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ loading ? 'Processando...' : 'Comparar Agora' }}
            </button>
          </form>
        </div>

        <!-- Call to Action -->
        <div v-if="!isAuthenticated" class="mt-12 text-center">
          <div class="card bg-gradient-to-r from-neon-green/10 to-neon-blue/10 border-neon-green/30">
            <h3 class="text-2xl font-bold mb-4">Crie sua conta e salve suas comparações</h3>
            <p class="text-gray-400 mb-6">
              Acesse seu histórico de comparações e tome decisões mais informadas.
            </p>
            <button @click="router.push('/register')" class="btn-primary">
              Registrar Gratuitamente
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useComparisonStore } from '@/stores/comparison'

const router = useRouter()
const authStore = useAuthStore()
const comparisonStore = useComparisonStore()

const urls = ref(['', '', ''])
const loading = ref(false)
const error = ref<string | null>(null)
const comparisonSection = ref<HTMLElement | null>(null)

const isAuthenticated = computed(() => authStore.isAuthenticated)

function scrollToComparison() {
  comparisonSection.value?.scrollIntoView({ behavior: 'smooth' })
}

async function handleSubmit() {
  error.value = null
  
  // Validate URLs
  for (const url of urls.value) {
    if (!url || !url.trim()) {
      error.value = 'Please provide all 3 URLs'
      return
    }
  }

  loading.value = true

  const result = await comparisonStore.createComparison(urls.value)
  
  if (result.success && result.comparisonId) {
    router.push(`/comparison/${result.comparisonId}`)
  } else {
    error.value = result.error || 'Failed to create comparison'
  }
  
  loading.value = false
}
</script>
