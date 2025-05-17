<template>
  <div class="container mx-auto py-6 px-4">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Cierre de Caja</h1>
      <p class="text-gray-600 mt-1">Fecha: {{ fechaActual }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Panel de resumen -->
      <div class="col-span-2">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 class="text-xl font-semibold mb-4">Resumen de Ventas</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
              <p class="text-sm text-gray-600 mb-1">Ventas en Efectivo</p>
              <p class="text-xl font-bold">${{ formatPrice(totalEfectivo) }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border border-green-100">
              <p class="text-sm text-gray-600 mb-1">Ventas con Tarjeta</p>
              <p class="text-xl font-bold">${{ formatPrice(totalTarjeta) }}</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
              <p class="text-sm text-gray-600 mb-1">Ventas por Transferencia</p>
              <p class="text-xl font-bold">${{ formatPrice(totalTransferencia) }}</p>
            </div>
          </div>
          
          <div class="border-t pt-4">
            <h3 class="font-semibold mb-3">Movimientos de Caja</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                <p class="text-sm text-gray-600 mb-1">Monto Inicial</p>
                <p class="text-xl font-bold">${{ formatPrice(caja && caja.monto_inicial ? caja.monto_inicial : 0) }}</p>
              </div>
              <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                <p class="text-sm text-gray-600 mb-1">Entradas Adicionales</p>
                <p class="text-xl font-bold">${{ formatPrice(totalEntradas) }}</p>
              </div>
              <div class="bg-red-50 p-4 rounded-lg border border-red-100">
                <p class="text-sm text-gray-600 mb-1">Salidas</p>
                <p class="text-xl font-bold">${{ formatPrice(totalSalidas) }}</p>
              </div>
              <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                <p class="text-sm text-gray-600 mb-1">Saldo Esperado en Efectivo</p>
                <p class="text-xl font-bold">${{ formatPrice(saldoEsperado) }}</p>
              </div>
            </div>
          </div>
          
          <div class="border-t pt-4">
            <h3 class="font-semibold mb-3">Total de Ventas</h3>
            <p class="text-2xl font-bold text-blue-700">${{ formatPrice(totalVentas) }}</p>
          </div>
        </div>
        
        <!-- Lista de ventas -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">Detalle de Ventas</h2>
          
          <div v-if="ventas.length === 0" class="text-center text-gray-500 py-4">
            No hay ventas registradas para este período.
          </div>
          
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método Pago</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="venta in ventas" :key="venta.id" class="hover:bg-gray-50">
                  <td class="px-4 py-2">{{ venta.id }}</td>
                  <td class="px-4 py-2">{{ formatTime(venta.created_at) }}</td>
                  <td class="px-4 py-2">{{ venta.cliente?.nombre || 'Cliente general' }}</td>
                  <td class="px-4 py-2">
                    <span :class="{
                      'px-2 py-1 rounded text-xs font-medium': true,
                      'bg-blue-100 text-blue-800': venta.metodo_pago === 'efectivo',
                      'bg-green-100 text-green-800': venta.metodo_pago === 'tarjeta',
                      'bg-purple-100 text-purple-800': venta.metodo_pago === 'transferencia'
                    }">
                      {{ formatMetodoPago(venta.metodo_pago) }}
                    </span>
                  </td>
                  <td class="px-4 py-2 font-medium">${{ formatPrice(venta.total) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
      <!-- Formulario de cierre -->
      <div class="col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
          <h2 class="text-xl font-semibold mb-4">Finalizar Cierre de Caja</h2>
          
          <form @submit.prevent="realizarCierre">
            <div class="mb-4">
              <label for="monto_final" class="block text-sm font-medium text-gray-700 mb-1">
                Monto Final en Efectivo
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="text-gray-500 sm:text-sm">$</span>
                </div>
                <input
                  v-model="form.monto_final"
                  type="number"
                  name="monto_final"
                  id="monto_final"
                  class="block w-full pl-7 pr-12 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="0.00"
                  step="0.01"
                  min="0"
                  required
                  @input="calcularDiferencia"
                />
              </div>
              <div v-if="form.errors.monto_final" class="text-red-500 text-sm mt-1">
                {{ form.errors.monto_final }}
              </div>
            </div>
            
            <div class="mb-4">
              <p class="text-sm text-gray-600 mb-1">Saldo Esperado en Efectivo</p>
              <p class="font-bold">${{ formatPrice(saldoEsperado) }}</p>
            </div>
            
            <div class="mb-4">
              <p class="text-sm font-medium mb-1" :class="diferencia === 0 ? 'text-green-600' : 'text-red-600'">
                Diferencia
              </p>
              <p class="font-bold text-lg" :class="diferencia === 0 ? 'text-green-600' : 'text-red-600'">
                ${{ formatPrice(diferencia) }}
              </p>
              <p v-if="diferencia > 0" class="text-xs text-green-600 mt-1">
                Hay un sobrante de efectivo
              </p>
              <p v-if="diferencia < 0" class="text-xs text-red-600 mt-1">
                Hay un faltante de efectivo
              </p>
            </div>
            
            <div v-if="diferencia !== 0" class="mb-4">
              <label for="justificacion_diferencia" class="block text-sm font-medium text-gray-700 mb-1">
                Justificación de la Diferencia
              </label>
              <textarea
                v-model="form.justificacion_diferencia"
                name="justificacion_diferencia"
                id="justificacion_diferencia"
                rows="3"
                class="block w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Explique la razón de la diferencia entre el monto esperado y el monto real"
                required
              ></textarea>
              <div v-if="form.errors.justificacion_diferencia" class="text-red-500 text-sm mt-1">
                {{ form.errors.justificacion_diferencia }}
              </div>
            </div>
            
            <div class="mb-4">
              <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">
                Observaciones (Opcional)
              </label>
              <textarea
                v-model="form.observaciones"
                name="observaciones"
                id="observaciones"
                rows="2"
                class="block w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Observaciones adicionales sobre el cierre"
              ></textarea>
              <div v-if="form.errors.observaciones" class="text-red-500 text-sm mt-1">
                {{ form.errors.observaciones }}
              </div>
            </div>
            
            <div class="flex justify-between mt-6">
              <Link
                :href="route('caja.index')"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              >
                Cancelar
              </Link>
              <button
                type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                :disabled="processing"
              >
                {{ processing ? 'Cerrando caja...' : 'Finalizar Cierre' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

// Props con valores por defecto para evitar undefined
const props = defineProps({
  caja: {
    type: Object,
    default: () => ({
      id: null,
      monto_inicial: 0,
      monto_final: 0,
      total_ventas: 0
    })
  },
  ventas: {
    type: Array,
    default: () => []
  },
  totalEfectivo: {
    type: Number,
    default: 0
  },
  totalTarjeta: {
    type: Number,
    default: 0
  },
  totalTransferencia: {
    type: Number,
    default: 0
  },
  totalVentas: {
    type: Number,
    default: 0
  },
  totalEntradas: {
    type: Number,
    default: 0
  },
  totalSalidas: {
    type: Number,
    default: 0
  },
  saldoEsperado: {
    type: Number,
    default: 0
  },
  usuario: {
    type: Object,
    default: () => ({})
  },
  fechaActual: {
    type: String,
    default: ''
  }
})

// Estado del formulario
const form = useForm({
  monto_final: '',
  observaciones: '',
  justificacion_diferencia: '',
  hay_diferencia: false
})

const processing = ref(false)
const diferencia = ref(0)

// Métodos
const formatPrice = (price) => {
  return Number(price).toFixed(2)
}

const formatTime = (dateString) => {
  if (!dateString) return '--:--'
  
  const date = new Date(dateString)
  return date.toLocaleTimeString('es-MX', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatMetodoPago = (metodo) => {
  const metodos = {
    'efectivo': 'Efectivo',
    'tarjeta': 'Tarjeta',
    'transferencia': 'Transferencia'
  }
  return metodos[metodo] || metodo
}

const calcularDiferencia = () => {
  const montoFinal = parseFloat(form.monto_final) || 0
  diferencia.value = montoFinal - props.saldoEsperado
  form.hay_diferencia = diferencia.value !== 0
}

const realizarCierre = () => {
  // Asegurarse de que la diferencia esté calculada
  calcularDiferencia()
  
  processing.value = true
  // Verificar si hay una caja válida y mostrar un mensaje amigable al usuario
  if (!props.caja || !props.caja.id) {
    console.error('No hay una caja válida para cerrar');
    processing.value = false;
    alert('No se encontró una caja abierta para cerrar. Por favor, verifique el estado de la caja.');
    window.location.href = route('caja.index');
    return;
  }
  
  form.put(route('caja.update', props.caja.id), {
    onSuccess: () => {
      processing.value = false
    },
    onError: () => {
      processing.value = false
    }
  })
}
</script>
