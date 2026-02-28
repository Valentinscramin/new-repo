import { defineStore } from 'pinia'
import { ref } from 'vue'
import apiClient from '@/api/client'

export interface Product {
  id: number
  name: string
  brand: string | null
  price: string | null
  currency: string | null
  category: string | null
  specs: Record<string, any>
  strengths: string[]
  weaknesses: string[]
  score: string | null
  url: string
}

export interface ComparisonDetail {
  id: number
  status: 'processing' | 'completed' | 'failed'
  createdAt: string
  products: Product[]
  winnerId: number | null
}

export interface ComparisonListItem {
  id: number
  status: 'processing' | 'completed' | 'failed'
  createdAt: string
  productCount: number
  winnerId: number | null
}

export const useComparisonStore = defineStore('comparison', () => {
  const currentComparison = ref<ComparisonDetail | null>(null)
  const comparisons = ref<ComparisonListItem[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function createComparison(urls: string[]) {
    loading.value = true
    error.value = null
    try {
      const response = await apiClient.post('/comparisons', { urls })
      currentComparison.value = { 
        id: response.data.comparisonId,
        status: response.data.status,
        createdAt: new Date().toISOString(),
        products: [],
        winnerId: null
      }
      return { success: true, comparisonId: response.data.comparisonId }
    } catch (err: any) {
      error.value = err.response?.data?.error || 'Failed to create comparison'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  async function fetchComparison(id: number) {
    loading.value = true
    error.value = null
    try {
      const response = await apiClient.get(`/comparisons/${id}`)
      currentComparison.value = response.data
      return { success: true, data: response.data }
    } catch (err: any) {
      error.value = err.response?.data?.error || 'Failed to fetch comparison'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  async function fetchUserComparisons() {
    loading.value = true
    error.value = null
    try {
      const response = await apiClient.get('/comparisons')
      comparisons.value = response.data
      return { success: true }
    } catch (err: any) {
      error.value = err.response?.data?.error || 'Failed to fetch comparisons'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  return {
    currentComparison,
    comparisons,
    loading,
    error,
    createComparison,
    fetchComparison,
    fetchUserComparisons
  }
})
