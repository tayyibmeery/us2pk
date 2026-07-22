<template>
  <div>
    <!-- Header -->
    <header class="site-navbar js-sticky-header site-navbar-target" role="banner">
      <div class="container">
        <div class="row align-items-center position-relative">
          <div class="site-logo">
            <a href="/" class="text-black"><span class="text-primary">US2PK</span></a>
          </div>
          <div class="col-12">
            <nav class="site-navigation text-right ml-auto" role="navigation">
              <ul class="site-menu main-menu js-clone-nav ml-auto d-none d-lg-block">
                <li><a href="#home-section" class="nav-link">Home</a></li>
                <li><a href="#services-section" class="nav-link">Services</a></li>
                <li><a href="#about-section" class="nav-link">About</a></li>
                <li><a href="#why-us-section" class="nav-link">Why Us</a></li>
                <li><a href="#testimonials-section" class="nav-link">Testimonials</a></li>
                <li><a href="#blog-section" class="nav-link">Blog</a></li>
                <li><a href="#contact-section" class="nav-link">Contact</a></li>
                <!-- Authentication Links -->
                <li class="nav-links" style="display: inline-flex; gap: 1rem; margin-left: 1rem;">
                  <template v-if="!isAuthenticated">
                    <router-link to="/signin" class="nav-link">Login</router-link>
                    <router-link to="/signup" class="nav-link ">Register</router-link>
                  </template>
                  <template v-else>
                    <router-link to="/dashboard" class="nav-link">Dashboard</router-link>
                    <a href="#" @click.prevent="logout" class="nav-link">Logout</a>
                  </template>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </header>

    <slot />

    <!-- Footer -->
    <footer class="site-footer">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-7">
                <h2 class="footer-heading mb-4">About Us</h2>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eum voluptate debitis voluptatum et dolorum.
                </p>
              </div>
              <div class="col-md-4 ml-auto">
                <h2 class="footer-heading mb-4">Features</h2>
                <ul class="list-unstyled">
                  <li><a href="#">About Us</a></li>
                  <li><a href="#">Testimonials</a></li>
                  <li><a href="#">Terms of Service</a></li>
                  <li><a href="#">Privacy</a></li>
                  <li><a href="#">Contact Us</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-4 ml-auto">
            <div class="mb-5">
              <h2 class="footer-heading mb-4">Subscribe to Newsletter</h2>
              <form action="#" method="post" class="footer-suscribe-form">
                <div class="input-group mb-3">
                  <input type="text" class="form-control border-secondary text-white bg-transparent"
                    placeholder="Enter Email" aria-label="Enter Email" aria-describedby="button-addon2">
                  <div class="input-group-append">
                    <button class="btn btn-primary text-white" type="button" id="button-addon2">Subscribe</button>
                  </div>
                </div>
              </form>
            </div>
            <h2 class="footer-heading mb-4">Follow Us</h2>
            <a href="#" class="pl-0 pr-3"><span class="icon-facebook"></span></a>
            <a href="#" class="pl-3 pr-3"><span class="icon-twitter"></span></a>
            <a href="#" class="pl-3 pr-3"><span class="icon-instagram"></span></a>
            <a href="#" class="pl-3 pr-3"><span class="icon-linkedin"></span></a>
          </div>
        </div>
        <div class="row pt-5 mt-5 text-center">
          <div class="col-md-12">
            <div class="border-top pt-5">
              <p>
                Copyright &copy; {{ new Date().getFullYear() }} All rights reserved | This template is made with <i
                  class="icon-heart" aria-hidden="true"></i> by <a href="https://colorlib.com"
                  target="_blank">Colorlib</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </footer>

    <!-- ============================================================
         WHATSAPP FLOATING WIDGET
         ============================================================ -->
    <div class="whatsapp-widget">
      <!-- Tooltip / Message Preview Bubble -->
      <transition name="tooltip-fade">
        <div v-if="showTooltip" class="whatsapp-tooltip">
          <button class="whatsapp-tooltip-close" @click.stop="dismissTooltip" aria-label="Close">
            &times;
          </button>
          <div class="whatsapp-tooltip-header">
            <span class="whatsapp-tooltip-avatar">
              <!-- Using SVG WhatsApp icon -->
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="20" height="20" fill="white">
                <path
                  d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
              </svg>
            </span>
            <div>
              <strong>US2PK Support</strong>
              <span class="whatsapp-tooltip-status">
                <span class="status-dot"></span> Online now
              </span>
            </div>
          </div>
          <p class="whatsapp-tooltip-msg">Hi there 👋 Need help with a shipment? Chat with us on WhatsApp.</p>
        </div>
      </transition>

      <!-- WhatsApp Floating Button -->
      <a :href="whatsappUrl" target="_blank" rel="noopener noreferrer" class="whatsapp-float"
        :class="{ 'is-visible': isMounted }" aria-label="Chat with US2PK on WhatsApp" @mouseenter="showTooltip = true"
        @mouseleave="showTooltip = false">
        <span class="whatsapp-ring"></span>
        <span class="whatsapp-online-dot"></span>
        <!-- WhatsApp SVG Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="32" height="32" fill="white">
          <path
            d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
        </svg>
      </a>
    </div>
    <!-- End WhatsApp Floating Widget -->
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const cssFiles = [
  '/depot/fonts/icomoon/style.css',
  '/depot/css/bootstrap.min.css',
  '/depot/css/jquery.fancybox.min.css',
  '/depot/css/owl.carousel.min.css',
  '/depot/css/owl.theme.default.min.css',
  '/depot/fonts/flaticon/font/flaticon.css',
  '/depot/css/aos.css',
  '/depot/css/style.css',
];

