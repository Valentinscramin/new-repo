import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../views/HomeView.vue'),
      meta: { layout: 'main' }
    },
    {
      path: '/products',
      name: 'products',
      component: () => import('../views/ProductsView.vue'),
      meta: { layout: 'main' }
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('../views/Dashboard.vue'),
      meta: { layout: 'main' }
    },
    {
      path: '/dashboard/favorites',
      name: 'dashboard-favorites',
      component: () => import('../views/Favorites.vue'),
      meta: { layout: 'main' }
    },
    {
      path: '/dashboard/reviews',
      name: 'dashboard-reviews',
      component: () => import('../views/Reviews.vue'),
      meta: { layout: 'main' }
    },
    {
      path: '/compare',
      name: 'compare',
      component: () => import('../views/CompareView.vue'),
      meta: { layout: 'main' }
    },
    {
      path: '/dashboard/comparisons',
      name: 'dashboard-comparisons',
      component: () => import('../views/SavedComparisonsView.vue'),
      meta: { layout: 'main', requiresAuth: true }
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/Login.vue'),
      meta: { layout: 'none' }
    },
    {
      path: '/admin',
      name: 'admin-dashboard',
      component: () => import('../views/admin/AdminDashboard.vue'),
      meta: { layout: 'admin', subtitle: 'Visão geral do sistema' }
    },
    {
      path: '/admin/categories',
      name: 'admin-categories',
      component: () => import('../views/admin/Categories.vue'),
      meta: { layout: 'admin', subtitle: 'Gerenciar categorias de produtos' }
    },
    {
      path: '/admin/marketplaces',
      name: 'admin-marketplaces',
      component: () => import('../views/admin/Marketplaces.vue'),
      meta: { layout: 'admin', subtitle: 'Gerenciar marketplaces' }
    },
    {
      path: '/admin/suppliers',
      name: 'admin-suppliers',
      component: () => import('../views/admin/Suppliers.vue'),
      meta: { layout: 'admin', subtitle: 'Gerenciar fornecedores' }
    },
    {
      path: '/admin/products',
      name: 'admin-products',
      component: () => import('../views/admin/Products.vue'),
      meta: { layout: 'admin', subtitle: 'Gerenciar produtos e ofertas' }
    },
  ],
});

export default router;
