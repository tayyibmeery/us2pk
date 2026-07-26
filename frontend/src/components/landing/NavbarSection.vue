<template>
  <header class="nav">
    <div class="nav-inner">
      <router-link to="/" class="brand">
        <span class="dot"></span>US2PK
      </router-link>

      <!-- Mobile Toggle -->
      <button class="mobile-toggle" @click="toggleMenu" :aria-expanded="mobileOpen">
        <span v-if="!mobileOpen">☰</span>
        <span v-else>✕</span>
      </button>

      <div class="nav-right" :class="{ 'mobile-open': mobileOpen }">
        <nav class="nav-links">
          <a v-for="item in navbarItems" :key="item.section_key" :href="item.route_path || '#'" @click="closeMenu">
            {{ item.nav_label || item.section_name }}
          </a>
        </nav>

        <div class="nav-cta">
          <template v-if="!isAuthenticated">
            <router-link to="/signin" class="login" @click="closeMenu">Sign In</router-link>
            <router-link to="/signup" class="btn btn-amber" @click="closeMenu">Get Your US Address</router-link>
          </template>
          <template v-else>
            <router-link to="/dashboard" class="login" @click="closeMenu">Dashboard</router-link>
            <a href="#" @click.prevent="logout" class="btn btn-amber">Logout</a>
          </template>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import { useLandingStore } from '@/stores/landingStore';

const authStore = useAuthStore();
const landingStore = useLandingStore();
const router = useRouter();

const mobileOpen = ref(false);
const isAuthenticated = computed(() => authStore.isAuthenticated);
const navbarItems = computed(() => landingStore.getNavbarItems());

const toggleMenu = () => {
  mobileOpen.value = !mobileOpen.value;
  document.body.style.overflow = mobileOpen.value ? 'hidden' : '';
};

const closeMenu = () => {
  mobileOpen.value = false;
  document.body.style.overflow = '';
};

const logout = async () => {
  await authStore.logout();
  closeMenu();
  router.push('/signin');
};

const handleResize = () => {
  if (window.innerWidth > 960 && mobileOpen.value) closeMenu();
};

onMounted(() => {
  landingStore.fetchSettings();
  window.addEventListener('resize', handleResize);
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize);
  document.body.style.overflow = '';
});
</script>

<style scoped>
.nav {
  position: sticky;
  top: 0;
  z-index: 1000;
  background: rgba(10, 19, 48, .95);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(255, 255, 255, .08);
  box-shadow: 0 2px 20px rgba(0, 0, 0, .15);
}

.nav-inner {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 76px;
  position: relative;
}

.brand {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #fff;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 700;
  font-size: 22px;
  text-decoration: none;
  flex-shrink: 0;
  transition: opacity .2s;
}

.brand:hover {
  opacity: .85;
}

.brand .dot {
  width: 8px;
  height: 8px;
  background: #E0A93A;
  border-radius: 50%;
  display: inline-block;
  box-shadow: 0 0 12px rgba(224, 169, 58, .4);
}

.mobile-toggle {
  display: none;
  background: none;
  border: none;
  color: #fff;
  font-size: 26px;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 6px;
  transition: background .2s;
  line-height: 1;
  z-index: 1001;
}

.mobile-toggle:hover {
  background: rgba(255, 255, 255, .08);
}

.nav-right {
  display: flex;
  align-items: center;
  gap: 32px;
}

.nav-links {
  display: flex;
  gap: 32px;
  align-items: center;
}

.nav-links a {
  color: rgba(255, 255, 255, .7);
  font-size: 14.5px;
  font-weight: 500;
  transition: color .25s ease;
  text-decoration: none;
  position: relative;
  padding: 4px 0;
}

.nav-links a::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 0;
  height: 2px;
  background: #E0A93A;
  transition: width .3s ease;
  border-radius: 2px;
}

.nav-links a:hover {
  color: #fff;
}

.nav-links a:hover::after {
  width: 100%;
}

.nav-cta {
  display: flex;
  gap: 12px;
  align-items: center;
  flex-shrink: 0;
}

.nav-cta a.login {
  color: rgba(255, 255, 255, .75);
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  padding: 6px 12px;
  border-radius: 6px;
  transition: color .2s, background .2s;
}

.nav-cta a.login:hover {
  color: #fff;
  background: rgba(255, 255, 255, .06);
}

.btn-amber {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 22px;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 14px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  transition: all .25s ease;
  text-decoration: none;
  background: linear-gradient(135deg, #E0A93A, #d49a2a);
  color: #0A1330;
  white-space: nowrap;
  box-shadow: 0 2px 12px rgba(224, 169, 58, .25);
}

.btn-amber:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 24px rgba(224, 169, 58, .4);
  background: linear-gradient(135deg, #e8b84a, #d49a2a);
}

.btn-amber:active {
  transform: translateY(0);
}

@media (max-width: 960px) {
  .mobile-toggle {
    display: block;
  }

  .nav-right {
    position: fixed;
    top: 0;
    right: -100%;
    width: 320px;
    max-width: 85%;
    height: 100vh;
    background: rgba(10, 19, 48, .98);
    backdrop-filter: blur(16px);
    flex-direction: column;
    align-items: stretch;
    padding: 80px 28px 32px;
    gap: 0;
    transition: right .35s cubic-bezier(.4, 0, .2, 1);
    border-left: 1px solid rgba(255, 255, 255, .08);
    box-shadow: -8px 0 40px rgba(0, 0, 0, .3);
    overflow-y: auto;
  }

  .nav-right.mobile-open {
    right: 0;
  }

  .nav-right.mobile-open::before {
    content: '';
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .5);
    z-index: -1;
    animation: fadeIn .3s ease;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
  }

  .nav-links {
    flex-direction: column;
    align-items: stretch;
    gap: 4px;
    margin-bottom: 24px;
  }

  .nav-links a {
    padding: 12px 16px;
    font-size: 16px;
    border-radius: 8px;
    color: rgba(255, 255, 255, .8);
    transition: background .2s, color .2s;
  }

  .nav-links a::after {
    display: none;
  }

  .nav-links a:hover {
    background: rgba(255, 255, 255, .06);
    color: #fff;
  }

  .nav-cta {
    flex-direction: column;
    gap: 12px;
    width: 100%;
    border-top: 1px solid rgba(255, 255, 255, .08);
    padding-top: 20px;
  }

  .nav-cta a.login {
    text-align: center;
    padding: 12px;
    width: 100%;
    font-size: 15px;
  }

  .nav-cta .btn-amber {
    width: 100%;
    justify-content: center;
    padding: 14px 22px;
    font-size: 15px;
  }

  .nav-inner {
    padding: 0 16px;
    height: 64px;
  }

  .brand {
    font-size: 20px;
  }
}

@media (max-width: 480px) {
  .nav-right {
    width: 100%;
    max-width: 100%;
    padding: 72px 20px 24px;
  }

  .nav-links a {
    padding: 10px 14px;
    font-size: 15px;
  }

  .nav-cta .btn-amber {
    padding: 12px 18px;
    font-size: 14px;
  }

  .nav-inner {
    padding: 0 12px;
    height: 58px;
  }

  .brand {
    font-size: 18px;
  }

  .mobile-toggle {
    font-size: 22px;
  }
}

.nav-right::-webkit-scrollbar {
  width: 4px;
}

.nav-right::-webkit-scrollbar-track {
  background: transparent;
}

.nav-right::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, .15);
  border-radius: 4px;
}

.nav-right::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, .25);
}
</style>
