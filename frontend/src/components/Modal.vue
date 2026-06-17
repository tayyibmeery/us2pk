<template>
  <teleport to="body">
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" @click.self="close">
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" @click="close"></div>
        <div class="relative w-full max-w-2xl">
          <slot name="body" />
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup lang="ts">
defineProps < { isOpen: boolean } > ()
const emit = defineEmits(['close'])

const close = () => emit('close')
</script>
