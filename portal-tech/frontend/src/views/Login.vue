<template>
  <div class="max-w-md mx-auto mt-20 bg-gray-900/60 p-8 rounded border border-gray-800">
    <h2 class="text-2xl font-semibold mb-4 text-center">Entrar</h2>

    <div class="mb-4">
      <label class="inline-flex items-center mr-4">
        <input type="radio" v-model="role" value="user" class="form-radio" />
        <span class="ml-2">Usuário</span>
      </label>
      <label class="inline-flex items-center">
        <input type="radio" v-model="role" value="admin" class="form-radio" />
        <span class="ml-2">Admin</span>
      </label>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div v-if="role === 'user'">
        <label class="block text-sm text-gray-300">Email</label>
        <input v-model="email" type="email" required class="w-full mt-1 px-3 py-2 rounded bg-gray-800 border border-gray-700" />
      </div>

      <div v-else>
        <label class="block text-sm text-gray-300">Email do Admin</label>
        <input v-model="username" type="email" placeholder="admin@exemplo.com" required class="w-full mt-1 px-3 py-2 rounded bg-gray-800 border border-gray-700" />
      </div>

      <div>
        <label class="block text-sm text-gray-300">Senha</label>
        <input v-model="password" type="password" required class="w-full mt-1 px-3 py-2 rounded bg-gray-800 border border-gray-700" />
      </div>

      <div v-if="error" class="text-sm text-red-400">{{ error }}</div>

      <div class="flex items-center justify-between">
        <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 rounded font-medium">Entrar</button>
        <button type="button" @click="clear" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 rounded">Limpar</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { login } from '../services/auth.js';

const router = useRouter();
const role = ref('user');
const email = ref('');
const username = ref('');
const password = ref('');
const error = ref('');

function clear() {
  email.value = '';
  username.value = '';
  password.value = '';
  error.value = '';
}

async function handleSubmit() {
  error.value = '';
  const creds = {};
  if (role.value === 'user') {
    creds.email = email.value;
  } else {
    // backend /api/login expects an `email` field — send the admin login value as `email`
    // include `username` as well in case backend uses it later
    creds.email = username.value;
    creds.username = username.value;
    creds.admin = true;
  }
  creds.password = password.value;

  try {
    const res = await login(creds);
    // redirect according to role or returned user
    if (role.value === 'admin') {
      router.push('/admin');
    } else {
      router.push('/dashboard');
    }
  } catch (err) {
    error.value = (err?.response?.data?.message) || 'Erro ao autenticar. Verifique credenciais.';
  }
}
</script>

<style scoped>
/* minimal styling — layout handled by Tailwind classes */
</style>
