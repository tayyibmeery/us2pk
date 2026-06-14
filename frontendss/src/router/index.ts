// import { createRouter, createWebHistory } from 'vue-router';
// import { useAuthStore } from '@/stores/authStore';

// // Public views
// import Login from '@/views/auth/Login.vue';
// import Register from '@/views/auth/Register.vue';

// // Protected views (will implement later)
// const Dashboard = () => import('@/views/Dashboard.vue');

// const routes = [
//     { path: '/', redirect: '/login' },
//     { path: '/login', name: 'login', component: Login, meta: { guestOnly: true } },
//     { path: '/register', name: 'register', component: Register, meta: { guestOnly: true } },
//     {
//         path: '/dashboard',
//         name: 'dashboard',
//         component: Dashboard,
//         meta: { requiresAuth: true },
//     },
// ];

// const router = createRouter({
//     history: createWebHistory(),
//     routes,
// });

// router.beforeEach(async (to, from, next) => {
//     const authStore = useAuthStore();
//     // If token exists but user not loaded, try to fetch
//     if (authStore.token && !authStore.user) {
//         await authStore.fetchUser().catch(() => {
//             authStore.logout(); // invalid token
//         });
//     }

//     if (to.meta.requiresAuth && !authStore.isAuthenticated) {
//         next('/login');
//     } else if (to.meta.guestOnly && authStore.isAuthenticated) {
//         next('/dashboard');
//     } else {
//         next();
//     }
// });

// export default router;
import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

// Public
import Login from '@/views/auth/Login.vue';
import Register from '@/views/auth/Register.vue';

// User
import UserDashboard from '@/views/user/Dashboard.vue';

// Admin (lazy loaded)
const AdminDashboard = () => import('@/views/admin/Dashboard.vue');
const AdminUsers = () => import('@/views/admin/Users.vue');
// const AdminShipments = () => import('@/views/admin/Shipments.vue');
// const AdminNewShipment = () => import('@/views/admin/NewShipment.vue');
// const AdminConsolidations = () => import('@/views/admin/Consolidations.vue');
// const AdminAddresses = () => import('@/views/admin/Addresses.vue');
// const AdminCities = () => import('@/views/admin/Cities.vue');
// const AdminSettings = () => import('@/views/admin/Settings.vue');
// const AdminStatistics = () => import('@/views/admin/Statistics.vue');
// const AdminInvoices = () => import('@/views/admin/Invoices.vue');
// const AdminRevenues = () => import('@/views/admin/Revenues.vue');
// const AdminDebtors = () => import('@/views/admin/Debtors.vue');

const routes = [
    { path: '/', redirect: '/login' },
    { path: '/login', component: Login, meta: { guestOnly: true } },
    { path: '/register', component: Register, meta: { guestOnly: true } },
    {
        path: '/dashboard',
        component: UserDashboard,
        meta: { requiresAuth: true, role: 'user' }
    },
    {
        path: '/admin',
        component: AdminDashboard,
        meta: { requiresAuth: true, role: 'admin' },
        children: [
            { path: 'users', component: AdminUsers },
            // { path: 'shipments', component: AdminShipments },
            // { path: 'new-shipment', component: AdminNewShipment },
            // { path: 'consolidations', component: AdminConsolidations },
            // { path: 'addresses', component: AdminAddresses },
            // { path: 'cities', component: AdminCities },
            // { path: 'settings', component: AdminSettings },
            // { path: 'statistics', component: AdminStatistics },
            // { path: 'invoices', component: AdminInvoices },
            // { path: 'revenues', component: AdminRevenues },
            // { path: 'debtors', component: AdminDebtors },
            { path: '', redirect: '/admin/users' }
        ]
    }
];

const router = createRouter({ history: createWebHistory(), routes });

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    if (authStore.token && !authStore.user) {
        await authStore.fetchUser().catch(() => authStore.logout());
    }

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next('/login');
    } else if (to.meta.guestOnly && authStore.isAuthenticated) {
        next('/dashboard');
    } else if (to.meta.role && authStore.user?.role !== to.meta.role) {
        next('/dashboard'); // redirect if role mismatch
    } else {
        next();
    }
});

export default router;
