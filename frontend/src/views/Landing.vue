<!-- src/views/Landing.vue -->
<template>
  <PublicLayout>
    <div class="landing-page">
      <!-- Spinner -->
      <!-- <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div> -->

      <!-- Hero Section -->
      <HeroSection />

      <!-- About Section -->
      <AboutSection />

      <!-- Stats Section -->
      <StatsSection />

      <!-- Services Section -->
      <ServicesSection />

      <!-- Testimonials Section -->
      <TestimonialsSection />

      <!-- Why Us Section -->
      <WhyUsSection />

      <!-- Team Section -->
      <TeamSection />

      <!-- Pricing Section -->
      <PricingSection />

      <!-- FAQ Section -->
      <FaqSection />

      <!-- Blog Section -->
      <BlogSection />

      <!-- Contact Section -->
      <ContactSection />

      <!-- Back to Top -->
      <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-0 back-to-top">
        <i class="bi bi-arrow-up"></i>
      </a>
    </div>
  </PublicLayout>
</template>

<script setup>
import { onMounted } from 'vue';
import { useLandingStore } from '@/stores/landingStore';
import PublicLayout from '@/components/layout/PublicLayout.vue';
import HeroSection from '@/components/landing/HeroSection.vue';
import AboutSection from '@/components/landing/AboutSection.vue';
import StatsSection from '@/components/landing/StatsSection.vue';
import ServicesSection from '@/components/landing/ServicesSection.vue';
import TestimonialsSection from '@/components/landing/TestimonialsSection.vue';
import WhyUsSection from '@/components/landing/WhyUsSection.vue';
import TeamSection from '@/components/landing/TeamSection.vue';
import PricingSection from '@/components/landing/PricingSection.vue';
import FaqSection from '@/components/landing/FaqSection.vue';
import BlogSection from '@/components/landing/BlogSection.vue';
import ContactSection from '@/components/landing/ContactSection.vue';

const landingStore = useLandingStore();

onMounted(async () => {
  await landingStore.fetchLandingData();

  if (typeof window !== 'undefined') {
    // Spinner
    const spinner = document.getElementById('spinner');
    if (spinner) {
      setTimeout(() => {
        spinner.classList.remove('show');
        spinner.classList.add('d-none');
      }, 500);
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
      const headerCarousel = document.querySelector('.header-carousel');
      if (headerCarousel) {
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
      }

      const testimonialCarousel = document.querySelector('.testimonial-carousel');
      if (testimonialCarousel) {
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

      const teamCarousel = document.querySelector('.owl-all');
      if (teamCarousel) {
        $('.owl-all').owlCarousel({
          autoplay: true,
          smartSpeed: 1000,
          autoplayTimeout: 4000,
          loop: true,
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
  }
});
</script>

<style scoped>
.landing-page {
  position: relative;
}
</style>
