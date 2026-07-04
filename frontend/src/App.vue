<template>
  <ThemeProvider>
    <SidebarProvider>
      <RouterView />
    </SidebarProvider>
  </ThemeProvider>
</template>

<script setup lang="ts">
import ThemeProvider from './components/layout/ThemeProvider.vue'
import SidebarProvider from './components/layout/SidebarProvider.vue'
import flatpickr from 'flatpickr';

flatpickr.defaultConfig = {
  dateFormat: 'Y-m-d',   // value sent to backend
  altInput: true,        // show a pretty version in the input
  altFormat: 'd/m/Y',    // display as DD/MM/YYYY
  allowInput: true,
};

</script>
