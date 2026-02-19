<template>
  <div class="min-h-screen bg-netflix-black text-white flex">
    <!-- Sidebar -->
    <aside 
      :class="[
        'fixed left-0 top-0 h-full bg-netflix-black-light border-r border-gray-800 transition-all duration-300 z-40 overflow-hidden shadow-2xl',
        isSidebarOpen ? 'w-72' : 'w-20'
      ]"
    >
      <!-- Logo -->
      <div class="h-16 flex items-center justify-center border-b border-gray-800 px-4">
        <router-link to="/" class="flex items-center gap-2">
          <component :is="LayoutDashboard" class="w-7 h-7 text-netflix-red" />
          <transition name="fade">
            <div v-if="isSidebarOpen" class="flex items-baseline gap-1">
              <span class="text-netflix-red text-xl font-bold tracking-tight">PORTAL</span>
              <span class="text-white text-xl font-bold tracking-tight">TECH</span>
            </div>
          </transition>
        </router-link>
      </div>

      <!-- Navigation -->
      <nav class="p-3 space-y-1 overflow-y-auto h-[calc(100vh-16rem)]">
        <router-link
          v-for="item in navigationItems"
          :key="item.path"
          :to="item.path"
          class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-netflix-gray-dark group relative"
          active-class="bg-netflix-red hover:bg-netflix-red-dark shadow-lg"
          :title="!isSidebarOpen ? item.name : ''"
        >
          <component :is="item.icon" class="w-5 h-5 flex-shrink-0 group-hover:scale-110 transition-transform" />
          <transition name="fade">
            <span v-if="isSidebarOpen" class="text-sm font-medium whitespace-nowrap">{{ item.name }}</span>
          </transition>
        </router-link>
      </nav>

      <!-- Divider -->
      <div class="mx-4 my-2 border-t border-gray-800"></div>

      <!-- User Section -->
      <div class="p-3 space-y-1 absolute bottom-0 left-0 right-0 bg-netflix-black-light">
        <router-link
          to="/dashboard"
          class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-netflix-gray-dark group"
          :title="!isSidebarOpen ? 'Perfil' : ''"
        >
          <component :is="User" class="w-5 h-5 flex-shrink-0 group-hover:scale-110 transition-transform" />
          <transition name="fade">
            <span v-if="isSidebarOpen" class="text-sm font-medium">Perfil</span>
          </transition>
        </router-link>

        <button
          @click="handleLogout"
          class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-red-900/50 text-left group"
          :title="!isSidebarOpen ? 'Sair' : ''"
        >
          <component :is="LogOut" class="w-5 h-5 flex-shrink-0 group-hover:scale-110 transition-transform" />
          <transition name="fade">
            <span v-if="isSidebarOpen" class="text-sm font-medium">Sair</span>
          </transition>
        </button>
      </div>

      <!-- Toggle Button -->
      <button
        @click="toggleSidebar"
        class="absolute -right-3 top-20 w-6 h-6 bg-netflix-red rounded-full flex items-center justify-center hover:bg-netflix-red-dark transition-all shadow-lg hover:scale-110"
        title="Recolher/Expandir menu"
      >
        <component 
          :is="isSidebarOpen ? ChevronLeft : ChevronRight"
          class="w-4 h-4 text-white"
        />
      </button>
    </aside>

    <!-- Main Content Area -->
    <div 
      :class="[
        'flex-1 transition-all duration-300 min-h-screen bg-netflix-black',
        isSidebarOpen ? 'ml-72' : 'ml-20'
      ]"
    >
      <!-- Top Bar -->
      <header class="h-16 bg-netflix-black-light/80 backdrop-blur-md border-b border-gray-800 flex items-center justify-between px-6 sticky top-0 z-30 shadow-lg">
        <div class="flex items-center gap-3">
          <component :is="getPageIcon()" class="w-6 h-6 text-netflix-red" />
          <div>
            <h1 class="text-xl font-bold text-white">{{ pageTitle }}</h1>
            <p v-if="pageSubtitle" class="text-xs text-gray-400">{{ pageSubtitle }}</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <!-- Search -->
          <button class="relative p-2 hover:bg-netflix-gray-dark rounded-lg transition-all group" title="Buscar">
            <component :is="Search" class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" />
          </button>

          <!-- Notifications -->
          <button class="relative p-2 hover:bg-netflix-gray-dark rounded-lg transition-all group" title="Notificações">
            <component :is="Bell" class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" />
            <span class="absolute top-1 right-1 w-2 h-2 bg-netflix-red rounded-full animate-pulse"></span>
          </button>

          <!-- Settings -->
          <button class="relative p-2 hover:bg-netflix-gray-dark rounded-lg transition-all group" title="Configurações">
            <component :is="Settings" class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" />
          </button>

          <!-- User Avatar -->
          <div class="flex items-center gap-2 pl-3 border-l border-gray-800">
            <div class="w-9 h-9 bg-gradient-to-br from-netflix-red to-netflix-red-dark rounded-full flex items-center justify-center shadow-lg">
              <span class="text-white text-sm font-semibold">{{ userInitial }}</span>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-6 min-h-screen">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useUIStore } from '../stores/ui'
import { 
  LayoutDashboard, 
  Grid3x3, 
  Building2, 
  Store, 
  Package, 
  User,
  LogOut,
  ChevronLeft,
  ChevronRight,
  Bell,
  Search,
  Settings
} from 'lucide-vue-next'

const router = useRouter()
const route = useRoute()
const uiStore = useUIStore()

const isSidebarOpen = computed({
  get: () => uiStore.isSidebarOpen,
  set: (value) => uiStore.setSidebarOpen(value)
})

const navigationItems = [
  {
    name: 'Dashboard',
    path: '/admin',
    icon: LayoutDashboard
  },
  {
    name: 'Categorias',
    path: '/admin/categories',
    icon: Grid3x3
  },
  {
    name: 'Fornecedores',
    path: '/admin/suppliers',
    icon: Building2
  },
  {
    name: 'Marketplaces',
    path: '/admin/marketplaces',
    icon: Store
  },
  {
    name: 'Produtos & Ofertas',
    path: '/admin/products',
    icon: Package
  }
]

const pageTitle = computed(() => {
  const item = navigationItems.find(item => item.path === route.path)
  if (item) return item.name
  const routeName = route.name || route.path.split('/').pop()
  return routeName.charAt(0).toUpperCase() + routeName.slice(1)
})

const pageSubtitle = computed(() => {
  return route.meta?.subtitle || ''
})

const getPageIcon = () => {
  const item = navigationItems.find(item => item.path === route.path)
  return item ? item.icon : LayoutDashboard
}

const userInitial = computed(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  return user.name ? user.name.charAt(0).toUpperCase() : 'A'
})

const toggleSidebar = () => {
  uiStore.toggleSidebar()
}

const handleLogout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/login')
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
