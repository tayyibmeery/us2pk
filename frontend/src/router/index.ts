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
    {
      path: '/',
      name: 'Home',
      component: () => import('@/views/HomePage.vue'),
      meta: { title: 'Home' },
    },
    { path: '/', name: 'Landing', component: () => import('@/views/Landing.vue'), meta: { title: 'Home' } },
    { path: '/signin', name: 'Signin', component: () => import('../views/Auth/Signin.vue'), meta: { title: 'Signin', guestOnly: true } },
    { path: '/signup', name: 'Signup', component: () => import('../views/Auth/Signup.vue'), meta: { title: 'Signup', guestOnly: true } },
    { path: '/error-404', name: '404 Error', component: () => import('../views/Errors/FourZeroFour.vue'), meta: { title: '404 Error' } },

    // ---- Authenticated routes (shared between user and admin) ----
    {
      path: '/',
      component: AdminLayout,
      meta: { requiresAuth: true },
      children: [
        { path: 'profile', name: 'Profile', component: () => import('../views/Others/UserProfile.vue'), meta: { title: 'Profile' } },
        { path: 'my-shipments', name: 'MyShipments', component: () => import('@/views/dashboard/MyShipments.vue'), meta: { title: 'My Shipments' } },
        { path: 'dashboard', name: 'UserDashboard', component: () => import('../views/dashboard/UserDashboard.vue'), meta: { title: 'Dashboard', role: 'user' } },
        // Admin routes - all under /admin
        {
          path: 'admin',
          component: AdminLayout, // we can nest layouts, but we already have AdminLayout as parent; we can just put admin routes directly here
          // Actually we need to group admin routes under /admin path.
          // Since the parent is '/', we can define a child with path 'admin' and its own children.
          // But that would be nested layout, not needed. Better to have separate top-level routes for admin.
        }
      ],
    },

    // ---- Admin-only routes (under /admin) ----
    {
      path: '/admin',
      component: AdminLayout,
      meta: { requiresAuth: true, role: 'admin' },
      children: [
        { path: 'dashboard', name: 'AdminDashboard', component: () => import('@/views/admin/AdminDashboard.vue'), meta: { title: 'Admin Dashboard' } },
        { path: 'users', name: 'AdminUsers', component: () => import('@/views/admin/Users.vue'), meta: { title: 'Users' } },
        { path: 'shipments', name: 'AdminShipments', component: () => import('@/views/admin/Shipments.vue'), meta: { title: 'Shipments' } },
        { path: 'shipments/:id', name: 'AdminShipmentView', component: () => import('@/views/admin/ShipmentView.vue'), meta: { title: 'Shipment Details' } },
        { path: 'cities', name: 'AdminCities', component: () => import('@/views/admin/Cities.vue'), meta: { title: 'Cities' } },
        { path: 'accounts', name: 'AdminAccounts', component: () => import('@/views/admin/Accounts.vue'), meta: { title: 'Chart of Accounts' } },
        { path: 'vouchers', name: 'AdminVouchers', component: () => import('@/views/admin/Vouchers.vue'), meta: { title: 'Vouchers' } },
        { path: 'vouchers/:id', name: 'AdminVoucherView', component: () => import('@/views/admin/VoucherView.vue'), meta: { title: 'Voucher Details' } },
        {
          path: '/admin/vouchers/approval',
          name: 'AdminVoucherApproval',
          component: () => import('@/views/admin/VoucherApproval.vue'),
          meta: { title: 'Approval Screen' }
        },
        { path: 'journal', name: 'AdminJournal', component: () => import('@/views/admin/Journal.vue'), meta: { title: 'General Journal' } },
        { path: 'trial-balance', name: 'AdminTrialBalance', component: () => import('@/views/admin/TrialBalance.vue'), meta: { title: 'Trial Balance' } },
        { path: 'pandl/since-inception', name: 'AdminPandLSinceInception', component: () => import('@/views/admin/PandL/SinceInception.vue'), meta: { title: 'P&L Since Inception' } },
        { path: 'pandl/yearly', name: 'AdminPandLYearly', component: () => import('@/views/admin/PandL/Yearly.vue'), meta: { title: 'P&L Yearly' } },
        { path: 'pandl/quarterly', name: 'AdminPandLQuarterly', component: () => import('@/views/admin/PandL/Quarterly.vue'), meta: { title: 'P&L Quarterly' } },
        { path: 'pandl/monthly', name: 'AdminPandLMonthly', component: () => import('@/views/admin/PandL/Monthly.vue'), meta: { title: 'P&L Monthly' } },
        { path: 'pandl/balance-sheet', name: 'AdminBalanceSheet', component: () => import('@/views/admin/PandL/BalanceSheet.vue'), meta: { title: 'Balance Sheet' } },

        {
          path: 'ledger',
          name: 'AdminLedger',
          component: () => import('@/views/admin/Ledger.vue'),
          meta: { title: 'Ledger' },
        },
        { path: 'payment-methods', name: 'AdminPaymentMethods', component: () => import('@/views/admin/PaymentMethods.vue'), meta: { title: 'Payment Methods' } },
        { path: 'sites', name: 'AdminSites', component: () => import('@/views/admin/Sites.vue'), meta: { title: 'Sites' } },
        { path: 'international-couriers', name: 'AdminInternationalCouriers', component: () => import('@/views/admin/InternationalCouriers.vue'), meta: { title: 'International Couriers' } },
        { path: 'local-couriers', name: 'AdminLocalCouriers', component: () => import('@/views/admin/LocalCouriers.vue'), meta: { title: 'Local Couriers' } },
        { path: 'shipment-statuses', name: 'AdminShipmentStatuses', component: () => import('@/views/admin/ShipmentStatuses.vue'), meta: { title: 'Shipment Statuses' } },
        { path: 'warehouses', name: 'AdminWarehouses', component: () => import('@/views/admin/Warehouses.vue'), meta: { title: 'Warehouses' } },
        { path: 'weight-discounts', name: 'AdminWeightDiscounts', component: () => import('@/views/admin/WeightDiscounts.vue'), meta: { title: 'Weight Discounts' } },

        { path: 'pages', name: 'AdminPages', component: () => import('@/views/admin/Pages.vue'), meta: { title: 'Pages' } },
        { path: 'invoices', name: 'AdminInvoices', component: () => import('@/views/admin/Invoices.vue'), meta: { title: 'Invoices' } },
        { path: 'revenues', name: 'AdminRevenues', component: () => import('@/views/admin/Revenues.vue'), meta: { title: 'Revenues' } },
        { path: 'debtors', name: 'AdminDebtors', component: () => import('@/views/admin/Debtors.vue'), meta: { title: 'Debtors' } },
        // Consolidations
        { path: 'consolidations', name: 'AdminConsolidations', component: () => import('@/views/admin/Consolidations.vue'), meta: { title: 'Consolidations' } },
        { path: 'consolidations/create', name: 'AdminConsolidationCreate', component: () => import('@/views/admin/ConsolidationForm.vue'), meta: { title: 'New Consolidation' } },
        { path: 'consolidations/:id', name: 'AdminConsolidationView', component: () => import('@/views/admin/ConsolidationView.vue'), meta: { title: 'Consolidation Details' } },
        { path: 'consolidations/:id/edit', name: 'AdminConsolidationEdit', component: () => import('@/views/admin/ConsolidationForm.vue'), meta: { title: 'Edit Consolidation' } },
      ],
    },

    // ---- Catch-all ----
    { path: '/:pathMatch(.*)*', name: 'NotFound', component: () => import('../views/Errors/FourZeroFour.vue'), meta: { title: '404' } },
  ],
});

router.beforeEach(async (to, from, next) => {
  document.title = `Vue.js ${to.meta.title || ''} | US2PK Dashboard`;
  const auth = useAuthStore();

  if (auth.token && !auth.user) {
    await auth.fetchUser().catch(() => auth.logout());
  }

  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
  const role = to.matched.find(record => record.meta.role)?.meta.role;

  if (requiresAuth && !auth.isAuthenticated) {
    next('/signin');
  } else if (to.meta.guestOnly && auth.isAuthenticated) {
    // If user is authenticated and tries to go to signin/signup, redirect to appropriate dashboard
    if (auth.isAdmin) {
      next('/admin/dashboard');
    } else {
      next('/dashboard');
    }
  } else if (role && auth.user?.role !== role) {
    // Role mismatch: admin trying to access user-only route or vice versa
    // Redirect to appropriate dashboard
    if (auth.isAdmin) {
      next('/admin/dashboard');
    } else {
      next('/dashboard');
    }
  } else {
    next();
  }
});

export default router;
