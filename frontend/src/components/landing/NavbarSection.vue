<!-- src/components/landing/NavbarSection.vue -->
<template>
  <nav class="navbar navbar-expand-lg bg-white navbar-light shadow border-top border-5 border-primary sticky-top p-0">
    <router-link to="/" class="navbar-brand bg-primary d-flex align-items-center px-4 px-lg-5">
      <h2 class="mb-2 text-white">US2PK</h2>
    </router-link>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <div class="navbar-nav ms-auto p-4 p-lg-0">
        <router-link to="/" class="nav-item nav-link active">Home</router-link>
        <a href="#about" class="nav-item nav-link">About</a>
        <a href="#services" class="nav-item nav-link">Services</a>
        <a href="#contact" class="nav-item nav-link">Contact</a>
      </div>
      <div class="d-flex align-items-center gap-3">
        <router-link to="/signin" class="btn btn-outline-primary btn-sm">Sign In</router-link>
        <router-link to="/signup" class="btn btn-primary btn-sm">Sign Up</router-link>
      </div>
    </div>
  </nav>
</template>

<script setup>
// No additional logic needed
</script>
