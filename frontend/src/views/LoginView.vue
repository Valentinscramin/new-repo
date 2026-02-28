<template>
  <div class="min-h-screen bg-dark-bg flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full">
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold mb-2 bg-gradient-to-r from-neon-green to-neon-blue bg-clip-text text-transparent">
          TechCompare
        </h1>
        <p class="text-gray-400">Entre na sua conta</p>
      </div>

      <div class="card">
        <form @submit.prevent="handleLogin" class="space-y-6">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
              Email
            </label>
            <input
              id="email"
              v-model="email"
              type="email"
              required
              class="input-field"
              placeholder="seu@email.com"
            />
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
              Senha
            </label>
            <input
              id="password"
              v-model="password"
              type="password"
              required
              class="input-field"
              placeholder="••••••••"
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
            {{ loading ? 'Entrando...' : 'Entrar' }}
          </button>
        </form>

        <div class="mt-6 text-center">
          <p class="text-gray-400">
            Não tem uma conta?
            <router-link to="/register" class="text-neon-green hover:underline">
              Registre-se
            </router-link>
          </p>
        </div>

        <div class="mt-4 text-center">
          <router-link to="/" class="text-gray-500 hover:text-gray-300 text-sm">
            ← Voltar para home
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref<string | null>(null)

async function handleLogin() {
  error.value = null
  loading.value = true

  const result = await authStore.login(email.value, password.value)

  if (result.success) {
    router.push('/dashboard')
  } else {
    error.value = result.error || 'Login failed'
  }

  loading.value = false
}
</script>