const jsFiles = [
  '/depot/js/jquery-3.3.1.min.js',
  '/depot/js/popper.min.js',
  '/depot/js/bootstrap.min.js',
  '/depot/js/owl.carousel.min.js',
  '/depot/js/jquery.sticky.js',
  '/depot/js/jquery.waypoints.min.js',
  '/depot/js/jquery.animateNumber.min.js',
  '/depot/js/jquery.fancybox.min.js',
  '/depot/js/jquery.easing.1.3.js',
  '/depot/js/aos.js',
  '/depot/js/main.js',
];

let addedLinks = [];
let addedScripts = [];

function loadAssets() {
  cssFiles.forEach(href => {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = href;
    document.head.appendChild(link);
    addedLinks.push(link);
  });
  jsFiles.forEach(src => {
    const script = document.createElement('script');
    script.src = src;
    script.async = false;
    document.body.appendChild(script);
    addedScripts.push(script);
  });
}

function unloadAssets() {
  addedLinks.forEach(link => link.remove());
  addedLinks = [];
  addedScripts.forEach(script => script.remove());
  addedScripts = [];
}

const authStore = useAuthStore();
const router = useRouter();

const isAuthenticated = computed(() => authStore.isAuthenticated);
const user = computed(() => authStore.user);

const logout = async () => {
  await authStore.logout();
  router.push('/signin');
};

// ============================================================
// WHATSAPP CONFIGURATION
// ============================================================
// Replace with your actual WhatsApp number (without + sign)
const whatsappNumber = '923015579810';
const whatsappMessage = 'Hello US2PK! I have a question about shipping from USA to Pakistan.';

const whatsappUrl = computed(() => {
  const encodedMessage = encodeURIComponent(whatsappMessage);
  return `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;
});

const isMounted = ref(false);
const showTooltip = ref(false);
let tooltipAutoHideTimer = null;

function dismissTooltip() {
  showTooltip.value = false;
  if (tooltipAutoHideTimer) clearTimeout(tooltipAutoHideTimer);
}
// ============================================================

onMounted(() => {
  loadAssets();

  requestAnimationFrame(() => {
    isMounted.value = true;
  });

  tooltipAutoHideTimer = setTimeout(() => {
    showTooltip.value = true;
    tooltipAutoHideTimer = setTimeout(() => {
      showTooltip.value = false;
    }, 6000);
  }, 2500);
});

onBeforeUnmount(() => {
  unloadAssets();
  if (tooltipAutoHideTimer) clearTimeout(tooltipAutoHideTimer);
});
</script>

<style scoped>
/* ============================================================
   WHATSAPP FLOATING WIDGET
   ============================================================ */
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

/* --- Floating button --- */
.whatsapp-float {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: linear-gradient(145deg, #25d366, #128c7e);
  color: #ffffff;
  text-decoration: none;
  box-shadow:
    0 10px 24px rgba(18, 140, 126, 0.35),
    0 2px 6px rgba(0, 0, 0, 0.15);
  transform: scale(0.4) translateY(20px);
  opacity: 0;
  transition:
    transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1),
    opacity 0.35s ease,
    box-shadow 0.25s ease,
    background 0.25s ease;
}

.whatsapp-float.is-visible {
  transform: scale(1) translateY(0);
  opacity: 1;
}

.whatsapp-float:hover {
  transform: scale(1.08) translateY(-2px);
  box-shadow:
    0 14px 30px rgba(18, 140, 126, 0.45),
    0 4px 10px rgba(0, 0, 0, 0.2);
  background: linear-gradient(145deg, #2be374, #0f7a6b);
  color: #ffffff;
}

.whatsapp-float:active {
  transform: scale(0.94);
}

.whatsapp-float svg {
  position: relative;
  z-index: 2;
  filter: drop-shadow(0 1px 1px rgba(0, 0, 0, 0.15));
}

/* Expanding ripple ring */
.whatsapp-ring {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  border: 2px solid rgba(37, 211, 102, 0.55);
  animation: ripple 2.4s ease-out infinite;
  pointer-events: none;
}

@keyframes ripple {
  0% {
    transform: scale(1);
    opacity: 0.6;
  }

  100% {
    transform: scale(1.6);
    opacity: 0;
  }
}

/* Small "online" indicator dot */
.whatsapp-online-dot {
  position: absolute;
  top: 3px;
  right: 3px;
  width: 13px;
  height: 13px;
  background: #4ade80;
  border: 2px solid #ffffff;
  border-radius: 50%;
  z-index: 3;
  box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.06);
}

/* --- Tooltip / message preview bubble --- */
.whatsapp-tooltip {
  position: relative;
  width: 260px;
  background: #ffffff;
  border-radius: 14px;
  padding: 14px 16px 16px;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
  font-family: inherit;
}

.whatsapp-tooltip::after {
  content: '';
  position: absolute;
  bottom: -8px;
  right: 26px;
  width: 16px;
  height: 16px;
  background: #ffffff;
  transform: rotate(45deg);
  box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.04);
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
  transition: color 0.15s ease, background 0.15s ease;
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
  color: #ffffff;
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

/* Tooltip transitions */
.tooltip-fade-enter-active,
.tooltip-fade-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.tooltip-fade-enter-from,
.tooltip-fade-leave-to {
  opacity: 0;
  transform: translateY(8px) scale(0.96);
}

/* Responsive - Tablet */
@media (max-width: 768px) {
  .whatsapp-widget {
    bottom: 20px;
    right: 20px;
  }

  .whatsapp-float {
    width: 56px;
    height: 56px;
  }

  .whatsapp-float svg {
    width: 28px;
    height: 28px;
  }

  .whatsapp-tooltip {
    width: 220px;
  }
}

/* Responsive - Mobile: hide tooltip content, keep just the button */
@media (max-width: 480px) {
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
