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
                    <router-link to="/signup"
                      class="nav-link ">Register</router-link>
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
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, computed } from 'vue';
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

onMounted(() => {
  loadAssets();
});

onBeforeUnmount(() => {
  unloadAssets();
});
</script>

