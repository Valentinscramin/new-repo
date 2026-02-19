<template>
  <nav 
    :class="[
      'fixed top-0 left-0 right-0 z-50 transition-all duration-300 shadow-lg',
      isScrolled ? 'bg-netflix-black/95 backdrop-blur-md' : 'bg-gradient-to-b from-black/80 to-transparent'
    ]"
    @scroll="handleScroll"
  >
    <div class=" mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Logo -->
        <div class="flex-shrink-0 flex items-center gap-2">
          <router-link to="/" class="flex items-center gap-1 group">
            <component :is="Sparkles" class="w-7 h-7 text-netflix-red group-hover:animate-pulse" />
            <span class="text-netflix-red text-2xl font-bold tracking-tight">PORTAL</span>
            <span class="text-white text-2xl font-bold tracking-tight">TECH</span>
          </router-link>
        </div>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center space-x-6">
          <router-link 
            v-for="item in navigationItems" 
            :key="item.name"
            :to="item.path"
            class="flex items-center gap-1 text-gray-300 hover:text-white transition-colors duration-200 text-sm font-medium group relative"
            active-class="text-white"
          >
            <component :is="item.icon" class="w-4 h-4 group-hover:scale-110 transition-transform" style="margin-left: 5px;"/>
            {{ item.name }}
            <!-- Comparison Badge -->
            <span 
              v-if="item.name === 'Comparar' && comparisonCount > 0"
              class="absolute -top-2 -right-2 bg-netflix-red text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse"
            >
              {{ comparisonCount }}
            </span>
          </router-link>
        </div>

        <!-- Right side - Search & User -->
        <div class="hidden md:flex items-center space-x-3">
          <button 
            class="p-2 text-gray-300 hover:text-white transition-all hover:bg-gray-800 rounded-lg group"
            title="Buscar"
          >
            <component :is="Search" class="w-5 h-5 group-hover:scale-110 transition-transform" />
          </button>
          
          <div v-if="isAuthenticated" class="relative" ref="userMenuRef">
            <button 
              @click="toggleUserMenu"
              class="flex items-center gap-2 px-3 py-1.5 text-gray-300 hover:text-white transition-all rounded-lg hover:bg-gray-800"
            >
              <div class="w-8 h-8 bg-gradient-to-br from-netflix-red to-netflix-red-dark rounded-full flex items-center justify-center shadow-lg">
                <span class="text-white text-sm font-semibold">{{ userInitial }}</span>
              </div>
              <component 
                :is="ChevronDown"
                :class="['w-4 h-4 transition-transform', showUserMenu ? 'rotate-180' : '']"
              />
            </button>
            
            <!-- User Dropdown -->
            <transition name="fade">
              <div 
                v-if="showUserMenu"
                class="absolute right-0 mt-2 w-56 bg-netflix-black-light/95 backdrop-blur-md border border-gray-700 rounded-lg shadow-2xl py-1 overflow-hidden"
              >
                <router-link 
                  to="/dashboard" 
                  class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                  @click="showUserMenu = false"
                >
                  <component :is="LayoutDashboard" class="w-4 h-4" />
                  Meu Dashboard
                </router-link>
                <router-link 
                  to="/dashboard/favorites" 
                  class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                  @click="showUserMenu = false"
                >
                  <component :is="Heart" class="w-4 h-4" />
                  Favoritos
                </router-link>
                <router-link 
                  to="/dashboard/comparisons" 
                  class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                  @click="showUserMenu = false"
                >
                  <component :is="GitCompare" class="w-4 h-4" />
                  Minhas Comparações
                  <span 
                    v-if="comparisonCount > 0"
                    class="ml-auto bg-netflix-red text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"
                  >
                    {{ comparisonCount }}
                  </span>
                </router-link>
                <router-link 
                  v-if="isAdmin"
                  to="/admin" 
                  class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                  @click="showUserMenu = false"
                >
                  <component :is="Shield" class="w-4 h-4" />
                  Painel Admin
                </router-link>
                <hr class="my-1 border-gray-700">
                <button 
                  @click="handleLogout"
                  class="flex items-center gap-3 w-full text-left px-4 py-2.5 text-sm text-red-400 hover:bg-red-900/30 hover:text-red-300 transition-colors"
                >
                  <component :is="LogOut" class="w-4 h-4" />
                  Sair
                </button>
              </div>
            </transition>
          </div>

          <router-link 
            v-else
            to="/login" 
            class="netflix-button-primary flex items-center gap-2"
          >
            <component :is="LogIn" class="w-4 h-4" />
            Entrar
          </router-link>
        </div>

        <!-- Mobile menu button -->
        <div class="md:hidden">
          <button 
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="p-2 text-gray-300 hover:text-white transition-colors hover:bg-gray-800 rounded-lg"
          >
            <component :is="mobileMenuOpen ? X : Menu" class="w-6 h-6" />
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Menu -->
    <transition name="slide-down">
      <div v-if="mobileMenuOpen" class="md:hidden bg-netflix-black/95 backdrop-blur-md border-t border-gray-800">
        <div class="px-2 pt-2 pb-3 space-y-1">
          <router-link 
            v-for="item in navigationItems" 
            :key="item.name"
            :to="item.path"
            class="flex items-center gap-3 px-3 py-2.5 text-base font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors"
            @click="mobileMenuOpen = false"
          >
            <component :is="item.icon" class="w-5 h-5" />
            {{ item.name }}
          </router-link>
          
          <div v-if="isAuthenticated" class="border-t border-gray-800 pt-2 mt-2">
            <router-link 
              to="/dashboard" 
              class="flex items-center gap-3 px-3 py-2.5 text-base font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors"
              @click="mobileMenuOpen = false"
            >
              <component :is="LayoutDashboard" class="w-5 h-5" />
              Meu Dashboard
            </router-link>
            <router-link 
              v-if="isAdmin"
              to="/admin" 
              class="flex items-center gap-3 px-3 py-2.5 text-base font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors"
              @click="mobileMenuOpen = false"
            >
              <component :is="Shield" class="w-5 h-5" />
              Painel Admin
            </router-link>
            <button 
              @click="handleLogout"
              class="flex items-center gap-3 w-full text-left px-3 py-2.5 text-base font-medium text-red-400 hover:text-red-300 hover:bg-red-900/30 rounded-lg transition-colors"
            >
              <component :is="LogOut" class="w-5 h-5" />
              Sair
            </button>
          </div>
          
          <router-link 
            v-else
            to="/login" 
            class="flex items-center justify-center gap-2 px-3 py-2.5 text-base font-medium text-white bg-netflix-red hover:bg-netflix-red-dark rounded-lg text-center transition-colors mx-3 mt-2"
            @click="mobileMenuOpen = false"
          >
            <component :is="LogIn" class="w-5 h-5" />
            Entrar
          </router-link>
        </div>
      </div>
    </transition>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useComparisonStore } from '../stores/comparison'
