<template>
  <PublicLayout>
    <HeroSection v-if="sections.hero && sections.hero[0]" :data="sections.hero[0]" />
    <ServicesSection v-if="sections.service" :services="sections.service" />
    <AboutSection v-if="sections.about && sections.about[0]" :data="sections.about[0]" />
    <WhyUsSection v-if="sections.whyus && sections.whyus[0]" :data="sections.whyus[0]" />
    <TestimonialsSection v-if="sections.testimonial" :testimonials="sections.testimonial" />
    <TeamSection v-if="sections.team" :team="sections.team" />
    <PricingSection v-if="sections.pricing" :plans="sections.pricing" />
    <FaqSection v-if="sections.faq" :faqs="sections.faq" />
    <BlogSection v-if="sections.blog" :posts="sections.blog" />
    <ContactSection v-if="sections.contact && sections.contact[0]" :data="sections.contact[0]" />
  </PublicLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import PublicLayout from '@/components/layout/PublicLayout.vue';
import HeroSection from '@/components/landing/HeroSection.vue';
import ServicesSection from '@/components/landing/ServicesSection.vue';
import AboutSection from '@/components/landing/AboutSection.vue';
import WhyUsSection from '@/components/landing/WhyUsSection.vue';
import TestimonialsSection from '@/components/landing/TestimonialsSection.vue';
import TeamSection from '@/components/landing/TeamSection.vue';
import PricingSection from '@/components/landing/PricingSection.vue';
import FaqSection from '@/components/landing/FaqSection.vue';
import BlogSection from '@/components/landing/BlogSection.vue';
import ContactSection from '@/components/landing/ContactSection.vue';

const sections = ref({});

onMounted(async () => {
  try {
    const res = await api.get('/public/landing');
    sections.value = res.data;
  } catch (e) {
    console.error('Failed to load landing data', e);
  }
});
</script>
