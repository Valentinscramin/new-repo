import axios from 'axios';

const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  headers: {
    'Content-Type': 'application/json',
  },
});

// Interceptor para adicionar token JWT automaticamente
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Interceptor para tratamento de erros
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default api;

// Função helper para buscar dados da homepage a partir do backend
export async function getHomeData() {
  try {
    const res = await api.get('/home')
    return res.data
  } catch (err) {
    // Propaga o erro para que o chamador possa fazer fallback
    throw err
  }
}

// Comparison API methods
export async function getComparisons() {
  try {
    const res = await api.get('/comparisons')
    return res.data
  } catch (err) {
    throw err
  }
}

export async function saveComparison(productIds) {
  try {
    const res = await api.post('/comparisons', { products: productIds })
    return res.data
  } catch (err) {
    throw err
  }
}

export async function deleteComparison(comparisonId) {
  try {
    const res = await api.delete(`/comparisons/${comparisonId}`)
    return res.data
  } catch (err) {
    throw err
  }
}

export async function getProductsByIds(ids) {
  try {
    const res = await api.get(`/products?ids=${ids.join(',')}`)
    return res.data
  } catch (err) {
    throw err
  }
}

export async function getAllProducts() {
  try {
    const res = await api.get('/products')
    return res.data
  } catch (err) {
    throw err
  }
}
