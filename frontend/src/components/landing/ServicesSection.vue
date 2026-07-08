<template>
  <div class="site-section bg-light" id="services-section">
    <div class="container">
      <div class="row mb-5 justify-content-center">
        <div class="col-md-7 text-center">
          <div class="block-heading-1">
            <h2>Services</h2>
            <p>Far far away, behind the word mountains...</p>
          </div>
        </div>
      </div>
      <div class="owl-carousel owl-all">
        <div v-for="service in services" :key="service.id" class="block__35630 text-center">
          <div class="icon mb-0">
            <span :class="service.icon || 'flaticon-ferry'"></span>
          </div>
          <h3 class="mb-3">{{ service.title }}</h3>
          <p>{{ service.content }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps(['services']);
</script>
