<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Layout from '../Layouts/Layout.vue'


defineOptions({ layout: Layout })
const props = defineProps({
  option: {
    type: Number,
    default: 0,
  }
});
</script>


<template v-if="option !== 0">
  <!-- Overlay -->
  <div class="fixed inset-0 bg-black/20 z-[300] flex items-center justify-center">
    <div class="flex flex-col items-center space-y-4">
      <!-- Loading Animation with pulsing yellow circle -->
      <div class="w-12 h-12 bg-yellow-400 rounded-full pulsing-circle"></div>
      <!-- Loading text -->
      <p class="text-white text-3xl font-bold">Cargando...</p>
    </div>
  </div>
</template>

<style scoped>
/* Animación de parpadeo para el círculo amarillo */
.pulsing-circle {
  animation: pulse 0.5s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 0.4;
    transform: scale(1);
  }
  50% {
    opacity: 1;
    transform: scale(1.1);
  }
}
</style>
