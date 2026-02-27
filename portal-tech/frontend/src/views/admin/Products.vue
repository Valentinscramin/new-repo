<template>
  <div class="min-h-screen flex items-start justify-center">
    <div class=" w-full py-8 px-4">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold">Produtos & Ofertas</h2>
      <div class="flex gap-3">
        <router-link to="/admin" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded transition">← Back</router-link>
        <button @click="showCreateForm = true" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded transition">+ Novo Produto</button>
      </div>
    </div>

    <div v-if="showCreateForm || editingItem" class="mb-6 p-4 bg-gray-900 rounded-lg border border-gray-800">
      <h3 class="text-lg font-semibold mb-4">{{ editingItem ? 'Editar' : 'Criar' }} Produto</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <input v-model="form.name" placeholder="Nome *" class="px-4 py-2 bg-gray-800 rounded border border-gray-700" />
        <input v-model="form.slug" placeholder="Slug *" class="px-4 py-2 bg-gray-800 rounded border border-gray-700" />
        <select v-model="form.categoryId" class="px-4 py-2 bg-gray-800 rounded border border-gray-700">
          <option value="">Selecione Categoria *</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
        <select v-model="form.supplierId" class="px-4 py-2 bg-gray-800 rounded border border-gray-700">
          <option value="">Select Supplier (opcional)</option>
          <option v-for="sup in suppliers" :key="sup.id" :value="sup.id">{{ sup.name }}</option>
        </select>
        <textarea v-model="form.description" placeholder="Descrição (opcional)" class="px-4 py-2 bg-gray-800 rounded border border-gray-700 md:col-span-2" rows="3"></textarea>
      </div>
      <div class="flex gap-3">
        <button @click="save" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded transition">Salvar</button>
        <button @click="cancelEdit" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded transition">Cancelar</button>
      </div>
    </div>

    <LoadingSpinner v-if="loading" message="Carregando produtos..." />
    <div v-else-if="items.length === 0" class="text-gray-400">Nenhum produto ainda.</div>
    <div v-else class="bg-gray-900 rounded-lg border border-gray-800 overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-800">
          <tr>
            <th class="text-left p-4">Nome</th>
            <th class="text-left p-4">Categoria</th>
            <th class="text-left p-4">Fornecedor</th>
            <th class="text-left p-4">Ofertas</th>
            <th class="text-right p-4">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items" :key="item.id" class="border-t border-gray-800 hover:bg-gray-800/50">
            <td class="p-4">{{ item.name }}</td>
            <td class="p-4 text-gray-400">{{ item.category?.name || 'N/A' }}</td>
            <td class="p-4 text-gray-400">{{ item.supplier?.name || 'N/A' }}</td>
            <td class="p-4">
              <button @click="viewOffers(item)" class="px-3 py-1 bg-purple-600 hover:bg-purple-700 rounded text-sm">
                Ver Ofertas {{ item.offersCount || 0 ? `(${item.offersCount})` : '' }}
              </button>
            </td>
            <td class="p-4 text-right">
              <button @click="edit(item)" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 rounded text-sm mr-2">Editar</button>
              <button @click="deleteItem(item.id)" class="px-3 py-1 bg-red-600 hover:bg-red-700 rounded text-sm">Deletar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Offers Modal -->
    <div v-if="showOffersModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="closeOffersModal">
      <div class="bg-gray-900 rounded-lg border border-gray-800 w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gray-900 border-b border-gray-800 p-6 flex justify-between items-center">
          <h3 class="text-xl font-bold">Ofertas: {{ selectedProduct?.name }}</h3>
          <button @click="closeOffersModal" class="text-gray-400 hover:text-white text-2xl">&times;</button>
        </div>
        
        <div class="p-6">
          <!-- Add Offer Form -->
          <div v-if="showOfferForm || editingOffer" class="mb-6 p-4 bg-gray-800 rounded-lg">
            <h4 class="text-lg font-semibold mb-4">{{ editingOffer ? 'Editar' : 'Adicionar' }} Oferta</h4>
            <div class="grid grid-cols-1 gap-4">
              <select v-model="offerForm.marketplaceId" class="px-4 py-2 bg-gray-700 rounded border border-gray-600">
                <option value="">Selecione Marketplace *</option>
                <option v-for="mp in marketplaces" :key="mp.id" :value="mp.id">{{ mp.name }}</option>
              </select>
              <input v-model.number="offerForm.price" type="number" step="0.01" placeholder="Preço *" class="px-4 py-2 bg-gray-700 rounded border border-gray-600" />
              <input v-model="offerForm.affiliateLink" placeholder="Link de Afiliado *" class="px-4 py-2 bg-gray-700 rounded border border-gray-600" />
            </div>
            <div class="flex gap-3 mt-4">
              <button @click="saveOffer" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded transition">Salvar</button>
              <button @click="cancelOfferEdit" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded transition">Cancelar</button>
            </div>
          </div>

          <button v-if="!showOfferForm && !editingOffer" @click="showOfferForm = true" class="mb-4 px-4 py-2 bg-green-600 hover:bg-green-700 rounded transition">+ Nova Oferta</button>

          <!-- Offers List -->
          <LoadingSpinner v-if="loadingOffers" message="Carregando ofertas..." />
          <div v-else-if="offers.length === 0" class="text-gray-400 text-center py-8">
            Nenhuma oferta ainda. Adicione a primeira oferta!
          </div>
          <div v-else class="space-y-3">
            <div v-for="offer in offers" :key="offer.id" class="p-4 bg-gray-800 rounded-lg flex items-center justify-between">
              <div class="flex-1">
                <div class="font-semibold text-lg">{{ offer.marketplace?.name || 'N/A' }}</div>
                <div class="text-green-400 text-xl font-bold">R$ {{ offer.price?.toFixed(2) }}</div>
                <div class="text-gray-400 text-sm truncate max-w-md">{{ offer.affiliateLink }}</div>
                <div class="text-gray-500 text-xs mt-1">Atualizado: {{ formatDate(offer.lastUpdatedAt) }}</div>
              </div>
              <div class="flex gap-2">
                <button @click="editOffer(offer)" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 rounded text-sm">Editar</button>
                <button @click="deleteOffer(offer.id)" class="px-3 py-1 bg-red-600 hover:bg-red-700 rounded text-sm">Deletar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import LoadingSpinner from '../../components/LoadingSpinner.vue';

