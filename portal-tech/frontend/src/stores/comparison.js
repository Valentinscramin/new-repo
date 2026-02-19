import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

const STORAGE_KEY = 'portal-tech-comparison'
const MAX_PRODUCTS = 3

export const useComparisonStore = defineStore('comparison', () => {
  // State
  const selectedProducts = ref([])
  const savedComparisons = ref([])

  // Getters
  const comparisonCount = computed(() => selectedProducts.value.length)
  const canAddMore = computed(() => selectedProducts.value.length < MAX_PRODUCTS)
  const isProductSelected = computed(() => (productId) => {
    return selectedProducts.value.some(p => p.id === productId)
  })
  const selectedCategory = computed(() => {
    if (selectedProducts.value.length === 0) return null
    return selectedProducts.value[0].category
  })
  const selectedCategoryName = computed(() => {
    return selectedCategory.value?.name || null
  })

  // Actions
  function addProduct(product) {
    if (!product || !product.id) {
      console.error('Invalid product')
      return false
    }

    if (selectedProducts.value.length >= MAX_PRODUCTS) {
      alert(`Máximo ${MAX_PRODUCTS} produtos permitidos para comparação`)
      return false
    }

    if (selectedProducts.value.some(p => p.id === product.id)) {
      console.warn('Product already in comparison')
      return false
    }

    // Validar categoria: todos produtos devem ser da mesma categoria
    if (selectedProducts.value.length > 0) {
      const firstCategory = selectedProducts.value[0].category?.name || selectedProducts.value[0].category?.id
      const newCategory = product.category?.name || product.category?.id
      
      if (firstCategory !== newCategory) {
        alert(`Só é possível comparar produtos da mesma categoria.\nCategoria selecionada: ${selectedProducts.value[0].category?.name || 'Indefinida'}`)
        return false
      }
    }

    selectedProducts.value.push(product)
    syncToStorage()
    return true
  }

  function removeProduct(productId) {
    const index = selectedProducts.value.findIndex(p => p.id === productId)
    if (index !== -1) {
      selectedProducts.value.splice(index, 1)
      syncToStorage()
      return true
    }
    return false
  }

  function clearAll() {
    selectedProducts.value = []
    syncToStorage()
  }

  function loadFromStorage() {
    try {
      const stored = localStorage.getItem(STORAGE_KEY)
      if (stored) {
        const data = JSON.parse(stored)
        if (Array.isArray(data)) {
          selectedProducts.value = data.slice(0, MAX_PRODUCTS)
        }
      }
    } catch (error) {
      console.error('Error loading comparison from storage:', error)
    }
  }

  function syncToStorage() {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(selectedProducts.value))
    } catch (error) {
      console.error('Error saving comparison to storage:', error)
    }
  }

  async function saveComparison(apiService) {
    if (selectedProducts.value.length < 2) {
      throw new Error('At least 2 products required to save comparison')
    }

    const productIds = selectedProducts.value.map(p => p.id)
    try {
      const result = await apiService.saveComparison(productIds)
      return result
    } catch (error) {
      console.error('Error saving comparison:', error)
      throw error
    }
  }

  async function fetchSavedComparisons(apiService) {
    try {
      const result = await apiService.getComparisons()
      savedComparisons.value = result.data || result
      return savedComparisons.value
    } catch (error) {
      console.error('Error fetching saved comparisons:', error)
      throw error
    }
  }

  async function deleteComparison(apiService, comparisonId) {
    try {
      await apiService.deleteComparison(comparisonId)
      savedComparisons.value = savedComparisons.value.filter(c => c.id !== comparisonId)
      return true
    } catch (error) {
      console.error('Error deleting comparison:', error)
      throw error
    }
  }

  function loadComparison(products) {
    selectedProducts.value = products.slice(0, MAX_PRODUCTS)
    syncToStorage()
  }

  // Initialize from localStorage
  loadFromStorage()

  return {
    // State
    selectedProducts,
    savedComparisons,
    // Getters
    comparisonCount,
    canAddMore,
    isProductSelected,
    selectedCategory,
    selectedCategoryName,
    // Actions
    addProduct,
    removeProduct,
    clearAll,
    loadFromStorage,
    syncToStorage,
    saveComparison,
    fetchSavedComparisons,
    deleteComparison,
    loadComparison,
  }
})
