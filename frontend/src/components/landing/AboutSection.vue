<template>
  <div class="site-section" id="about-section">
    <div class="container">
      <div class="row mb-5 justify-content-center">
        <div class="col-md-7 text-center">
          <div class="block-heading-1">
            <h2>{{ data?.title || 'About Us' }}</h2>
            <p>{{ data?.content || 'Far far away, behind the word mountains...' }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps(['data']);
</script>
