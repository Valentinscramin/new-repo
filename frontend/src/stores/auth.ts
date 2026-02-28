import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiClient from '@/api/client'

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('token'))
  const user = ref<any>(null)

  const isAuthenticated = computed(() => !!token.value)

  async function login(email: string, password: string) {
    try {
      const response = await apiClient.post('/login', { email, password })
      token.value = response.data.token
      localStorage.setItem('token', response.data.token)
      await fetchUser()
      return { success: true }
    } catch (error: any) {
      return { 
        success: false, 
        error: error.response?.data?.message || 'Login failed' 
      }
    }
  }

  async function register(name: string, email: string, password: string) {
    try {
      const response = await apiClient.post('/register', { name, email, password })
      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem('token', response.data.token)
      localStorage.setItem('user', JSON.stringify(response.data.user))
      return { success: true }
    } catch (error: any) {
      return { 
        success: false, 
        error: error.response?.data?.error || 'Registration failed' 
      }
    }
  }

  async function fetchUser() {
    try {
      const response = await apiClient.get('/me')
      user.value = response.data
      localStorage.setItem('user', JSON.stringify(response.data))
    } catch (error) {
      logout()
    }
  }

  function logout() {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  // Initialize user from localStorage
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    try {
      user.value = JSON.parse(storedUser)
    } catch (e) {
      localStorage.removeItem('user')
    }
  }

  return {
    token,
    user,
    isAuthenticated,
    login,
    register,
    logout,
    fetchUser
  }
})
