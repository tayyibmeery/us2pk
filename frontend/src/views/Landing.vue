<!-- src/views/Landing.vue -->
<template>
  <div class="landing-page">
    <!-- Spinner -->
    <div id="spinner"
      class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
      <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>

    <!-- Navbar -->
    <NavbarSection />

    <!-- Hero Section -->
    <HeroSection />

    <!-- About Section -->
    <AboutSection />

    <!-- Stats Section -->
    <StatsSection />

    <!-- Services Section -->
    <ServicesSection />

    <!-- Contact Section -->
    <ContactSection />

    <!-- Footer -->
    <FooterSection />

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-0 back-to-top">
      <i class="bi bi-arrow-up"></i>
    </a>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import NavbarSection from '@/components/landing/NavbarSection.vue';
import HeroSection from '@/components/landing/HeroSection.vue';
import AboutSection from '@/components/landing/AboutSection.vue';
import StatsSection from '@/components/landing/StatsSection.vue';
import ServicesSection from '@/components/landing/ServicesSection.vue';
import ContactSection from '@/components/landing/ContactSection.vue';
import FooterSection from '@/components/landing/FooterSection.vue';

onMounted(() => {
  if (typeof window !== 'undefined') {
    // Spinner
    const spinner = document.getElementById('spinner');
    if (spinner) {
      setTimeout(() => {
        spinner.classList.remove('show');
        spinner.classList.add('d-none');
      }, 500);
    }

    // Sticky Navbar
    const navbar = document.querySelector('.navbar');
    if (navbar) {
      window.addEventListener('scroll', () => {
        if (window.scrollY > 45) {
          navbar.classList.add('sticky-top', 'shadow-sm');
        } else {
          navbar.classList.remove('sticky-top', 'shadow-sm');
        }
      });
    }

    // Back to Top button
    const backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
      window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
          backToTop.style.display = 'block';
        } else {
          backToTop.style.display = 'none';
        }
      });
      backToTop.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }

    // Wow.js
    if (window.WOW) {
      new window.WOW().init();
    }

    // Counter Up
    const counters = document.querySelectorAll('[data-toggle="counter-up"]');
    if (window.counterUp && counters.length) {
      counters.forEach((counter) => {
        const target = parseInt(counter.innerText, 10);
        window.counterUp(counter, {
          duration: 2000,
          delay: 16,
        });
      });
    }

    // Owl Carousel
    if (window.$ && window.$.fn.owlCarousel) {
      // Header Carousel
      $('.header-carousel').owlCarousel({
        animateOut: 'fadeOut',
        items: 1,
        autoplay: true,
        smartSpeed: 1000,
        autoplayTimeout: 4000,
        loop: true,
        dots: false,
        nav: true,
        navText: [
          '<i class="bi bi-chevron-left"></i>',
          '<i class="bi bi-chevron-right"></i>'
        ]
      });

      // Testimonial Carousel
      $('.testimonial-carousel').owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        autoplayTimeout: 4000,
        loop: true,
        center: true,
        dots: true,
        nav: false,
        margin: 24,
        responsive: {
          0: { items: 1 },
          768: { items: 2 },
          992: { items: 3 }
        }
      });
    }
  }
});
</script>

<style scoped>
/* Component-specific styles can go here */
</style>
