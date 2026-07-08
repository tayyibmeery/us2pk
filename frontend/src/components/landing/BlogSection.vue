<template>
  <div class="site-section py-5" id="blog-section">
    <div class="container">
      <div class="row justify-content-center text-center mb-5">
        <div class="col-lg-4 mb-5 mb-lg-0">
          <div class="block-heading-1">
            <h2>Articles</h2>
          </div>
        </div>
      </div>
      <div class="row">
        <div v-for="post in posts" :key="post.id" class="col-lg-6">
          <div class="mb-5 d-block blog-entry" data-aos="fade-right" data-aos-delay="">
            <a :href="`/blog/${post.slug}`" class="blog-thumbnail mb-3 d-block">
              <img :src="post.image || '/depot/images/depot_img_1.jpg'" alt="Image" class="img-fluid">
            </a>
            <div class="blog-excerpt">
              <span class="d-block text-muted">{{ post.meta?.date || 'Apr 19, 2019' }}</span>
              <h2 class="h4 mb-3"><a :href="`/blog/${post.slug}`">{{ post.title }}</a></h2>
              <p>{{ post.content }}</p>
              <p><a :href="`/blog/${post.slug}`" class="text-primary">Read More</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps(['posts']);
</script>
