<template>
  <div class="container mx-auto py-6 px-4">
    <div class="mb-6 flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-800">Gestión de Caja</h1>
      <div>
        <button 
          v-if="!cajaAbierta" 
          @click="abrirCaja" 
          class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
        >
          Abrir Caja
        </button>
        <button 
          v-if="cajaAbierta && tienePermiso('gestionar_caja')" 
          @click="cerrarCaja" 
          class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 ml-2"
        >
          Cerrar Caja
        </button>
      </div>
    </div>

    <!-- Estado actual de la caja -->
    <div v-if="cajaAbierta" class="bg-white rounded-lg shadow-md mb-6 p-6">
      <h2 class="text-xl font-semibold mb-4">Caja Abierta</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="border p-4 rounded-lg bg-gray-50">
          <p class="text-gray-600 text-sm">Responsable</p>
          <p class="font-semibold">{{ cajaAbierta.usuario?.name || 'No disponible' }}</p>
        </div>
        <div class="border p-4 rounded-lg bg-gray-50">
          <p class="text-gray-600 text-sm">Fecha de Apertura</p>
          <p class="font-semibold">{{ formatDate(cajaAbierta.fecha_apertura) }}</p>
        </div>
        <div class="border p-4 rounded-lg bg-gray-50">
          <p class="text-gray-600 text-sm">Monto Inicial</p>
          <p class="font-semibold">${{ formatPrice(cajaAbierta.monto_inicial) }}</p>
        </div>
        <div class="border p-4 rounded-lg bg-gray-50">
          <p class="text-gray-600 text-sm">Estado</p>
          <p class="inline-block px-2 py-1 rounded bg-green-100 text-green-800 font-medium text-sm">
            {{ cajaAbierta.estado }}
          </p>
        </div>
      </div>
      
      <!-- Registrar movimientos de caja -->
      <div class="mt-6 border-t pt-6">
        <h3 class="text-lg font-semibold mb-4">Registrar Movimiento</h3>
        <form @submit.prevent="registrarMovimiento" class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
            <select 
              v-model="movimiento.tipo" 
              id="tipo" 
              class="w-full px-3 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
              required
            >
              <option value="entrada">Entrada</option>
              <option value="salida">Salida</option>
            </select>
          </div>
          <div>
            <label for="monto" class="block text-sm font-medium text-gray-700 mb-1">Monto</label>
            <input 
              v-model="movimiento.monto" 
              type="number" 
              id="monto" 
              step="0.01" 
              min="0.01" 
              class="w-full px-3 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
              required
            />
          </div>
          <div>
            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <input 
              v-model="movimiento.descripcion" 
              type="text" 
              id="descripcion" 
              class="w-full px-3 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
              required
            />
          </div>
          <div class="flex items-end">
            <button 
              type="submit" 
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 w-full"
              :disabled="registrandoMovimiento"
            >
              {{ registrandoMovimiento ? 'Registrando...' : 'Registrar Movimiento' }}
            </button>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Historial de cajas cerradas -->
    <div class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-xl font-semibold mb-4">Últimas Cajas Cerradas</h2>
      <div v-if="cajasCerradas.length === 0" class="py-4 text-center text-gray-500">
        No hay cajas cerradas en el historial.
      </div>
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Responsable
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Fecha de Apertura
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Fecha de Cierre
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Monto Inicial
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Monto Final
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Ventas
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="caja in cajasCerradas" :key="caja.id">
              <td class="px-6 py-4 whitespace-nowrap">
                {{ caja.usuario?.name || 'No disponible' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ formatDate(caja.fecha_apertura) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ formatDate(caja.fecha_cierre) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                ${{ formatPrice(caja.monto_inicial) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                ${{ formatPrice(caja.monto_final) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                ${{ formatPrice(caja.total_ventas) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <div class="flex space-x-2">
                  <Link :href="route('caja.show', caja.id ? caja.id : 0)" class="text-indigo-600 hover:text-indigo-900">
                    Ver Detalles
                  </Link>
                  <button
                    v-if="tienePermiso('administrar_sistema')"
                    @click="reabrirCaja(caja.id)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Reabrir
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'

// Props
const props = defineProps({
  cajaAbierta: Object,
  cajasCerradas: Array,
  usuario: Object,
  fechaActual: String
})

// Estado local
const movimiento = ref({
  tipo: 'entrada',
  monto: '',
  descripcion: '',
})
const registrandoMovimiento = ref(false)

// Métodos
const formatDate = (dateString) => {
  if (!dateString) return 'No disponible'
  
  const date = new Date(dateString)
  return date.toLocaleString('es-MX', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatPrice = (price) => {
  return Number(price).toFixed(2)
}

const tienePermiso = (permiso) => {
  // Si el usuario es admin, siempre tiene permiso
  if (props.usuario?.rol?.nombre?.toLowerCase() === 'admin') {
    return true;
  }
  
  // Si tiene el rol con permisos específicos
  const permisos = props.usuario?.rol?.permisos || []
  if (Array.isArray(permisos)) {
    // Si permisos es un array de objetos, buscar por la propiedad nombre
    if (permisos.length > 0 && typeof permisos[0] === 'object') {
      return permisos.some(p => p.nombre === permiso);
    }
    // Si permisos es un array de strings
    return permisos.includes(permiso);
  }
  
  return false;
}

const abrirCaja = () => {
  router.visit(route('caja.create'))
}

const cerrarCaja = () => {
  if (props.cajaAbierta) {
    if (props.cajaAbierta && props.cajaAbierta.id) {
      router.visit(route('caja.edit', props.cajaAbierta.id))
    } else {
      console.error('No hay una caja abierta o no tiene ID válido')
    }
  }
}

const reabrirCaja = (id) => {
  if (confirm('¿Está seguro de reabrir esta caja? Esta acción quedará registrada en el historial.')) {
    router.post(route('caja.reabrir', id ? id : 0), {}, {
      onSuccess: () => {
        // El redirect lo maneja el controlador
      }
    })
  }
}

const registrarMovimiento = () => {
  if (!props.cajaAbierta) return
  
  registrandoMovimiento.value = true
  
  router.post(route('caja.movimiento'), {
    caja_id: props.cajaAbierta.id,
    tipo: movimiento.value.tipo,
    monto: movimiento.value.monto,
    descripcion: movimiento.value.descripcion,
  }, {
    onSuccess: () => {
      // Limpiar el formulario
      movimiento.value = {
        tipo: 'entrada',
        monto: '',
        descripcion: '',
      }
      registrandoMovimiento.value = false
    },
    onError: () => {
      registrandoMovimiento.value = false
    }
  })
}
</script>
