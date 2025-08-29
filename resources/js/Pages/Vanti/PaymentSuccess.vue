<script setup>

import Layout from "./Layouts/Layout.vue";

// Props
const props = defineProps({
  payment_id: String,
  status: String,
  referencia: String
});

defineOptions({ layout: Layout });

const goHome = () => {
  window.location.href = '/';
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-green-50 to-green-100 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center">
      <!-- Logo -->

      <!-- Icono de éxito -->
      <div class="mb-6">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto">
          <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
      </div>

      <!-- Título -->
      <h1 class="text-2xl font-bold text-gray-900 mb-4">
        ¡Pago Exitoso!
      </h1>

      <!-- Mensaje -->
      <p class="text-gray-600 mb-6">
        Su pago de servicios vanti ha sido procesado correctamente.
      </p>

      <!-- Detalles del pago -->
      <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
        <div class="space-y-2">
          <div class="flex justify-between">
            <span class="text-gray-600">Código:</span>
            <span class="font-medium">{{ referencia }}</span>
          </div>
          <div class="flex justify-between" v-if="payment_id">
            <span class="text-gray-600">ID de Pago:</span>
            <span class="font-medium">{{ payment_id }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Estado:</span>
            <span class="font-medium text-green-600">{{ status || 'Aprobado' }}</span>
          </div>
        </div>
      </div>

      <!-- Botón para volver -->
      <button
        @click="goHome"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors"
      >
        Realizar Nueva Consulta
      </button>

      <!-- Nota informativa -->
      <p class="text-sm text-gray-500 mt-4">
        Guarde este comprobante para sus registros.
      </p>
    </div>
  </div>
</template>
