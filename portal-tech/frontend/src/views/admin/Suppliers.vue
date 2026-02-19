<template>
  <div class="min-h-screen flex items-start justify-center">
    <div class="max-w-6xl w-full py-8 px-4">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold">Suppliers</h2>
      <div class="flex gap-3">
        <router-link to="/admin" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded transition">← Back</router-link>
        <button @click="showCreateForm = true" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded transition">+ New Supplier</button>
      </div>
    </div>

    <div v-if="showCreateForm || editingItem" class="mb-6 p-4 bg-gray-900 rounded-lg border border-gray-800">
      <h3 class="text-lg font-semibold mb-4">{{ editingItem ? 'Edit' : 'Create' }} Supplier</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <input v-model="form.name" placeholder="Name" class="px-4 py-2 bg-gray-800 rounded border border-gray-700" />
        <input v-model="form.website" placeholder="Website (optional)" class="px-4 py-2 bg-gray-800 rounded border border-gray-700" />
      </div>
      <div class="flex gap-3">
        <button @click="save" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded transition">Save</button>
        <button @click="cancelEdit" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded transition">Cancel</button>
      </div>
    </div>

    <LoadingSpinner v-if="loading" message="Carregando fornecedores..." />
    <div v-else-if="items.length === 0" class="text-gray-400">No suppliers yet.</div>
    <div v-else class="bg-gray-900 rounded-lg border border-gray-800 overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-800">
          <tr>
            <th class="text-left p-4">Name</th>
            <th class="text-left p-4">Website</th>
            <th class="text-left p-4">Created</th>
            <th class="text-right p-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items" :key="item.id" class="border-t border-gray-800 hover:bg-gray-800/50">
            <td class="p-4">{{ item.name }}</td>
            <td class="p-4 text-gray-400">{{ item.website || 'N/A' }}</td>
            <td class="p-4 text-gray-400 text-sm">{{ formatDate(item.createdAt) }}</td>
            <td class="p-4 text-right">
              <button @click="edit(item)" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 rounded text-sm mr-2">Edit</button>
              <button @click="deleteItem(item.id)" class="px-3 py-1 bg-red-600 hover:bg-red-700 rounded text-sm">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import LoadingSpinner from '../../components/LoadingSpinner.vue';

const items = ref([]);
const loading = ref(false);
const showCreateForm = ref(false);
const editingItem = ref(null);
const form = ref({ name: '', website: '' });

async function load() {
  loading.value = true;
  try {
    const res = await api.get('/admin/suppliers');
    items.value = res.data;
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

async function save() {
  try {
    if (editingItem.value) {
      await api.put(`/admin/suppliers/${editingItem.value.id}`, form.value);
    } else {
      await api.post('/admin/suppliers', form.value);
    }
    await load();
    cancelEdit();
  } catch (e) {
    console.error(e);
    const msg = e?.response?.data?.error || 'Error saving supplier';
    alert(msg);
  }
}

function edit(item) {
  editingItem.value = item;
  form.value = { name: item.name, website: item.website || '' };
  showCreateForm.value = false;
}

function cancelEdit() {
  editingItem.value = null;
  showCreateForm.value = false;
  form.value = { name: '', website: '' };
}

async function deleteItem(id) {
  if (!confirm('Delete this supplier?')) return;
  try {
    await api.delete(`/admin/suppliers/${id}`);
    await load();
  } catch (e) {
    console.error(e);
    const msg = e?.response?.data?.error || 'Error deleting supplier';
    alert(msg);
  }
}

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString();
}

onMounted(load);
</script>
