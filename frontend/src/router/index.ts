// router/index.ts
import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import AdminLayout from '@/components/layout/AdminLayout.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || { left: 0, top: 0 };
  },
  routes: [
    // ---- Public routes ----
    { path: '/signin', name: 'Signin', component: () => import('../views/Auth/Signin.vue'), meta: { title: 'Signin', guestOnly: true } },
    { path: '/signup', name: 'Signup', component: () => import('../views/Auth/Signup.vue'), meta: { title: 'Signup', guestOnly: true } },
    { path: '/error-404', name: '404 Error', component: () => import('../views/Errors/FourZeroFour.vue'), meta: { title: '404 Error' } },

    // ---- Redirect /admin to dashboard ----
    { path: '/admin', redirect: '/admin/dashboard' },

    // ---- All authenticated routes under AdminLayout ----
    {
      path: '/',
      component: AdminLayout,
      meta: { requiresAuth: true },
      children: [
        // Shared (both user & admin)
        { path: 'profile', name: 'Profile', component: () => import('../views/Others/UserProfile.vue'), meta: { title: 'Profile' } },
        { path: 'my-shipments', name: 'MyShipments', component: () => import('@/views/dashboard/MyShipments.vue'), meta: { title: 'My Shipments' } },
        {
          path: 'admin/consolidations',
          component: () => import('@/views/admin/Consolidations.vue'),
          meta: { title: 'Consolidations' },
        },
        {
          path: 'admin/consolidations/create',
          component: () => import('@/views/admin/ConsolidationForm.vue'),
          meta: { title: 'New Consolidation' },
        },
        {
          path: 'admin/consolidations/:id',
          component: () => import('@/views/admin/ConsolidationView.vue'),
          meta: { title: 'Consolidation Details' },
        },
        {
          path: 'admin/consolidations/:id/edit',
          component: () => import('@/views/admin/ConsolidationForm.vue'),
          meta: { title: 'Edit Consolidation' },
        },

        { path: 'dashboard', name: 'UserDashboard', component: () => import('../views/dashboard/UserDashboard.vue'), meta: { title: 'Dashboard', role: 'user' } },

        // Admin only
        { path: 'admin/users', name: 'AdminUsers', component: () => import('@/views/admin/Users.vue'), meta: { title: 'Users', role: 'admin' } },

        { path: 'admin/dashboard', name: 'AdminDashboard', component: () => import('@/views/admin/AdminDashboard.vue'), meta: { title: 'Admin Dashboard', role: 'admin' } },
        { path: 'admin/shipments', name: 'AdminShipments', component: () => import('@/views/admin/Shipments.vue'), meta: { title: 'Shipments', role: 'admin' } },
        { path: 'admin/cities', name: 'AdminCities', component: () => import('@/views/admin/Cities.vue'), meta: { title: 'Cities', role: 'admin' } },
        { path: 'admin/categories', name: 'AdminCategories', component: () => import('@/views/admin/Categories.vue'), meta: { title: 'Categories', role: 'admin' } },
        { path: 'admin/sub-categories', name: 'AdminSubCategories', component: () => import('@/views/admin/SubCategories.vue'), meta: { title: 'Sub Categories', role: 'admin' } },
        { path: 'admin/sub-sub-categories', name: 'AdminSubSubCategories', component: () => import('@/views/admin/SubSubCategories.vue'), meta: { title: 'Sub Sub Categories', role: 'admin' } },
      ],
    },

    // ---- Catch-all ----
    { path: '/:pathMatch(.*)*', name: 'NotFound', component: () => import('../views/Errors/FourZeroFour.vue'), meta: { title: '404' } },
  ],
});

router.beforeEach(async (to, from, next) => {
  document.title = `Vue.js ${to.meta.title || ''} | US2PK Dashboard`;
  const auth = useAuthStore();

  // If token exists but user not loaded, fetch user
  if (auth.token && !auth.user) {
    await auth.fetchUser().catch(() => auth.logout());
  }

  // --- Root redirect based on role ---
  if (to.path === '/') {
    if (auth.isAuthenticated) {
      return next(auth.isAdmin ? '/admin/dashboard' : '/dashboard');
    } else {
      return next('/signin');
    }
  }

  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
  const role = to.matched.find(record => record.meta.role)?.meta.role;

  if (requiresAuth && !auth.isAuthenticated) {
    next('/signin');
  } else if (to.meta.guestOnly && auth.isAuthenticated) {
    next(auth.isAdmin ? '/admin/dashboard' : '/dashboard');
  } else if (role && auth.user?.role !== role) {
    next(auth.isAdmin ? '/admin/dashboard' : '/dashboard');
  } else {
    next();
  }
});

export default router;
