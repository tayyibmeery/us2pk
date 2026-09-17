<template>
  <div>
    <!-- Navbar -->
    <header class="nav" :class="{ 'is-scrolled': isScrolled }">
      <div class="nav-inner">
        <router-link to="/" class="brand">
          <span class="dot"></span>{{ brandName }}
        </router-link>

        <!-- Mobile Toggle -->
        <button class="mobile-toggle" @click="mobileOpen = !mobileOpen">
          <span v-if="!mobileOpen">☰</span>
          <span v-else>✕</span>
        </button>

        <nav class="nav-links" :class="{ 'mobile-open': mobileOpen }">
          <a v-for="item in navbarItems" :key="item.section_key" :href="item.route_path || '#'"
            @click="mobileOpen = false">
            {{ item.nav_label || item.section_name }}
          </a>
        </nav>

        <div class="nav-cta" :class="{ 'mobile-open': mobileOpen }">
          <template v-if="!isAuthenticated">
            <router-link to="/signin" class="nav-btn nav-btn-ghost">Sign In</router-link>
            <router-link to="/signup" class="nav-btn nav-btn-solid">Get Started</router-link>
          </template>
          <template v-else>
            <router-link to="/dashboard" class="nav-btn nav-btn-ghost">Dashboard</router-link>
            <a href="#" @click.prevent="logout" class="nav-btn nav-btn-solid">Logout</a>
          </template>
        </div>
      </div>
    </header>

    <slot />

    <!-- Dynamic Footer -->
    <FooterSection />

    <!-- WhatsApp Widget -->
    <div class="whatsapp-widget">
      <transition name="tooltip-fade">
        <div v-if="showTooltip" class="whatsapp-tooltip">
          <button class="whatsapp-tooltip-close" @click.stop="dismissTooltip" aria-label="Close">&times;</button>
          <div class="whatsapp-tooltip-header">
            <span class="whatsapp-tooltip-avatar">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="20" height="20" fill="white">
                <path
                  d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
              </svg>
            </span>
            <div>
              <strong>{{ brandName }} Support</strong>
              <span class="whatsapp-tooltip-status"><span class="status-dot"></span> Online now</span>
            </div>
          </div>
          <p class="whatsapp-tooltip-msg">Hi there 👋 Need help with a shipment? Chat with us on WhatsApp.</p>
        </div>
      </transition>
      <a :href="whatsappUrl" target="_blank" rel="noopener noreferrer" class="whatsapp-float"
        :class="{ 'is-visible': isMounted }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
        @click="trackWhatsAppClick">
        <span class="whatsapp-ring"></span>
        <span class="whatsapp-online-dot"></span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="32" height="32" fill="white">
          <path
            d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
        </svg>
      </a>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import { useLandingStore } from '@/stores/landingStore';
import FooterSection from '@/components/landing/FooterSection.vue';

const authStore = useAuthStore();
const landingStore = useLandingStore();
const router = useRouter();

const isAuthenticated = computed(() => authStore.isAuthenticated);
const navbarItems = computed(() => landingStore.getNavbarItems());
const mobileOpen = ref(false);

// Get footer data from landing store
const footerData = computed(() => landingStore.getPublicFooter);

// Brand name from footer data
const brandName = computed(() => {
  return footerData.value?.title || 'US2PK';
});

// WhatsApp number from footer data
const whatsappNumber = computed(() => {
  return footerData.value?.whatsapp_number || '923015579810';
});

const whatsappMessage = 'Hello US2PK! I have a question about shipping from USA to Pakistan.';
const whatsappUrl = computed(() => {
  const encodedMessage = encodeURIComponent(whatsappMessage);
  const number = whatsappNumber.value.replace(/[^0-9]/g, '');
  return `https://wa.me/${number}?text=${encodedMessage}`;
});

// =========================================================
// META PIXEL: WhatsApp Click Tracking
// =========================================================
// ⚠️ Test Event Code - REMOVE 'test_event_code' from the track call after testing is complete!
const META_TEST_EVENT_CODE = 'TEST82641';

function trackWhatsAppClick() {
  // Check if fbq (Meta Pixel) is available
  if (typeof window.fbq === 'function') {
    // Track as a "Contact" event (standard Meta event)
    // Alternative: use 'Lead' instead of 'Contact' if you prefer
    window.fbq('track', 'Contact', {
      content_name: 'WhatsApp Chat',
      content_category: 'Customer Support',
      value: 1.00,
      currency: 'USD'
    }, {
      test_event_code: META_TEST_EVENT_CODE  // ⚠️ REMOVE this for production!
    });

    console.log('✅ Meta Pixel: Contact event tracked (WhatsApp click)');
  } else {
    console.warn('⚠️ Meta Pixel (fbq) not loaded');
  }
}

