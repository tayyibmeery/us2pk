<template>
  <router-link to="/" class="lg:hidden">
    <img class="dark:hidden" src="/images/logo/logo.png" alt="Logo" style="max-height: 2.5rem;" />
    <img class="hidden dark:block" src="/images/logo/logo-dark.svg" alt="Logo" style="max-height: 2.5rem;" />
  </router-link>
</template>

<script setup>
import { RouterLink } from 'vue-router'
</script>
