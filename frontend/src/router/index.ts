import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import AdminLayout from '@/components/layout/AdminLayout.vue';
import UserLayout from '@/views/user/layouts/UserLayout.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || { left: 0, top: 0 };
  },
  routes: [
    {
      path: '/',
      name: 'Landing',
      component: () => import('@/views/Landing.vue'),
      meta: { title: 'US2PK - Shipping & Logistics', public: true },
    },
    {
      path: '/landing',
      redirect: '/',
    },
    {
      path: '/signin',
      name: 'Signin',
      component: () => import('@/views/Auth/Signin.vue'),
      meta: { title: 'Signin', guestOnly: true },
    },
    {
      path: '/signup',
      name: 'Signup',
      component: () => import('@/views/Auth/Signup.vue'),
      meta: { title: 'Signup', guestOnly: true },
    },
    {
      path: '/error-404',
      name: '404 Error',
      component: () => import('@/views/Errors/FourZeroFour.vue'),
      meta: { title: '404 Error' },
    },

    // ============================================================
    // REDIRECTS
    // ============================================================
    {
      path: '/profile',
      redirect: () => {
        const auth = useAuthStore();
        return auth.isAdmin ? '/admin/profile' : '/user/profile';
      }
    },
    {
      path: '/settings',
      redirect: () => {
        const auth = useAuthStore();
        return auth.isAdmin ? '/admin/settings' : '/user/settings';
      }
    },

    // ============================================================
    // USER ROUTES
    // ============================================================
    {
      path: '/user',
      component: UserLayout,
      meta: { requiresAuth: true, role: 'user' },
      children: [
        {
          path: 'dashboard',
          name: 'UserDashboard',
          component: () => import('@/views/user/dashboard/UserDashboard.vue'),
          meta: { title: 'Dashboard' },
        },
        {
          path: 'my-shipments',
          name: 'MyShipments',
          component: () => import('@/views/user/shipments/MyShipments.vue'),
          meta: { title: 'My Shipments' },
        },
        {
          path: 'my-shipments/:id',
          name: 'ShipmentDetail',
          component: () => import('@/views/user/shipments/ShipmentDetail.vue'),
          meta: { title: 'Shipment Details' },
        },
        {
          path: 'track-shipment',
          name: 'TrackShipment',
          component: () => import('@/views/user/shipments/ShipmentTracker.vue'),
          meta: { title: 'Track Shipment' },
        },
        {
          path: 'profile',
          name: 'UserProfile',
          component: () => import('@/views/user/profile/UserProfile.vue'),
          meta: { title: 'Profile' },
        },
        {
          path: 'settings',
          name: 'UserSettings',
          component: () => import('@/views/user/profile/ProfileSettings.vue'),
          meta: { title: 'Settings' },
        },
      ],
    },

    // ============================================================
    // ADMIN ROUTES
    // ============================================================
    {
      path: '/admin',
      component: AdminLayout,
      meta: { requiresAuth: true, role: 'admin' },
      children: [
        // Dashboard
        {
          path: 'dashboard',
          name: 'AdminDashboard',
          component: () => import('@/views/admin/AdminDashboard.vue'),
          meta: { title: 'Admin Dashboard' },
        },
        // Profile & Settings
        {
          path: 'profile',
          name: 'AdminProfile',
          component: () => import('@/views/admin/AdminProfile.vue'),
          meta: { title: 'Profile' },
        },
        {
          path: 'settings',
          name: 'AdminSettings',
          component: () => import('@/views/admin/AdminSettings.vue'),
          meta: { title: 'Settings' },
        },
        // Users
        {
          path: 'users',
          name: 'AdminUsers',
          component: () => import('@/views/admin/Users.vue'),
          meta: { title: 'Users' },
        },
        // Shipments
        {
          path: 'shipments',
          name: 'AdminShipments',
          component: () => import('@/views/admin/Shipments.vue'),
          meta: { title: 'Shipments' },
        },
        {
          path: 'shipments/:id',
          name: 'AdminShipmentView',
          component: () => import('@/views/admin/ShipmentView.vue'),
          meta: { title: 'Shipment Details' },
        },
        // Consolidations
        {
          path: 'consolidations',
          name: 'AdminConsolidations',
          component: () => import('@/views/admin/Consolidations.vue'),
          meta: { title: 'Consolidations' },
        },
        {
          path: 'consolidations/create',
          name: 'AdminConsolidationCreate',
          component: () => import('@/views/admin/ConsolidationForm.vue'),
          meta: { title: 'New Consolidation' },
        },
        {
          path: 'consolidations/:id',
          name: 'AdminConsolidationView',
          component: () => import('@/views/admin/ConsolidationView.vue'),
          meta: { title: 'Consolidation Details' },
        },
        {
          path: 'consolidations/:id/edit',
          name: 'AdminConsolidationEdit',
          component: () => import('@/views/admin/ConsolidationForm.vue'),
          meta: { title: 'Edit Consolidation' },
        },
        // Financial
        {
          path: 'accounts',
          name: 'AdminAccounts',
          component: () => import('@/views/admin/Accounts.vue'),
          meta: { title: 'Chart of Accounts' },
        },
        {
          path: 'vouchers',
          name: 'AdminVouchers',
          component: () => import('@/views/admin/Vouchers.vue'),
          meta: { title: 'Vouchers' },
        },
        {
          path: 'vouchers/:id',
          name: 'AdminVoucherView',
          component: () => import('@/views/admin/VoucherView.vue'),
          meta: { title: 'Voucher Details' },
        },
        {
          path: 'vouchers/approval',
          name: 'AdminVoucherApproval',
          component: () => import('@/views/admin/VoucherApproval.vue'),
          meta: { title: 'Approval Screen' },
        },
        {
          path: 'journal',
          name: 'AdminJournal',
          component: () => import('@/views/admin/Journal.vue'),
          meta: { title: 'General Journal' },
        },
        {
          path: 'ledger',
          name: 'AdminLedger',
          component: () => import('@/views/admin/Ledger.vue'),
          meta: { title: 'Ledger' },
        },
        {
          path: 'trial-balance',
          name: 'AdminTrialBalance',
          component: () => import('@/views/admin/TrialBalance.vue'),
          meta: { title: 'Trial Balance' },
        },
        // P&L
        {
          path: 'pandl/since-inception',
          name: 'AdminPandLSinceInception',
          component: () => import('@/views/admin/PandL/SinceInception.vue'),
          meta: { title: 'P&L Since Inception' },
        },
        {
          path: 'pandl/yearly',
          name: 'AdminPandLYearly',
          component: () => import('@/views/admin/PandL/Yearly.vue'),
          meta: { title: 'P&L Yearly' },
        },
        {
          path: 'pandl/quarterly',
          name: 'AdminPandLQuarterly',
          component: () => import('@/views/admin/PandL/Quarterly.vue'),
          meta: { title: 'P&L Quarterly' },
        },
        {
          path: 'pandl/monthly',
          name: 'AdminPandLMonthly',
          component: () => import('@/views/admin/PandL/Monthly.vue'),
          meta: { title: 'P&L Monthly' },
        },
        {
          path: 'pandl/balance-sheet',
          name: 'AdminBalanceSheet',
          component: () => import('@/views/admin/PandL/BalanceSheet.vue'),
          meta: { title: 'Balance Sheet' },
        },
        // Invoices & Debtors
        {
          path: 'invoices',
          name: 'AdminInvoices',
          component: () => import('@/views/admin/Invoices.vue'),
          meta: { title: 'Invoices' },
        },
        {
          path: 'debtors',
          name: 'AdminDebtors',
          component: () => import('@/views/admin/Debtors.vue'),
          meta: { title: 'Debtors' },
        },
        // Directory & Setup
        {
          path: 'cities',
          name: 'AdminCities',
          component: () => import('@/views/admin/Cities.vue'),
          meta: { title: 'Cities' },
        },
        {
          path: 'payment-methods',
          name: 'AdminPaymentMethods',
          component: () => import('@/views/admin/PaymentMethods.vue'),
          meta: { title: 'Payment Methods' },
        },
        {
          path: 'sites',
          name: 'AdminSites',
          component: () => import('@/views/admin/Sites.vue'),
          meta: { title: 'Sites' },
        },
        {
          path: 'international-couriers',
          name: 'AdminInternationalCouriers',
          component: () => import('@/views/admin/InternationalCouriers.vue'),
          meta: { title: 'International Couriers' },
        },
        {
          path: 'local-couriers',
          name: 'AdminLocalCouriers',
          component: () => import('@/views/admin/LocalCouriers.vue'),
          meta: { title: 'Local Couriers' },
        },
        {
          path: 'shipment-statuses',
          name: 'AdminShipmentStatuses',
          component: () => import('@/views/admin/ShipmentStatuses.vue'),
          meta: { title: 'Shipment Statuses' },
        },
        {
          path: 'warehouses',
          name: 'AdminWarehouses',
          component: () => import('@/views/admin/Warehouses.vue'),
          meta: { title: 'Warehouses' },
        },
        {
          path: 'weight-discounts',
          name: 'AdminWeightDiscounts',
          component: () => import('@/views/admin/WeightDiscounts.vue'),
          meta: { title: 'Weight Discounts' },
        },
        {
          path: 'pages',
          name: 'AdminPages',
          component: () => import('@/views/admin/Pages.vue'),
          meta: { title: 'Page Settings' },
        },

        // ============================================================
        // LANDING PAGES - 14 MODULES
        // ============================================================
        {
          path: 'hero-slides',
          name: 'AdminHeroSlides',
          component: () => import('@/views/admin/HeroSlides.vue'),
          meta: { title: 'Hero Slides' },
        },
        {
          path: 'about-sections',
          name: 'AdminAboutSections',
          component: () => import('@/views/admin/AboutSections.vue'),
          meta: { title: 'About Sections' },
        },
        {
          path: 'services',
          name: 'AdminServices',
          component: () => import('@/views/admin/Services.vue'),
          meta: { title: 'Services' },
        },
        {
          path: 'testimonials',
          name: 'AdminTestimonials',
          component: () => import('@/views/admin/Testimonials.vue'),
          meta: { title: 'Testimonials' },
        },
        {
          path: 'team-members',
          name: 'AdminTeamMembers',
          component: () => import('@/views/admin/TeamMembers.vue'),
          meta: { title: 'Team Members' },
        },
        {
          path: 'pricing-plans',
          name: 'AdminPricingPlans',
          component: () => import('@/views/admin/PricingPlans.vue'),
          meta: { title: 'Pricing Plans' },
        },
        {
          path: 'faqs',
          name: 'AdminFaqs',
          component: () => import('@/views/admin/Faqs.vue'),
          meta: { title: 'FAQs' },
        },
        {
          path: 'blog-posts',
          name: 'AdminBlogPosts',
          component: () => import('@/views/admin/BlogPosts.vue'),
          meta: { title: 'Blog Posts' },
        },
        {
          path: 'why-us-sections',
          name: 'AdminWhyUsSections',
          component: () => import('@/views/admin/WhyUsSections.vue'),
          meta: { title: 'Why Us Sections' },
        },
        {
          path: 'contact-sections',
          name: 'AdminContactSections',
          component: () => import('@/views/admin/ContactSections.vue'),
          meta: { title: 'Contact Sections' },
        },
        {
          path: 'footer-sections',
          name: 'AdminFooterSections',
          component: () => import('@/views/admin/FooterSections.vue'),
          meta: { title: 'Footer Sections' },
        },
        {
          path: 'stats',
          name: 'AdminStats',
          component: () => import('@/views/admin/Stats.vue'),
          meta: { title: 'Statistics' },
        },
        {
          path: 'quote-requests',
          name: 'AdminQuoteRequests',
          component: () => import('@/views/admin/QuoteRequests.vue'),
          meta: { title: 'Quote Requests' },
        },
        {
          path: 'prohibited-items',
          name: 'AdminProhibitedItems',
          component: () => import('@/views/admin/ProhibitedItems.vue'),
          meta: { title: 'Prohibited Items' },
        },
      ],
    },

    // Catch-all
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: () => import('@/views/Errors/FourZeroFour.vue'),
      meta: { title: '404' },
    },
  ],
});