const isMounted = ref(false);
const showTooltip = ref(false);
let tooltipAutoHideTimer = null;

function dismissTooltip() {
  showTooltip.value = false;
  if (tooltipAutoHideTimer) clearTimeout(tooltipAutoHideTimer);
}

// Navbar scroll state — the header stays sticky/visible at all times;
// this just lets it pick up a shadow + tighten slightly once the page
// has actually moved, so it reads as a deliberate floating bar instead
// of a flat static one.
const isScrolled = ref(false);
function handleScroll() {
  isScrolled.value = window.scrollY > 8;
}

const logout = async () => {
  await authStore.logout();
  router.push('/signin');
};

onMounted(async () => {
  try {
    await Promise.all([
      landingStore.fetchSettings(),
      landingStore.fetchLandingData(),
      landingStore.fetchProhibitedItems()
    ]);
  } catch (error) {
    console.error('Error fetching data:', error);
  }

  isMounted.value = true;

  tooltipAutoHideTimer = setTimeout(() => {
    showTooltip.value = true;
    tooltipAutoHideTimer = setTimeout(() => {
      showTooltip.value = false;
    }, 6000);
  }, 2500);

  handleScroll();
  window.addEventListener('scroll', handleScroll, { passive: true });
});

onBeforeUnmount(() => {
  if (tooltipAutoHideTimer) clearTimeout(tooltipAutoHideTimer);
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style>
/* ===== GLOBAL DESIGN TOKENS ===== */
:root {
  --navy-950: #0A1330;
  --navy-900: #0F1B3D;
  --navy-800: #16265A;
  --navy-700: #1E3170;
  --paper: #F6F4EE;
  --paper-dim: #EDEAE0;
  --ink: #12141C;
  --ink-soft: #4B4F5E;
  --amber: #E0A93A;
  --teal: #2C8C86;
  --line: #DEDACB;
  --line-dark: rgba(255, 255, 255, .14);
  --danger: #C24A3E;
}

* {
  box-sizing: border-box;
}

html {
  scroll-behavior: smooth;
  /* overflow-x: clip (not hidden) — blocks horizontal overflow without
     turning body into a scroll container, which is what silently breaks
     position: sticky on the header below. */
  overflow-x: clip;
}

body {
  margin: 0;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  color: var(--ink);
  background: var(--paper);
  -webkit-font-smoothing: antialiased;
  overflow-x: clip;
  width: 100%;
}

h1,
h2,
h3,
h4 {
  font-family: 'Space Grotesk', -apple-system, sans-serif;
  margin: 0;
  letter-spacing: -0.01em;
}

p {
  line-height: 1.65;
  color: var(--ink-soft);
  margin: 0;
}

a {
  color: inherit;
  text-decoration: none;
}

img {
  max-width: 100%;
  display: block;
}

.wrap {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 32px;
}

section {
  padding: 1px 0;
}

.eyebrow {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 12px;
  letter-spacing: .16em;
  text-transform: uppercase;
  color: var(--teal);
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 14px;
}

.eyebrow::before {
  content: "";
  width: 22px;
  height: 1px;
  background: var(--teal);
  display: inline-block;
}

.eyebrow.on-dark {
  color: var(--amber);
}

.eyebrow.on-dark::before {
  background: var(--amber);
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 14px 26px;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 15px;
  border-radius: 2px;
  border: 1px solid transparent;
  cursor: pointer;
  transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
}

.btn-amber {
  background: var(--amber);
  color: var(--navy-950);
}

.btn-amber:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 24px rgba(224, 169, 58, .35);
}

.btn-outline {
  border-color: rgba(255, 255, 255, .35);
  color: #fff;
  background: transparent;
}

.btn-outline:hover {
  border-color: #fff;
  background: rgba(255, 255, 255, .06);
}

.btn-ink {
  border-color: var(--navy-900);
  color: var(--navy-900);
}

.btn-ink:hover {
  background: var(--navy-900);
  color: #fff;
}

/* ===== NAV ===== */
header.nav {
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(10, 19, 48, .92);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid var(--line-dark);
  width: 100%;
  transition: background .25s ease, box-shadow .25s ease, border-color .25s ease;
}

/* Once the page has actually scrolled, the bar tightens up and picks
   up depth — makes it read as a floating panel rather than a flat
   strip that happens to be pinned. */
header.nav.is-scrolled {
  background: rgba(7, 13, 33, .97);
  border-bottom-color: transparent;
  box-shadow: 0 14px 34px rgba(3, 6, 20, .35), 0 1px 0 rgba(255, 255, 255, .04) inset;
}

.nav-inner {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 32px;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  height: 76px;
  transition: height .25s ease;
}

header.nav.is-scrolled .nav-inner {
  height: 64px;
}

.brand {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #fff;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 700;
  font-size: 20px;
  flex-shrink: 0;
  transition: font-size .25s ease;
}

header.nav.is-scrolled .brand {
  font-size: 18px;
}

.brand .dot {
  width: 8px;
  height: 8px;
  background: var(--amber);
  border-radius: 50%;
}

.nav-links {
  display: flex;
  gap: 22px;
  align-items: center;
  /* keep this from being wider than the space it's given */
  flex: 1 1 auto;
  justify-content: center;
  min-width: 0;
}

.nav-links a {
  color: rgba(255, 255, 255, .75);
  font-size: 13.5px;
  font-weight: 500;
  position: relative;
  padding: 4px 0;
  transition: color .15s;
  white-space: nowrap;
}

.nav-links a:hover {
  color: #fff;
}

/* subtle underline-on-hover, gives the nav a bit more polish */
.nav-links a::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -2px;
  width: 0;
  height: 1px;
  background: var(--amber);
  transition: width .2s ease;
}

