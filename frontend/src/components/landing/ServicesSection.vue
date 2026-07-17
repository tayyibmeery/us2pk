<!-- src/components/landing/ServicesSection.vue -->
<template>
  <div id="services" class="container-xxl py-5">
    <div class="container py-5">
      <div class="text-center wow fadeInUp">
        <h6 class="text-secondary text-uppercase">Our Services</h6>
        <h1 class="mb-5">Explore Our Services</h1>
      </div>
      <div class="row g-4">
        <div v-for="(service, index) in services" :key="index" class="col-md-6 col-lg-4 wow fadeInUp">
          <div class="service-item p-4">
            <div class="overflow-hidden mb-4">
              <img class="img-fluid" :src="service.image" :alt="service.title">
            </div>
            <h4 class="mb-3">{{ service.title }}</h4>
            <p>{{ service.description }}</p>
            <a class="btn-slide mt-2" href="#"><i class="fa fa-arrow-right"></i><span>Read More</span></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const services = [
  {
    title: 'Air Freight',
    description: 'Fast air shipping for urgent packages from US to PK.',
    image: '/landing/img/service-1.jpg'
  },
  {
    title: 'Ocean Freight',
    description: 'Cost‑effective sea freight for bulk shipments.',
    image: '/landing/img/service-2.jpg'
  },
  {
    title: 'Road Freight',
    description: 'Reliable overland delivery across Pakistan.',
    image: '/landing/img/service-3.jpg'
  },
  {
    title: 'Train Freight',
    description: 'Rail transport for heavy and bulky goods.',
    image: '/landing/img/service-4.jpg'
  },
  {
    title: 'Customs Clearance',
    description: 'We handle all customs paperwork and duties.',
    image: '/landing/img/service-5.jpg'
  },
  {
    title: 'Warehouse Solutions',
    description: 'Safe storage and consolidation at our US warehouse.',
    image: '/landing/img/service-6.jpg'
  }
];
</script>
