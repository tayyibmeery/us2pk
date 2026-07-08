<template>
  <div class="site-section" id="faq-section">
    <div class="container">
      <div class="row mb-5">
        <div class="block-heading-1 col-12 text-center">
          <h2>Frequently Ask Questions</h2>
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

const props = defineProps(['faqs']);
const leftFaqs = computed(() => props.faqs.filter((_, i) => i % 2 === 0));
const rightFaqs = computed(() => props.faqs.filter((_, i) => i % 2 === 1));
</script>
