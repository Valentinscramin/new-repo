<template>
  <LoadingSpinner v-if="loading" message="Carregando dados..." />
  <div v-else class="space-y-6">
    <!-- Welcome Banner -->
    <div class="glass-effect rounded-xl p-6 border border-gray-800">
      <div class="flex items-start justify-between">
        <div>
          <h2 class="text-3xl font-bold text-white mb-2">Bem-vindo ao Portal Tech Admin! 👋</h2>
          <p class="text-gray-400">Aqui está um resumo das suas atividades recentes</p>
        </div>
        <component :is="TrendingUp" class="w-12 h-12 text-netflix-red" />
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
      <!-- Total Products -->
      <div class="glass-effect rounded-xl p-5 transition-all hover:shadow-xl hover:scale-105 border border-gray-800/50 group">
        <div class="flex items-center justify-between mb-3">
          <div class="w-12 h-12 bg-netflix-red/20 rounded-xl flex items-center justify-center group-hover:bg-netflix-red/30 transition-all">
            <component :is="Package" class="w-6 h-6 text-netflix-red" />
          </div>
          <div class="text-green-400 text-xs font-semibold flex items-center gap-1">
            <component :is="TrendingUp" class="w-3 h-3" />
            +12%
          </div>
        </div>
        <div class="text-gray-400 text-xs mb-1 uppercase tracking-wide">Produtos</div>
        <div class="text-3xl font-bold text-white">{{ stats.totalProducts }}</div>
      </div>

      <!-- Total Categories -->
      <div class="glass-effect rounded-xl p-5 transition-all hover:shadow-xl hover:scale-105 border border-gray-800/50 group">
        <div class="flex items-center justify-between mb-3">
          <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center group-hover:bg-blue-500/30 transition-all">
            <component :is="Grid3x3" class="w-6 h-6 text-blue-500" />
          </div>
          <div class="text-gray-400 text-xs font-semibold flex items-center gap-1">
            <component :is="Minus" class="w-3 h-3" />
            0%
          </div>
        </div>
        <div class="text-gray-400 text-xs mb-1 uppercase tracking-wide">Categorias</div>
        <div class="text-3xl font-bold text-white">{{ stats.totalCategories }}</div>
      </div>

      <!-- Total Offers -->
      <div class="glass-effect rounded-xl p-5 transition-all hover:shadow-xl hover:scale-105 border border-gray-800/50 group">
        <div class="flex items-center justify-between mb-3">
          <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center group-hover:bg-green-500/30 transition-all">
            <component :is="Tag" class="w-6 h-6 text-green-500" />
          </div>
          <div class="text-green-400 text-xs font-semibold flex items-center gap-1">
            <component :is="TrendingUp" class="w-3 h-3" />
            +8%
          </div>
        </div>
        <div class="text-gray-400 text-xs mb-1 uppercase tracking-wide">Ofertas</div>
        <div class="text-3xl font-bold text-white">{{ stats.totalOffers }}</div>
      </div>

      <!-- Total Marketplaces -->
      <div class="glass-effect rounded-xl p-5 transition-all hover:shadow-xl hover:scale-105 border border-gray-800/50 group">
        <div class="flex items-center justify-between mb-3">
          <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center group-hover:bg-purple-500/30 transition-all">
            <component :is="Store" class="w-6 h-6 text-purple-500" />
          </div>
          <div class="text-green-400 text-xs font-semibold flex items-center gap-1">
            <component :is="Check" class="w-3 h-3" />
            Ativo
          </div>
        </div>
        <div class="text-gray-400 text-xs mb-1 uppercase tracking-wide">Marketplaces</div>
        <div class="text-3xl font-bold text-white">{{ stats.totalMarketplaces }}</div>
      </div>

      <!-- Total Suppliers -->
      <div class="glass-effect rounded-xl p-5 transition-all hover:shadow-xl hover:scale-105 border border-gray-800/50 group">
        <div class="flex items-center justify-between mb-3">
          <div class="w-12 h-12 bg-orange-500/20 rounded-xl flex items-center justify-center group-hover:bg-orange-500/30 transition-all">
            <component :is="Building2" class="w-6 h-6 text-orange-500" />
          </div>
          <div class="text-blue-400 text-xs font-semibold flex items-center gap-1">
            <component :is="Check" class="w-3 h-3" />
            OK
          </div>
        </div>
        <div class="text-gray-400 text-xs mb-1 uppercase tracking-wide">Fornecedores</div>
        <div class="text-3xl font-bold text-white">{{ stats.totalSuppliers }}</div>
      </div>
    </div>

    <!-- Latest Updates -->
    <div class="glass-effect rounded-xl p-6 border border-gray-800">
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
          <component :is="Clock" class="w-6 h-6 text-netflix-red" />
          <h2 class="text-2xl font-bold text-white">Atualizações Recentes</h2>
        </div>
        <button class="text-netflix-red text-sm hover:text-netflix-red-dark transition-colors flex items-center gap-2 group">
          Ver Todas
          <component :is="ArrowRight" class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
        </button>
      </div>
      
      <div v-if="latestUpdates.length === 0" class="text-center py-16 text-gray-400">
        <component :is="Inbox" class="w-16 h-16 mx-auto mb-4 text-gray-600" />
        <p class="text-lg">Nenhuma atualização recente</p>
        <p class="text-sm mt-1">As atualizações aparecerão aqui</p>
      </div>
      
      <div v-else class="space-y-2">
        <div 
          v-for="update in latestUpdates" 
          :key="update.id" 
          class="flex justify-between items-center p-4 bg-netflix-gray-dark/30 rounded-xl hover:bg-netflix-gray-dark/50 transition-all group border border-gray-800/30"
        >
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-netflix-red/20 rounded-xl flex items-center justify-center group-hover:bg-netflix-red/30 transition-all">
              <component :is="Package" class="w-5 h-5 text-netflix-red" />
            </div>
            <div>
              <div class="font-semibold text-white flex items-center gap-2">
                {{ update.product }}
                <span class="text-xs text-green-400 bg-green-400/10 px-2 py-0.5 rounded-full">Novo</span>
              </div>
              <div class="text-sm text-gray-400 flex items-center gap-2 mt-1">
                <component :is="Store" class="w-3 h-3" />
                {{ update.marketplace }} • €{{ update.price }}
              </div>
            </div>
          </div>
          <div class="text-sm text-gray-500 flex items-center gap-2">
            <component :is="Clock" class="w-4 h-4" />
            {{ formatDate(update.updatedAt) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div>
      <div class="flex items-center gap-3 mb-4">
        <component :is="Zap" class="w-6 h-6 text-netflix-red" />
        <h2 class="text-2xl font-bold text-white">Ações Rápidas</h2>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <router-link 
          to="/admin/products" 
          class="glass-effect rounded-xl p-6 text-center transition-all hover:shadow-xl hover:scale-105 group border border-gray-800/50"
        >
          <div class="w-14 h-14 bg-netflix-red/20 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-netflix-red/30 transition-all">
            <component :is="Plus" class="w-7 h-7 text-netflix-red" />
          </div>
          <div class="text-white font-semibold text-lg">Novo Produto</div>
          <div class="text-gray-400 text-sm mt-1">Adicionar produto</div>
        </router-link>

        <router-link 
          to="/admin/products" 
          class="glass-effect rounded-xl p-6 text-center transition-all hover:shadow-xl hover:scale-105 group border border-gray-800/50"
        >
          <div class="w-14 h-14 bg-green-500/20 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-green-500/30 transition-all">
            <component :is="PlusCircle" class="w-7 h-7 text-green-500" />
          </div>
          <div class="text-white font-semibold text-lg">Gerenciar Ofertas</div>
          <div class="text-gray-400 text-sm mt-1">Ofertas dos produtos</div>
        </router-link>

        <router-link 
          to="/admin/categories" 
          class="glass-effect rounded-xl p-6 text-center transition-all hover:shadow-xl hover:scale-105 group border border-gray-800/50"
        >
          <div class="w-14 h-14 bg-blue-500/20 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-blue-500/30 transition-all">
            <component :is="FolderPlus" class="w-7 h-7 text-blue-500" />
          </div>
          <div class="text-white font-semibold text-lg">Nova Categoria</div>
          <div class="text-gray-400 text-sm mt-1">Adicionar categoria</div>
        </router-link>

        <router-link 
          to="/admin/suppliers" 
          class="glass-effect rounded-xl p-6 text-center transition-all hover:shadow-xl hover:scale-105 group border border-gray-800/50"
        >
          <div class="w-14 h-14 bg-orange-500/20 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-orange-500/30 transition-all">
            <component :is="UserPlus" class="w-7 h-7 text-orange-500" />
          </div>
          <div class="text-white font-semibold text-lg">Novo Fornecedor</div>
          <div class="text-gray-400 text-sm mt-1">Adicionar fornecedor</div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import LoadingSpinner from '../../components/LoadingSpinner.vue';
import { 
  Package, 
  Grid3x3, 
  Tag, 
  Store, 
  Building2,
  TrendingUp,
  Minus,
  Check,
  Clock,
  ArrowRight,
  Inbox,
  Zap,
  Plus,
  PlusCircle,
  FolderPlus,
  UserPlus
} from 'lucide-vue-next'

const loading = ref(true);
const stats = ref({
  totalProducts: 0,
  totalCategories: 0,
  totalOffers: 0,
  totalMarketplaces: 0,
  totalSuppliers: 0,
});
const latestUpdates = ref([]);

async function load() {
  loading.value = true;
  try {
    const res = await api.get('/admin/dashboard');
    stats.value = res.data.stats;
    latestUpdates.value = res.data.latestUpdates;
  } catch (e) {
    console.error('Failed to load dashboard:', e);
  } finally {
    loading.value = false;
  }
}

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleString('pt-BR', {
    day: '2-digit',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
  });
}

onMounted(load);
</script>

<style scoped>
</style>
