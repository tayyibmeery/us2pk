<template>
  <div class="block__73694 site-section border-top" id="why-us-section">
    <div class="container">
      <div class="row d-flex no-gutters align-items-stretch">
        <div class="col-12 col-lg-6 block__73422 order-lg-2"
          style="background-image: url('/depot/images/depot_delivery_1.jpg');" data-aos="fade-left"></div>
        <div class="col-lg-5 mr-auto p-lg-5 mt-4 mt-lg-0 order-lg-1" data-aos="fade-right">
          <h2 class="mb-4 text-black">{{ data?.title || 'Why Us' }}</h2>
          <p>{{ data?.content || 'Lorem ipsum dolor, sit amet consectetur adipisicing elit.' }}</p>
          <ul class="ul-check primary list-unstyled mt-5">
            <li>Cargo express</li>
            <li>Secure Services</li>
            <li>Secure Warehouseing</li>
            <li>Cost savings</li>
            <li>Proven by great companies</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps(['data']);
</script>
