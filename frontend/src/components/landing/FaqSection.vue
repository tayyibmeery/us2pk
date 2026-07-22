<!-- src/components/landing/FaqSection.vue -->
<template>
  <div class="site-section" id="faq-section">
    <div class="container">
      <div class="row mb-5">
        <div class="block-heading-1 col-12 text-center">
          <h2>{{ sectionData?.title || 'Frequently Asked Questions' }}</h2>
          <p>{{ sectionData?.content || 'Find answers to common questions about our services.' }}</p>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <div v-for="(faq, index) in leftFaqs" :key="index" class="mb-5" data-aos="fade-up" data-aos-delay="100">
            <h3 class="text-black h4 mb-4">{{ faq.title }}</h3>
            <p>{{ faq.content }}</p>
          </div>
        </div>
        <div class="col-lg-6">
          <div v-for="(faq, index) in rightFaqs" :key="index" class="mb-5" data-aos="fade-up" data-aos-delay="100">
            <h3 class="text-black h4 mb-4">{{ faq.title }}</h3>
            <p>{{ faq.content }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useLandingStore } from '@/stores/landingStore';

const landingStore = useLandingStore();

const sectionData = computed(() => {
  const data = landingStore.getFaq;
  return data.length > 0 ? data[0] : null;
});

const defaultFaqs = [
  {
    id: 1,
    title: 'How does US2PK work?',
    content: 'Simply sign up, get your US address, shop from any US retailer, and we\'ll handle the shipping to Pakistan.'
  },
  {
    id: 2,
    title: 'How long does shipping take?',
    content: 'Air freight takes 5-7 days, while sea freight takes 25-30 days to reach Pakistan.'
  },
  {
    id: 3,
    title: 'Do you handle customs clearance?',
    content: 'Yes, we handle all customs documentation and clearance for your shipments.'
  },
  {
    id: 4,
    title: 'Can I consolidate multiple packages?',
    content: 'Yes! We offer package consolidation to save on shipping costs by combining multiple packages into one shipment.'
  }
];

const displayFaqs = computed(() => {
  const faqs = landingStore.getFaq;
  return faqs.length > 0 ? faqs : defaultFaqs;
});

const leftFaqs = computed(() => displayFaqs.value.filter((_, i) => i % 2 === 0));
const rightFaqs = computed(() => displayFaqs.value.filter((_, i) => i % 2 === 1));
</script>