const items = ref([]);
const categories = ref([]);
const suppliers = ref([]);
const marketplaces = ref([]);
const loading = ref(false);
const showCreateForm = ref(false);
const editingItem = ref(null);
const form = ref({ name: '', slug: '', categoryId: '', supplierId: '', description: '' });

// Offers management
const showOffersModal = ref(false);
const selectedProduct = ref(null);
const offers = ref([]);
const loadingOffers = ref(false);
const showOfferForm = ref(false);
const editingOffer = ref(null);
const offerForm = ref({ marketplaceId: '', price: 0, affiliateLink: '' });

async function load() {
  loading.value = true;
  try {
    const [productsRes, categoriesRes, suppliersRes, marketplacesRes] = await Promise.all([
      api.get('/admin/products'),
      api.get('/admin/categories'),
      api.get('/admin/suppliers'),
      api.get('/admin/marketplaces'),
    ]);
    items.value = productsRes.data;
    categories.value = categoriesRes.data;
    suppliers.value = suppliersRes.data;
    marketplaces.value = marketplacesRes.data;
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

async function save() {
  if (!form.value.categoryId) {
    alert('Categoria é obrigatória');
    return;
  }
  try {
    if (editingItem.value) {
      await api.put(`/admin/products/${editingItem.value.id}`, form.value);
    } else {
      await api.post('/admin/products', form.value);
    }
    await load();
    cancelEdit();
  } catch (e) {
    console.error(e);
    const msg = e?.response?.data?.error || 'Erro ao salvar produto';
    alert(msg);
  }
}

function edit(item) {
  editingItem.value = item;
  form.value = { 
    name: item.name, 
    slug: item.slug, 
    categoryId: item.category?.id || '',
    supplierId: item.supplier?.id || '',
    description: item.description || ''
  };
  showCreateForm.value = false;
}

function cancelEdit() {
  editingItem.value = null;
  showCreateForm.value = false;
  form.value = { name: '', slug: '', categoryId: '', supplierId: '', description: '' };
}

async function deleteItem(id) {
  if (!confirm('Deletar este produto e todas as suas ofertas?')) return;
  try {
    await api.delete(`/admin/products/${id}`);
    await load();
  } catch (e) {
    console.error(e);
    const msg = e?.response?.data?.error || 'Erro ao deletar produto';
    alert(msg);
  }
}

// Offers functions
async function viewOffers(product) {
  selectedProduct.value = product;
  showOffersModal.value = true;
  await loadOffers();
}

async function loadOffers() {
  if (!selectedProduct.value) return;
  loadingOffers.value = true;
  try {
    const res = await api.get(`/admin/products/${selectedProduct.value.id}/offers`);
    offers.value = res.data;
    selectedProduct.value.offersCount = res.data.length;
  } catch (e) {
    console.error(e);
    const msg = e?.response?.data?.error || 'Erro ao carregar ofertas';
    alert(msg);
  } finally {
    loadingOffers.value = false;
  }
}

async function saveOffer() {
  if (!offerForm.value.marketplaceId || !offerForm.value.price || !offerForm.value.affiliateLink) {
    alert('Todos os campos são obrigatórios');
    return;
  }
  try {
    if (editingOffer.value) {
      await api.put(`/admin/products/${selectedProduct.value.id}/offers/${editingOffer.value.id}`, offerForm.value);
    } else {
      await api.post(`/admin/products/${selectedProduct.value.id}/offers`, offerForm.value);
    }
    await loadOffers();
    cancelOfferEdit();
  } catch (e) {
    console.error(e);
    const errorMsg = e.response?.data?.error || 'Erro ao salvar oferta';
    alert(errorMsg);
  }
}

function editOffer(offer) {
  editingOffer.value = offer;
  offerForm.value = {
    marketplaceId: offer.marketplace?.id || '',
    price: offer.price || 0,
    affiliateLink: offer.affiliateLink || ''
  };
  showOfferForm.value = false;
}

function cancelOfferEdit() {
  editingOffer.value = null;
  showOfferForm.value = false;
  offerForm.value = { marketplaceId: '', price: 0, affiliateLink: '' };
}

async function deleteOffer(offerId) {
  if (!confirm('Deletar esta oferta?')) return;
  try {
    await api.delete(`/admin/products/${selectedProduct.value.id}/offers/${offerId}`);
    await loadOffers();
  } catch (e) {
    console.error(e);
    const msg = e?.response?.data?.error || 'Erro ao deletar oferta';
    alert(msg);
  }
}

function closeOffersModal() {
  showOffersModal.value = false;
  selectedProduct.value = null;
  offers.value = [];
  cancelOfferEdit();
}

function formatDate(dateString) {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR');
}

onMounted(load);
</script>
