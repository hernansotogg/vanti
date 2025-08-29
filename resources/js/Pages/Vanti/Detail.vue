<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import Layout from "./Layouts/Layout.vue";
import Cargando from "./Components/Cargando.vue";
import logo from '@/assets/vanti/logo.png';
import logofooter from '@/assets/vanti/footerlogo.png';

// Props
const props = defineProps({
    apiData: Object,
    referencia: String,
    empresa: String,
    tieneDeuda: Boolean,
});

// Lógica
defineOptions({ layout: Layout });

const option = ref(0);

// Computed para obtener los datos de la factura
const facturaData = computed(() => {
    if (props.apiData?.data?.DataAuthorizer) {
        return props.apiData.data.DataAuthorizer;
    }
    return null;
});

// Computed para formatear el monto
const montoFormateado = computed(() => {
    if (facturaData.value?.Amount) {
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(facturaData.value.Amount);
    }
    return '$0';
});



// Formulario para el pago
const form = useForm({
    total: 0,
});

// Función para manejar el pago
const procesarPago = () => {
    // Verificar que tenemos datos válidos
    if (!facturaData.value || !facturaData.value.Amount) {
        console.error('No hay datos de factura válidos');
        return;
    }

    option.value = 1;

    // Obtener el monto como número entero
    const montoTotal = Math.round(facturaData.value.Amount);

    // Actualizar el formulario con el monto correcto
    form.total = montoTotal;

    console.log('Enviando pago con datos:', {
        total: form.total,
        amount: facturaData.value.Amount,
        montoTotal: montoTotal
    });

    // Enviar datos del pago
    setTimeout(() => {
        form.post("/pago", {
            preserveState: false,
            preserveScroll: true,
        });
    }, 1000);
};

// Función para volver atrás
const volver = () => {
    router.visit('/');
};
</script>

<template>
  <div class="min-h-screen bg-white">
    <!-- Header móvil -->
    <div class="lg:hidden bg-[#113455] px-4 py-1.5">
      <button class="text-[#ffcd3f] border border-[#ffcd3f] rounded px-3 py-1.5">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>
    </div>

    <!-- Header desktop -->
    <div class="hidden lg:block bg-[#113455] text-white">
      <div class=" mx-auto pl-10 py-2">
        <div class="flex justify-between items-center text-xl">
          <div class="flex space-x-6">
            <a href="#" class="text-[#ffcd3f] border-b-2 border-[#ffcd3f]">Hogares</a>
            <a href="#" class="hover:text-[#ffcd3f]">Empresas</a>
            <a href="#" class="hover:text-[#ffcd3f]">Constructores</a>
            <a href="#" class="hover:text-[#ffcd3f]">Gas Natural Vehicular</a>
          </div>
          <div class="flex space-x-6">
            <a href="#" class="hover:text-[#ffcd3f]">Conócenos</a>
            <a href="#" class="hover:text-[#ffcd3f]">Contáctanos</a>
            <span class="bg-[#ff8e67] px-4 py-2 rounded text-sm text-[#113455] flex gap-1 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
</svg>

Emergencias: Llamar al 164</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Logo -->
    <div class="bg-white py-5">
      <div class="container mx-auto">
        <div class="flex justify-center md:justify-start">
          <img :src="logo" alt="Vanti" class="h-36 md:h-48">
        </div>
      </div>
    </div>

    <!-- Contenido principal -->
    <div class="container mx-auto px-4 ">
      <div class="max-w-md mx-auto md:max-w-lg bg-white rounded-lg border border-gray-300 shadow-lg ">
        <div class="">
            <!-- Header con botón volver -->
            <div class="flex items-center justify-between p-4 border-b border-gray-300 bg-[#21252908]">
              <button @click="volver" class="flex items-center text-[#113455] hover:text-[#0f2d47]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
</svg>

              </button>
              <h1 class="text-xl font-semibold text-[#113455] flex items-center">
                Pagos Vanti

              </h1>
              <div></div> <!-- Spacer para centrar el título -->
            </div>

        <!-- Contenido de la factura -->
        <div class="p-6 space-y-4">
          <!-- Cuenta contrato -->
          <div>
            <h3 class="text-center text-[#113455] font-medium ">Cuenta contrato</h3>
            <div class="bg-[#e2d9d9] rounded-lg px-3 py-1.5 text-center">
              <span class="text-[#113455]">{{ props.referencia }}</span>
            </div>
          </div>

          <!-- Valor a pagar -->
          <div>
            <h3 class="text-center text-[#113455] font-medium ">Valor a pagar</h3>
            <div class="bg-[#e2d9d9] rounded-lg px-3 py-1.5 text-center">
              <span class="text-[#113455] ">{{ montoFormateado }}</span>
            </div>
          </div>

          <!-- Botón ir a pagar -->
          <button
            @click="procesarPago"
            type="button"
            class="w-full bg-[#113455] text-white py-3 px-4 rounded-lg hover:bg-[#0f2d47] transition duration-200"
          >
            Ir a pagar: {{ montoFormateado }}
          </button>
        </div>
        </div>

      </div>

      <!-- Footer -->
      <div class="text-center mt-8 flex justify-center items-center gap-1" >
        <p class="text-gray-600 text-sm mb-2 relative top-1 md:text-lg">Powered by:</p>
        <img :src="logofooter" alt="Similtech" class="h-7 md:h-10">
      </div>
    </div>

    <!-- Componente de carga -->
    <Cargando v-if="option === 1" />
  </div>
</template>
