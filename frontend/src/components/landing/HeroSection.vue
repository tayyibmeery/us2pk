<template>
  <div class="ftco-blocks-cover-1">
    <div class="ftco-cover-1 overlay"
      :style="{ backgroundImage: `url(${data?.image || '/depot/images/depot_hero_1.jpg'})` }">
      <div class="container">
        <div class="row align-items-center justify-content-center text-center">
          <div class="col-lg-6">
            <h1>{{ data?.title || 'Transportations & Logistics' }}</h1>
            <p class="mb-5">{{ data?.content || 'Lorem ipsum dolor sit amet consectetur adipisicing elit.' }}</p>
            <form action="#">
              <div class="form-group d-flex">
                <input type="text" class="form-control" placeholder="Your tracking number">
                <input type="submit" class="btn btn-primary text-white px-4" value="Track Now">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps(['data']);
</script>
