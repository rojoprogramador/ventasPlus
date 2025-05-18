<template>
  <div class="container mx-auto py-6 px-4">
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Reporte de Cierre de Caja</h1>
        <p class="text-gray-600 mt-1">Caja #{{ caja.id }}</p>
      </div>
      <div class="flex space-x-3">
        <button
          @click="imprimirReporte"
          class="px-4 py-2 flex items-center bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
          </svg>
          Imprimir
        </button>
        <Link
          :href="route('dashboard')"
          class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 flex items-center mr-2"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
          </svg>
          Volver al Dashboard
        </Link>
        <Link
          :href="route('caja.index')"
          class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50"
        >
          Volver a Caja
        </Link>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6" id="reporteImprimir">
      <div class="text-center mb-6">
        <h2 class="text-xl font-bold">VentasPlus - Reporte de Cierre de Caja</h2>
        <p class="text-gray-600">{{ formatDate(caja.fecha_cierre) }}</p>
      </div>
      
      <!-- Información básica -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 border-b pb-6">
        <div>
          <p class="text-sm text-gray-600">Responsable:</p>
          <p class="font-semibold">{{ caja.usuario?.name || 'No disponible' }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Apertura:</p>
          <p class="font-semibold">{{ formatDate(caja.fecha_apertura) }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Cierre:</p>
          <p class="font-semibold">{{ formatDate(caja.fecha_cierre) }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Estado:</p>
          <p class="inline-block px-2 py-1 rounded bg-gray-100 text-gray-800 font-medium text-sm">
            {{ caja.estado }}
          </p>
        </div>
      </div>
      
      <!-- Resumen de ventas -->
      <div class="mb-6 border-b pb-6">
        <h3 class="text-lg font-semibold mb-4">Resumen de Ventas</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
        <div class="mt-4">
          <p class="font-semibold">Total de Ventas: <span class="font-bold text-lg">${{ formatPrice(totalVentas) }}</span></p>
        </div>
      </div>
      
      <!-- Movimientos de caja -->
      <div class="mb-6 border-b pb-6">
        <h3 class="text-lg font-semibold mb-4">Movimientos de Caja</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <p class="text-sm text-gray-600 mb-1">Monto Inicial</p>
            <p class="text-xl font-bold">${{ formatPrice(caja.monto_inicial) }}</p>
          </div>
          <div class="bg-green-50 p-4 rounded-lg border border-green-100">
            <p class="text-sm text-gray-600 mb-1">Entradas Adicionales</p>
            <p class="text-xl font-bold">${{ formatPrice(totalEntradas) }}</p>
          </div>
          <div class="bg-red-50 p-4 rounded-lg border border-red-100">
            <p class="text-sm text-gray-600 mb-1">Salidas</p>
            <p class="text-xl font-bold">${{ formatPrice(totalSalidas) }}</p>
          </div>
          <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <p class="text-sm text-gray-600 mb-1">Saldo Esperado</p>
            <p class="text-xl font-bold">${{ formatPrice(saldoEsperado) }}</p>
          </div>
        </div>
      </div>
      
      <!-- Resultados del cierre -->
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-4">Resultados del Cierre</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
            <p class="text-sm text-gray-600 mb-1">Monto Final Declarado</p>
            <p class="text-xl font-bold">${{ formatPrice(caja.monto_final) }}</p>
          </div>
          <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <p class="text-sm text-gray-600 mb-1">Saldo Esperado</p>
            <p class="text-xl font-bold">${{ formatPrice(saldoEsperado) }}</p>
          </div>
          <div :class="{
            'p-4 rounded-lg border': true,
            'bg-green-50 border-green-100': diferencia === 0,
            'bg-red-50 border-red-100': diferencia < 0,
            'bg-yellow-50 border-yellow-100': diferencia > 0
          }">
            <p class="text-sm text-gray-600 mb-1">Diferencia</p>
            <p class="text-xl font-bold" :class="{
              'text-green-600': diferencia === 0,
              'text-red-600': diferencia < 0,
              'text-yellow-600': diferencia > 0
            }">${{ formatPrice(diferencia) }}</p>
          </div>
        </div>
        
        <div v-if="diferencia !== 0 && movimientosDiferencia.length > 0" class="mt-4 p-4 bg-gray-50 rounded-lg">
          <p class="font-semibold">Justificación de la diferencia:</p>
          <p class="mt-1">{{ movimientosDiferencia[0].descripcion }}</p>
        </div>
        
        <div v-if="caja.observaciones" class="mt-4 p-4 bg-gray-50 rounded-lg">
          <p class="font-semibold">Observaciones:</p>
          <p class="mt-1">{{ caja.observaciones }}</p>
        </div>
      </div>
      
      <!-- Detalle de movimientos -->
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-4">Detalle de Movimientos</h3>
        
        <div v-if="movimientos.length === 0" class="text-center text-gray-500 py-4">
          No hay movimientos de caja registrados.
        </div>
        
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="movimiento in movimientos" :key="movimiento.id" class="hover:bg-gray-50">
                <td class="px-4 py-2">{{ formatDateTime(movimiento.fecha) }}</td>
                <td class="px-4 py-2">
                  <span :class="{
                    'px-2 py-1 rounded text-xs font-medium': true,
                    'bg-green-100 text-green-800': movimiento.tipo === 'entrada',
                    'bg-red-100 text-red-800': movimiento.tipo === 'salida',
                    'bg-yellow-100 text-yellow-800': movimiento.tipo === 'sobrante',
                    'bg-orange-100 text-orange-800': movimiento.tipo === 'faltante'
                  }">
                    {{ formatTipoMovimiento(movimiento.tipo) }}
                  </span>
                </td>
                <td class="px-4 py-2 font-medium">${{ formatPrice(movimiento.monto) }}</td>
                <td class="px-4 py-2">{{ movimiento.descripcion }}</td>
                <td class="px-4 py-2">{{ movimiento.usuario?.name || 'No disponible' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Lista de ventas -->
      <div>
        <h3 class="text-lg font-semibold mb-4">Detalle de Ventas</h3>
        
        <div v-if="ventas.length === 0" class="text-center text-gray-500 py-4">
          No hay ventas registradas para este período.
        </div>
        
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método Pago</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="venta in ventas" :key="venta.id" class="hover:bg-gray-50">
                <td class="px-4 py-2">{{ venta.id }}</td>
                <td class="px-4 py-2">{{ formatDateTime(venta.created_at) }}</td>
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
      
      <!-- Firma -->
      <div class="mt-8 pt-8 border-t">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="text-center">
            <div class="h-px bg-gray-300 my-8 mx-auto w-2/3"></div>
            <p class="font-medium">Firma del Cajero</p>
          </div>
          <div class="text-center">
            <div class="h-px bg-gray-300 my-8 mx-auto w-2/3"></div>
            <p class="font-medium">Firma del Administrador</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import html2canvas from 'html2canvas'
import jsPDF from 'jspdf'

// Props
const props = defineProps({
  caja: Object,
  ventas: Array,
  movimientos: Array,
  totalEfectivo: Number,
  totalTarjeta: Number,
  totalTransferencia: Number,
  totalVentas: Number,
  totalEntradas: Number,
  totalSalidas: Number,
  saldoEsperado: Number,
  diferencia: Number,
  usuario: Object
})

// Computados
const movimientosDiferencia = computed(() => {
  return props.movimientos.filter(m => m.tipo === 'sobrante' || m.tipo === 'faltante')
})

// Métodos
const formatPrice = (price) => {
  return Number(price).toFixed(2)
}

const formatDate = (dateString) => {
  if (!dateString) return 'No disponible'
  
  const date = new Date(dateString)
  return date.toLocaleDateString('es-MX', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  })
}

const formatDateTime = (dateString) => {
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

const formatMetodoPago = (metodo) => {
  const metodos = {
    'efectivo': 'Efectivo',
    'tarjeta': 'Tarjeta',
    'transferencia': 'Transferencia'
  }
  return metodos[metodo] || metodo
}

const formatTipoMovimiento = (tipo) => {
  const tipos = {
    'entrada': 'Entrada',
    'salida': 'Salida',
    'sobrante': 'Sobrante',
    'faltante': 'Faltante',
    'apertura': 'Apertura',
    'cierre': 'Cierre',
    'reapertura': 'Reapertura'
  }
  return tipos[tipo] || tipo
}

const imprimirReporte = () => {
  const element = document.getElementById('reporteImprimir')
  
  html2canvas(element).then(canvas => {
    const imgData = canvas.toDataURL('image/png')
    const pdf = new jsPDF('p', 'mm', 'a4')
    const imgProps = pdf.getImageProperties(imgData)
    const pdfWidth = pdf.internal.pageSize.getWidth()
    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width
    
    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight)
    pdf.save(`reporte_caja_${props.caja.id}.pdf`)
  })
}
</script>
