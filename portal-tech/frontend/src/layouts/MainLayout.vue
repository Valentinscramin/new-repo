<template>
  <div class="min-h-screen bg-netflix-black text-white">
    <!-- Netflix-style Navbar -->
    <NetflixNavbar />

    <!-- Optional hero slot: pages can provide their own hero/banner -->
    <div>
      <slot name="hero" />
    </div>

    <!-- Main content with padding top to compensate for fixed navbar -->
    <main class="pt-16">
      <slot />
    </main>

    <!-- Netflix-style Footer -->
    <footer class="border-t border-gray-800 mt-16 py-12 bg-netflix-black-light">
      <div class=" mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
          <div>
            <h3 class="text-white font-semibold mb-4">Sobre</h3>
            <ul class="space-y-2 text-sm text-gray-400">
              <li><a href="#" class="hover:text-white transition">Quem Somos</a></li>
              <li><a href="#" class="hover:text-white transition">Como Funciona</a></li>
              <li><a href="#" class="hover:text-white transition">Parcerias</a></li>
            </ul>
          </div>
          <div>
            <h3 class="text-white font-semibold mb-4">Produtos</h3>
            <ul class="space-y-2 text-sm text-gray-400">
              <li><a href="#" class="hover:text-white transition">Comparações</a></li>
              <li><a href="#" class="hover:text-white transition">Rankings</a></li>
              <li><a href="#" class="hover:text-white transition">Ofertas</a></li>
            </ul>
          </div>
          <div>
            <h3 class="text-white font-semibold mb-4">Suporte</h3>
            <ul class="space-y-2 text-sm text-gray-400">
              <li><a href="#" class="hover:text-white transition">FAQ</a></li>
              <li><a href="#" class="hover:text-white transition">Contato</a></li>
              <li><a href="#" class="hover:text-white transition">Ajuda</a></li>
            </ul>
          </div>
          <div>
            <h3 class="text-white font-semibold mb-4">Legal</h3>
            <ul class="space-y-2 text-sm text-gray-400">
              <li><a href="#" class="hover:text-white transition">Termos de Uso</a></li>
              <li><a href="#" class="hover:text-white transition">Privacidade</a></li>
              <li><a href="#" class="hover:text-white transition">Cookies</a></li>
            </ul>
          </div>
        </div>
        <div class="text-center text-sm text-gray-500 pt-8 border-t border-gray-800">
          © 2026 Portal Tech — Comparação de Produtos
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import NetflixNavbar from '../components/NetflixNavbar.vue'
</script>

<style scoped>
/* Minimal styles - rely on Tailwind */
</style>
