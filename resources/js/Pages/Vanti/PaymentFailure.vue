<script setup>
import Layout from "./Layouts/Layout.vue";

// Props
const props = defineProps({
  payment_id: String,
  status: String,
  referencia: String,
  error: String
});

defineOptions({ layout: Layout });

const goHome = () => {
  window.location.href = '/';
};

const tryAgain = () => {
  window.history.back();
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center">
      <!-- Logo -->

      <!-- Icono de error -->
      <div class="mb-6">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto">
          <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </div>
      </div>

      <!-- Título -->
      <h1 class="text-2xl font-bold text-gray-900 mb-4">
        Pago No Procesado
      </h1>

      <!-- Mensaje -->
      <p class="text-gray-600 mb-6">
        {{ error || 'Hubo un problema al procesar su pago. Por favor intente nuevamente.' }}
      </p>

      <!-- Detalles del pago -->
      <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
        <div class="space-y-2">
          <div class="flex justify-between">
            <span class="text-gray-600">Código:</span>
            <span class="font-medium">{{ referencia }}</span>
          </div>
          <div class="flex justify-between" v-if="ref_payco">
            <span class="text-gray-600">Referencia ePayco:</span>
            <span class="font-medium">{{ ref_payco }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Estado:</span>
            <span class="font-medium text-red-600">{{ estado || 'Rechazado' }}</span>
          </div>
        </div>
      </div>

      <!-- Botones -->
      <div class="space-y-3">
        <button
          @click="tryAgain"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors"
        >
          Intentar Nuevamente
        </button>

        <button
          @click="goHome"
          class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-4 rounded-lg transition-colors"
        >
          Nueva Consulta
        </button>
      </div>

      <!-- Nota informativa -->
      <p class="text-sm text-gray-500 mt-4">
        Si el problema persiste, contacte al soporte técnico.
      </p>
    </div>
  </div>
</template>
