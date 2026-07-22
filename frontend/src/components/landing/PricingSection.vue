<!-- src/components/landing/PricingSection.vue -->
<template>
  <div class="site-section bg-light" id="pricing-section">
    <div class="container">
      <div class="row mb-5 justify-content-center text-center">
        <div class="col-md-7">
          <div class="block-heading-1">
            <h2>{{ sectionData?.title || 'Pricing' }}</h2>
            <p>{{ sectionData?.content || 'Far far away, behind the word mountains...' }}</p>
          </div>
        </div>
      </div>
      <div class="row mb-5">
        <div v-for="plan in displayPricing" :key="plan.id" class="col-md-6 mb-4 mb-lg-0 col-lg-4" data-aos="fade-up"
          data-aos-delay="">
          <div class="pricing">
            <h3 class="text-center text-black">{{ plan.title }}</h3>
            <div class="price text-center mb-4">
              <span><span>{{ plan.meta?.price || '$47' }}</span> / {{ plan.meta?.interval || 'year' }}</span>
            </div>
            <ul class="list-unstyled ul-check success mb-5" v-html="plan.content"></ul>
            <p class="text-center">
              <a :href="plan.meta?.link || '#'" class="btn btn-secondary btn-md">Buy Now</a>
            </p>
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
  const data = landingStore.getPricing;
  return data.length > 0 ? data[0] : null;
});

const defaultPricing = [
  {
    id: 1,
    title: 'Basic Plan',
    content: '<li><i class="fa fa-check"></i> HTML5 & CSS3</li><li><i class="fa fa-check"></i> Bootstrap v5</li><li><i class="fa fa-check"></i> FontAwesome Icons</li>',
    meta: { price: '$47', interval: 'year' }
  },
  {
    id: 2,
    title: 'Standard Plan',
    content: '<li><i class="fa fa-check"></i> HTML5 & CSS3</li><li><i class="fa fa-check"></i> Bootstrap v5</li><li><i class="fa fa-check"></i> FontAwesome Icons</li><li><i class="fa fa-check"></i> Responsive Layout</li>',
    meta: { price: '$97', interval: 'year' }
  },
  {
    id: 3,
    title: 'Premium Plan',
    content: '<li><i class="fa fa-check"></i> HTML5 & CSS3</li><li><i class="fa fa-check"></i> Bootstrap v5</li><li><i class="fa fa-check"></i> FontAwesome Icons</li><li><i class="fa fa-check"></i> Responsive Layout</li><li><i class="fa fa-check"></i> Cross-browser Support</li>',
    meta: { price: '$147', interval: 'year' }
  }
];

const displayPricing = computed(() => {
  const pricing = landingStore.getPricing;
  return pricing.length > 0 ? pricing : defaultPricing;
});
</script>
