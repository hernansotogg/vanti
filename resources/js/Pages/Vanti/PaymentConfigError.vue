<script setup>
import Layout from "./Layouts/Layout.vue";

// Props
const props = defineProps({
  error: String,
  referencia: String,
  details: Object
});

defineOptions({ layout: Layout });

const goHome = () => {
  window.location.href = '/';
};

const tryAgain = () => {
  window.history.back();
};

const contactSupport = () => {
  // Aquí puedes agregar lógica para contactar soporte
  alert('Por favor contacte al administrador del sistema.');
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-orange-50 to-orange-100 flex items-center justify-center p-4">
    <div class="max-w-lg w-full bg-white rounded-2xl shadow-xl p-8 text-center">
      <!-- Icono de configuración -->
      <div class="mb-6">
        <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto">
          <svg class="w-10 h-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
        </div>
      </div>

      <!-- Título -->
      <h1 class="text-2xl font-bold text-gray-900 mb-4">
        Error de Configuración
      </h1>

      <!-- Mensaje -->
      <p class="text-gray-600 mb-6">
        {{ error || 'Hay un problema con la configuración del sistema de pagos. Por favor contacte al administrador.' }}
      </p>

      <!-- Detalles técnicos (solo si están disponibles) -->
      <div v-if="details" class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
        <h3 class="font-medium text-gray-900 mb-2">Detalles técnicos:</h3>
        <div class="space-y-2 text-sm">
          <div v-if="details.status" class="flex justify-between">
            <span class="text-gray-600">Estado HTTP:</span>
            <span class="font-medium">{{ details.status }}</span>
          </div>
          <div v-if="details.message" class="flex justify-between">
            <span class="text-gray-600">Mensaje:</span>
            <span class="font-medium">{{ details.message }}</span>
          </div>
          <div v-if="referencia" class="flex justify-between">
            <span class="text-gray-600">referencia:</span>
            <span class="font-medium">{{ referencia }}</span>
          </div>
        </div>
      </div>

      <!-- Información de ayuda -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-left">
        <h3 class="font-medium text-blue-900 mb-2">¿Qué puede hacer?</h3>
        <ul class="text-sm text-blue-800 space-y-1">
          <li>• Intente nuevamente en unos minutos</li>
          <li>• Verifique su conexión a internet</li>
          <li>• Contacte al soporte técnico si persiste</li>
        </ul>
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
          @click="contactSupport"
          class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 px-4 rounded-lg transition-colors"
        >
          Contactar Soporte
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
        Este error ha sido reportado automáticamente al equipo técnico.
      </p>
    </div>
  </div>
</template>
