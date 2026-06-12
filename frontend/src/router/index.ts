import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

// Public views
import Login from '@/views/auth/Login.vue';
import Register from '@/views/auth/Register.vue';

// Protected views (will implement later)
const Dashboard = () => import('@/views/Dashboard.vue');

const routes = [
    { path: '/', redirect: '/login' },
    { path: '/login', name: 'login', component: Login, meta: { guestOnly: true } },
    { path: '/register', name: 'register', component: Register, meta: { guestOnly: true } },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    // If token exists but user not loaded, try to fetch
    if (authStore.token && !authStore.user) {
        await authStore.fetchUser().catch(() => {
            authStore.logout(); // invalid token
        });
    }

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next('/login');
    } else if (to.meta.guestOnly && authStore.isAuthenticated) {
        next('/dashboard');
    } else {
        next();
    }
});

export default router;
