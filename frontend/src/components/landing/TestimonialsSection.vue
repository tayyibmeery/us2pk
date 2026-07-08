<template>
  <div class="site-section bg-light block-13" id="testimonials-section" data-aos="fade">
    <div class="container">
      <div class="text-center mb-5">
        <div class="block-heading-1">
          <h2>Happy Clients</h2>
        </div>
      </div>
      <div class="owl-carousel nonloop-block-13">
        <div v-for="testimonial in testimonials" :key="testimonial.id">
          <div class="block-testimony-1 text-center">
            <blockquote class="mb-4">
              <p>&ldquo;{{ testimonial.content }}&rdquo;</p>
            </blockquote>
            <figure>
              <img :src="testimonial.image || '/depot/images/person_1.jpg'" alt="Image"
                class="img-fluid rounded-circle mx-auto">
            </figure>
            <h3 class="font-size-20 text-black">{{ testimonial.title }}</h3>
            <div v-if="testimonial.meta && testimonial.meta.rating" class="text-warning">
              <span v-for="n in 5" :key="n" class="fa fa-star"
                :class="{ 'text-warning': n <= testimonial.meta.rating }"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps(['testimonials']);
</script>
