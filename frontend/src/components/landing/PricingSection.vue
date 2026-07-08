<template>
  <div class="site-section bg-light" id="pricing-section">
    <div class="container">
      <div class="row mb-5 justify-content-center text-center">
        <div class="col-md-7">
          <div class="block-heading-1">
            <h2>Pricing</h2>
            <p>Far far away, behind the word mountains...</p>
          </div>
        </div>
      </div>
      <div class="row mb-5">
        <div v-for="plan in plans" :key="plan.id" class="col-md-6 mb-4 mb-lg-0 col-lg-4" data-aos="fade-up"
          data-aos-delay="">
          <div class="pricing">
            <h3 class="text-center text-black">{{ plan.title }}</h3>
            <div class="price text-center mb-4">
              <span><span>{{ plan.meta?.price || '$47' }}</span> / {{ plan.meta?.interval || 'year' }}</span>
            </div>
            <ul class="list-unstyled ul-check success mb-5" v-html="plan.content"></ul>
            <p class="text-center">
              <a href="#" class="btn btn-secondary btn-md">Buy Now</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps(['plans']);
</script>