// ============================================================
// NAVIGATION GUARD
// ============================================================
router.beforeEach(async (to, from, next) => {
  document.title = `${to.meta.title || 'US2PK'} | US2PK`;

  const auth = useAuthStore();

  if (auth.token && !auth.user) {
    try {
      await auth.fetchUser();
    } catch {
      await auth.logout();
      next('/');
      return;
    }
  }

  const isPublicRoute = to.path === '/' || to.path === '/landing';
  const isGuestOnly = to.meta.guestOnly === true;
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
  const requiredRole = to.matched.find(record => record.meta.role)?.meta.role;

  if (requiresAuth) {
    if (!auth.isAuthenticated) {
      next({ path: '/', query: { redirect: to.fullPath } });
      return;
    }

    if (requiredRole && auth.user?.role !== requiredRole) {
      if (auth.isAdmin) {
        next('/admin/dashboard');
      } else {
        next('/user/dashboard');
      }
      return;
    }
  }

  if (isGuestOnly) {
    if (auth.isAuthenticated) {
      next(auth.isAdmin ? '/admin/dashboard' : '/user/dashboard');
      return;
    }
    next();
    return;
  }

  if (isPublicRoute && auth.isAuthenticated) {
    if (to.path === '/') {
      next(auth.isAdmin ? '/admin/dashboard' : '/user/dashboard');
      return;
    }
  }

  if (to.path.startsWith('/admin') && auth.isAuthenticated && !auth.isAdmin) {
    next('/user/dashboard');
    return;
  }

  if (to.meta.role === 'user' && auth.isAdmin) {
    next('/admin/dashboard');
    return;
  }

  next();
});

export default router;
