<template>
  <div class="site-section" id="team-section">
    <div class="container">
      <div class="row mb-5 justify-content-center">
        <div class="col-md-7 text-center">
          <div class="block-heading-1">
            <h2>Our Staff</h2>
            <p>Far far away, behind the word mountains...</p>
          </div>
        </div>
      </div>
      <div class="owl-carousel owl-all mb-5">
        <div v-for="member in team" :key="member.id" class="block-team-member-1 text-center rounded h-100">
          <figure>
            <img :src="member.image || '/depot/images/person_1.jpg'" alt="Image" class="img-fluid rounded-circle">
          </figure>
          <h3 class="font-size-20 text-black">{{ member.title }}</h3>
          <span
            class="d-block font-gray-5 letter-spacing-1 text-uppercase font-size-12 mb-3">{{ member.meta?.position || 'Member' }}</span>
          <p class="mb-4">{{ member.content }}</p>
          <div class="block-social-1">
            <a :href="member.meta?.facebook || '#'" class="btn border-w-2 rounded primary-primary-outline--hover"><span
                class="icon-facebook"></span></a>
            <a :href="member.meta?.twitter || '#'" class="btn border-w-2 rounded primary-primary-outline--hover"><span
                class="icon-twitter"></span></a>
            <a :href="member.meta?.instagram || '#'" class="btn border-w-2 rounded primary-primary-outline--hover"><span
                class="icon-instagram"></span></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps(['team']);
</script>