.nav-links a:hover::after {
  width: 100%;
}

/* ===== NAV BUTTONS ===== */
.nav-cta {
  display: flex;
  gap: 12px;
  align-items: center;
  flex-shrink: 0;
}

.nav-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 13.5px;
  padding: 8px 16px;
  border-radius: 6px;
  border: 1px solid transparent;
  transition: transform .15s ease, box-shadow .15s ease, background .15s ease, border-color .15s ease, color .15s ease;
  white-space: nowrap;
}

/* Sign In — quiet, outlined, secondary */
.nav-btn-ghost {
  color: rgba(255, 255, 255, .82);
  border-color: rgba(255, 255, 255, .28);
  background: transparent;
}

.nav-btn-ghost:hover {
  color: #fff;
  border-color: rgba(255, 255, 255, .55);
  background: rgba(255, 255, 255, .06);
}

/* Get Started / Logout — the one thing you want the eye drawn to */
.nav-btn-solid {
  background: var(--amber);
  color: var(--navy-950);
  border-color: var(--amber);
}

.nav-btn-solid:hover {
  transform: translateY(-1px);
  box-shadow: 0 8px 18px rgba(224, 169, 58, .3);
}

.mobile-toggle {
  display: none;
  background: none;
  border: none;
  color: #fff;
  font-size: 24px;
  cursor: pointer;
  flex-shrink: 0;
}

/* ===== WHATSAPP WIDGET ===== */
.whatsapp-widget {
  position: fixed;
  bottom: 28px;
  right: 28px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 14px;
}

