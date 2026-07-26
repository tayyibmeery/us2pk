<template>
  <PublicLayout>
    <div class="landing-page">
      <template v-for="setting in enabledSections" :key="setting.section_key">
        <!-- Hero Section -->
        <HeroSection v-if="setting.section_key === 'hero'" :section-title="setting.section_title"
          :section-subtitle="setting.section_subtitle" />

        <!-- Stats Section -->
        <StatsSection v-else-if="setting.section_key === 'stats'" :section-title="setting.section_title"
          :section-subtitle="setting.section_subtitle" />

        <!-- About Section -->
        <AboutSection v-else-if="setting.section_key === 'about'" :section-title="setting.section_title"
          :section-subtitle="setting.section_subtitle" />

        <!-- Services Section -->
        <ServicesSection v-else-if="setting.section_key === 'services'" :section-title="setting.section_title"
          :section-subtitle="setting.section_subtitle" />

        <!-- Why Us Section -->
        <WhyUsSection v-else-if="setting.section_key === 'whyus'" :section-title="setting.section_title"
          :section-subtitle="setting.section_subtitle" />

        <!-- Testimonials Section -->
        <TestimonialsSection v-else-if="setting.section_key === 'testimonials'" :section-title="setting.section_title"
          :section-subtitle="setting.section_subtitle" />

        <!-- Team Section -->
        <TeamSection v-else-if="setting.section_key === 'team'" :section-title="setting.section_title"
          :section-subtitle="setting.section_subtitle" />

        <!-- Pricing Section -->
        <PricingSection v-else-if="setting.section_key === 'pricing'" :section-title="setting.section_title"
          :section-subtitle="setting.section_subtitle" />

        <!-- Prohibited Items Section -->
        <ProhibitedItemsSection v-else-if="setting.section_key === 'prohibited_items'"
          :section-title="setting.section_title" :section-subtitle="setting.section_subtitle" />

        <!-- FAQ Section -->
        <FaqSection v-else-if="setting.section_key === 'faq'" :section-title="setting.section_title"
          :section-subtitle="setting.section_subtitle" />

        <!-- Contact Section -->
        <ContactSection v-else-if="setting.section_key === 'contact'" :section-title="setting.section_title"
          :section-subtitle="setting.section_subtitle" />
      </template>

      <a href="#" class="back-to-top-btn" @click.prevent="scrollToTop">↑</a>
    </div>
  </PublicLayout>
</template>

<script setup>
import { onMounted, computed } from 'vue';
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
import ContactSection from '@/components/landing/ContactSection.vue';
import ProhibitedItemsSection from '@/components/landing/ProhibitedItemsSection.vue';

const landingStore = useLandingStore();
const enabledSections = computed(() => landingStore.getEnabledSections());

const scrollToTop = () => window.scrollTo({ top: 0, behavior: 'smooth' });

onMounted(async () => {
  await landingStore.fetchSettings();
  await landingStore.fetchLandingData();
  await landingStore.fetchProhibitedItems();

  const backToTop = document.querySelector('.back-to-top-btn');
  if (backToTop) {
    window.addEventListener('scroll', () => {
      backToTop.classList.toggle('visible', window.scrollY > 500);
    });
  }
});
</script>

<style scoped>
.landing-page {
  position: relative;
}

.back-to-top-btn {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 48px;
  height: 48px;
  background: #E0A93A;
  color: #0A1330;
  border: none;
  border-radius: 50%;
  font-size: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  opacity: 0;
  transform: translateY(20px);
  transition: opacity .3s ease, transform .3s ease;
  box-shadow: 0 4px 16px rgba(224, 169, 58, .4);
  z-index: 999;
  text-decoration: none;
}

.back-to-top-btn.visible {
  opacity: 1;
  transform: translateY(0);
}

.back-to-top-btn:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(224, 169, 58, .5);
}
</style>
