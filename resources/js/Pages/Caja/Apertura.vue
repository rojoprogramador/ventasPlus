<template>
  <div class="container mx-auto py-6 px-4">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Apertura de Caja</h1>
      <p class="text-gray-600 mt-1">Fecha: {{ fechaActual }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
      <form @submit.prevent="realizarApertura">
        <div class="mb-6">
          <label for="monto_inicial" class="block text-sm font-medium text-gray-700 mb-1">
            Monto Inicial (Efectivo en caja)
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <span class="text-gray-500 sm:text-sm">$</span>
            </div>
            <input
              v-model="form.monto_inicial"
              type="number"
              name="monto_inicial"
              id="monto_inicial"
              class="block w-full pl-7 pr-12 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              placeholder="0.00"
              step="0.01"
              min="0"
              required
            />
          </div>
          <div v-if="errors && errors.monto_inicial" class="text-red-500 text-sm mt-1">
            {{ errors.monto_inicial }}
          </div>
        </div>

        <div class="mb-6">
          <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">
            Observaciones (Opcional)
          </label>
          <textarea
            v-model="form.observaciones"
            name="observaciones"
            id="observaciones"
            rows="3"
            class="block w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="Observaciones adicionales sobre la apertura de caja"
          ></textarea>
          <div v-if="errors && errors.observaciones" class="text-red-500 text-sm mt-1">
            {{ errors.observaciones }}
          </div>
        </div>

        <div class="flex justify-between">
          <Link
            :href="route('caja.index')"
            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
          >
            Cancelar
          </Link>
          <button
            type="submit"
            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            :disabled="processing"
          >
            {{ processing ? 'Abriendo caja...' : 'Abrir Caja' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

// Props
const props = defineProps({
  usuario: Object,
  fechaActual: String,
  errors: Object
})

// Estado del formulario
const form = useForm({
  monto_inicial: '',
  observaciones: ''
})

const processing = ref(false)

// Métodos
const realizarApertura = () => {
  processing.value = true
  form.post(route('caja.store'), {
    onSuccess: () => {
      processing.value = false
    },
    onError: () => {
      processing.value = false
    }
  })
}
</script>
