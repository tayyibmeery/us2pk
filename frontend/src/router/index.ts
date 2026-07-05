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

        // ---- Admin only routes ----
        { path: 'admin/users', name: 'AdminUsers', component: () => import('@/views/admin/Users.vue'), meta: { title: 'Users', role: 'admin' } },
        { path: 'admin/dashboard', name: 'AdminDashboard', component: () => import('@/views/admin/AdminDashboard.vue'), meta: { title: 'Admin Dashboard', role: 'admin' } },
        { path: 'admin/shipments', name: 'AdminShipments', component: () => import('@/views/admin/Shipments.vue'), meta: { title: 'Shipments', role: 'admin' } },
        { path: 'admin/cities', name: 'AdminCities', component: () => import('@/views/admin/Cities.vue'), meta: { title: 'Cities', role: 'admin' } },




        // ---- Financial Accounting (Admin only) ----
        { path: 'admin/accounts', name: 'AdminAccounts', component: () => import('@/views/admin/Accounts.vue'), meta: { title: 'Chart of Accounts', role: 'admin' } },
        { path: 'admin/vouchers', name: 'AdminVouchers', component: () => import('@/views/admin/Vouchers.vue'), meta: { title: 'Vouchers', role: 'admin' } },
        { path: 'admin/vouchers/:id', name: 'AdminVoucherView', component: () => import('@/views/admin/VoucherView.vue'), meta: { title: 'Voucher Details', role: 'admin' } },
        { path: 'admin/journal', name: 'AdminJournal', component: () => import('@/views/admin/Journal.vue'), meta: { title: 'General Journal', role: 'admin' } },
        { path: 'admin/trial-balance', name: 'AdminTrialBalance', component: () => import('@/views/admin/TrialBalance.vue'), meta: { title: 'Trial Balance', role: 'admin' } },
        { path: 'admin/pandl/since-inception', name: 'AdminPandLSinceInception', component: () => import('@/views/admin/PandL/SinceInception.vue'), meta: { title: 'P&L Since Inception', role: 'admin' } },
        { path: 'admin/pandl/yearly', name: 'AdminPandLYearly', component: () => import('@/views/admin/PandL/Yearly.vue'), meta: { title: 'P&L Yearly', role: 'admin' } },
        { path: 'admin/pandl/quarterly', name: 'AdminPandLQuarterly', component: () => import('@/views/admin/PandL/Quarterly.vue'), meta: { title: 'P&L Quarterly', role: 'admin' } },
        { path: 'admin/pandl/monthly', name: 'AdminPandLMonthly', component: () => import('@/views/admin/PandL/Monthly.vue'), meta: { title: 'P&L Monthly', role: 'admin' } },


        // Accounting
        {
          path: 'admin/accounting',
          component: () => import('@/views/admin/AccountingDashboard.vue'),
          meta: { title: 'Accounting Dashboard', role: 'admin' },
        },
        {
          path: 'admin/expenses',
          component: () => import('@/views/admin/Expenses.vue'),
          meta: { title: 'Expenses', role: 'admin' },
        },
        {
          path: 'admin/expense-categories',
          component: () => import('@/views/admin/ExpenseCategories.vue'),
          meta: { title: 'Expense Categories', role: 'admin' },
        },
        {
          path: 'admin/employees',
          component: () => import('@/views/admin/Employees.vue'),
          meta: { title: 'Employees', role: 'admin' },
        },
        {
          path: 'admin/salary-payments',
          component: () => import('@/views/admin/SalaryPayments.vue'),
          meta: { title: 'Salary Payments', role: 'admin' },
        },
        // ---- 🆕 New admin routes ----
        {
          path: 'admin/shipments/:id',
          component: () => import('@/views/admin/ShipmentView.vue'),
          meta: { title: 'Shipment Details', role: 'admin' },
        },
        { path: 'admin/payment-methods', name: 'AdminPaymentMethods', component: () => import('@/views/admin/PaymentMethods.vue'), meta: { title: 'Payment Methods', role: 'admin' } },
        { path: 'admin/sites', name: 'AdminSites', component: () => import('@/views/admin/Sites.vue'), meta: { title: 'Sites', role: 'admin' } },
        { path: 'admin/international-couriers', name: 'AdminInternationalCouriers', component: () => import('@/views/admin/InternationalCouriers.vue'), meta: { title: 'International Couriers', role: 'admin' } },
        { path: 'admin/local-couriers', name: 'AdminLocalCouriers', component: () => import('@/views/admin/LocalCouriers.vue'), meta: { title: 'Local Couriers', role: 'admin' } },
        { path: 'admin/shipment-statuses', name: 'AdminShipmentStatuses', component: () => import('@/views/admin/ShipmentStatuses.vue'), meta: { title: 'Shipment Statuses', role: 'admin' } },

        { path: 'admin/warehouses', name: 'AdminWarehouses', component: () => import('@/views/admin/Warehouses.vue'), meta: { title: 'Warehouses', role: 'admin' } },
        { path: 'admin/weight-discounts', name: 'AdminWeightDiscounts', component: () => import('@/views/admin/WeightDiscounts.vue'), meta: { title: 'Weight Discounts', role: 'admin' } },
        { path: 'admin/settings', name: 'AdminSettings', component: () => import('@/views/admin/Settings.vue'), meta: { title: 'Settings', role: 'admin' } },

        { path: 'admin/pages', name: 'AdminPages', component: () => import('@/views/admin/Pages.vue'), meta: { title: 'Pages', role: 'admin' } },
        { path: 'admin/invoices', name: 'AdminInvoices', component: () => import('@/views/admin/Invoices.vue'), meta: { title: 'Invoices', role: 'admin' } },
        { path: 'admin/revenues', name: 'AdminRevenues', component: () => import('@/views/admin/Revenues.vue'), meta: { title: 'Revenues', role: 'admin' } },
        { path: 'admin/debtors', name: 'AdminDebtors', component: () => import('@/views/admin/Debtors.vue'), meta: { title: 'Debtors', role: 'admin' } },
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