import { 
  Home, 
  Package, 
  GitCompare, 
  Info, 
  Search, 
  ChevronDown,
  LayoutDashboard,
  Heart,
  Shield,
  LogOut,
  LogIn,
  Menu,
  X,
  Sparkles
} from 'lucide-vue-next'

const router = useRouter()
const comparisonStore = useComparisonStore()
const isScrolled = ref(false)
const mobileMenuOpen = ref(false)
const showUserMenu = ref(false)
const userMenuRef = ref(null)

const comparisonCount = computed(() => comparisonStore.comparisonCount)

const navigationItems = [
  { name: 'Início', path: '/', icon: Home },
  { name: 'Produtos', path: '/products', icon: Package },
  { name: 'Comparar', path: '/compare', icon: GitCompare },
  { name: 'Sobre', path: '/#about', icon: Info },
]

// Mock authentication - replace with actual auth logic
const isAuthenticated = computed(() => {
  return localStorage.getItem('token') !== null
})

const isAdmin = computed(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  return user.role === 'admin'
})

const userInitial = computed(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  return user.name ? user.name.charAt(0).toUpperCase() : 'U'
})

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const handleLogout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  showUserMenu.value = false
  mobileMenuOpen.value = false
  router.push('/login')
}

// Close user menu when clicking outside
const handleClickOutside = (event) => {
  if (userMenuRef.value && !userMenuRef.value.contains(event.target)) {
    showUserMenu.value = false
  }
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.slide-down-enter-active, .slide-down-leave-active {
  transition: all 0.3s ease;
}
.slide-down-enter-from, .slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
