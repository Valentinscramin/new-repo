import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useUIStore = defineStore('ui', () => {
  // State
  const modalProduct = ref(null)
  const navbarOpaque = ref(false)
  const isSidebarOpen = ref(true)

  // Getters
  const isModalOpen = computed(() => modalProduct.value !== null)

  // Actions
  function openProductModal(product) {
    modalProduct.value = product
  }

  function closeProductModal() {
    modalProduct.value = null
  }

  function setNavbarOpaque(value) {
    navbarOpaque.value = value
  }

  function toggleSidebar() {
    isSidebarOpen.value = !isSidebarOpen.value
  }

  function setSidebarOpen(value) {
    isSidebarOpen.value = value
  }

  return {
    // State
    modalProduct,
    navbarOpaque,
    isSidebarOpen,
    // Getters
    isModalOpen,
    // Actions
    openProductModal,
    closeProductModal,
    setNavbarOpaque,
    toggleSidebar,
    setSidebarOpen,
  }
})