.whatsapp-float {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: linear-gradient(145deg, #25d366, #128c7e);
  color: #fff;
  box-shadow: 0 10px 24px rgba(18, 140, 126, .35), 0 2px 6px rgba(0, 0, 0, .15);
  transform: scale(.4) translateY(20px);
  opacity: 0;
  transition: transform .45s cubic-bezier(.34, 1.56, .64, 1), opacity .35s ease, box-shadow .25s ease, background .25s ease;
}

.whatsapp-float.is-visible {
  transform: scale(1) translateY(0);
  opacity: 1;
}

.whatsapp-float:hover {
  transform: scale(1.08) translateY(-2px);
  box-shadow: 0 14px 30px rgba(18, 140, 126, .45), 0 4px 10px rgba(0, 0, 0, .2);
  background: linear-gradient(145deg, #2be374, #0f7a6b);
}

.whatsapp-float svg {
  position: relative;
  z-index: 2;
  filter: drop-shadow(0 1px 1px rgba(0, 0, 0, .15));
}

.whatsapp-ring {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  border: 2px solid rgba(37, 211, 102, .55);
  animation: ripple 2.4s ease-out infinite;
  pointer-events: none;
}

@keyframes ripple {
  0% {
    transform: scale(1);
    opacity: .6;
  }

  100% {
    transform: scale(1.6);
    opacity: 0;
  }
}

.whatsapp-online-dot {
  position: absolute;
  top: 3px;
  right: 3px;
  width: 13px;
  height: 13px;
  background: #4ade80;
  border: 2px solid #fff;
  border-radius: 50%;
  z-index: 3;
  box-shadow: 0 0 0 2px rgba(0, 0, 0, .06);
}

.whatsapp-tooltip {
  position: relative;
  width: 260px;
  background: #fff;
  border-radius: 14px;
  padding: 14px 16px 16px;
  box-shadow: 0 12px 32px rgba(0, 0, 0, .18);
}

.whatsapp-tooltip::after {
  content: '';
  position: absolute;
  bottom: -8px;
  right: 26px;
  width: 16px;
  height: 16px;
  background: #fff;
  transform: rotate(45deg);
  box-shadow: 4px 4px 8px rgba(0, 0, 0, .04);
}

.whatsapp-tooltip-close {
  position: absolute;
  top: 8px;
  right: 10px;
  border: none;
  background: transparent;
  color: #9ca3af;
  font-size: 18px;
  line-height: 1;
  cursor: pointer;
  padding: 2px 4px;
  border-radius: 4px;
  transition: color .15s ease, background .15s ease;
}

.whatsapp-tooltip-close:hover {
  color: #374151;
  background: #f3f4f6;
}

.whatsapp-tooltip-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 8px;
  padding-right: 16px;
}

.whatsapp-tooltip-avatar {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  min-width: 36px;
  border-radius: 50%;
  background: linear-gradient(145deg, #25d366, #128c7e);
  color: #fff;
}

.whatsapp-tooltip-header strong {
  display: block;
  font-size: 13.5px;
  color: #111827;
  line-height: 1.3;
}

.whatsapp-tooltip-status {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 11px;
  color: #6b7280;
  margin-top: 2px;
}

.status-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #22c55e;
}

.whatsapp-tooltip-msg {
  margin: 0;
  font-size: 12.5px;
  line-height: 1.55;
  color: #4b5563;
}

.tooltip-fade-enter-active,
.tooltip-fade-leave-active {
  transition: opacity .25s ease, transform .25s ease;
}

.tooltip-fade-enter-from,
.tooltip-fade-leave-to {
  opacity: 0;
  transform: translateY(8px) scale(.96);
}

/* ===== RESPONSIVE =====
   Raised from 960px to 1260px: with this many nav items (Home, About,
   Services, Testimonials, Team, Pricing, FAQ, Blog, Why Us, Contact,
   Stats, Prohibited Items) plus two buttons, there isn't room to keep
   them on one line below ~1260px without wrapping or clipping — so we
   switch to the hamburger menu earlier instead of letting it overflow. */
@media(max-width:1260px) {
  .mobile-toggle {
    display: block;
  }

  .nav-links {
    display: none;
    position: absolute;
    top: 76px;
    left: 0;
    right: 0;
    flex-direction: column;
    background: #0A1330;
    padding: 20px 32px;
    gap: 16px;
    border-bottom: 1px solid var(--line-dark);
  }

  .nav-links.mobile-open {
    display: flex;
  }

  .nav-links a {
    white-space: normal;
  }

  .nav-cta {
    display: none;
  }

  .nav-cta.mobile-open {
    display: flex;
    position: absolute;
    top: 76px;
    right: 0;
    padding: 20px 32px;
    background: #0A1330;
    border-bottom: 1px solid var(--line-dark);
  }

  section {
    padding: 1px 0;
  }
}

@media(max-width:640px) {
  .wrap {
    padding: 0 16px;
  }

  .nav-inner {
    padding: 0 16px;
  }

  .nav-links.mobile-open {
    padding: 16px;
  }

  .nav-cta.mobile-open {
    padding: 16px;
    flex-direction: column;
    width: 100%;
    gap: 12px;
  }

  .nav-cta.mobile-open .nav-btn {
    width: 100%;
  }

  .whatsapp-widget {
    bottom: 15px;
    right: 15px;
  }

  .whatsapp-float {
    width: 50px;
    height: 50px;
  }

  .whatsapp-float svg {
    width: 24px;
    height: 24px;
  }

  .whatsapp-tooltip {
    display: none;
  }
}
</style>
