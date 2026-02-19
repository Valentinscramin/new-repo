import api from './api';

/**
 * Register a new user
 * @param {Object} userData - { email, password, name? }
 * @returns {Promise<Object>} - { id, email }
 */
export async function register(userData) {
  const res = await api.post('/register', userData);
  return res.data;
}

/**
 * Login user
 * @param {Object} credentials - { email, password }
 * @returns {Promise<Object>} - { token, user: { id, email, name } }
 */
export async function login(credentials) {
  const res = await api.post('/login', credentials);
  if (res.data.token) {
    localStorage.setItem('token', res.data.token);
  }
  return res.data;
}

/**
 * Logout user
 */
export function logout() {
  localStorage.removeItem('token');
}

/**
 * Check if user is authenticated
 * @returns {boolean}
 */
export function isAuthenticated() {
  return !!localStorage.getItem('token');
}

/**
 * Get the current token
 * @returns {string|null}
 */
export function getToken() {
  return localStorage.getItem('token');
}
