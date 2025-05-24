<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Comprobante de Venta</h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            
            <!-- Información de la empresa -->
            <div class="text-center mb-6">
              <h1 class="text-xl font-bold">VentaPlus - Sistema POS</h1>
              <p>Comprobante de Venta</p>
              <p>NIT: 123456789-0</p>
              <p>Tel: (123) 456-7890</p>
              <p>Dirección: Calle Principal #123</p>
            </div>
            
            <!-- Información de la venta -->
            <div class="mb-6">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <p><strong>Código:</strong> {{ venta.codigo }}</p>
                  <p><strong>Fecha:</strong> {{ formatFecha(venta.fecha) }}</p>
                  <p><strong>Cliente:</strong> {{ venta.cliente ? venta.cliente.nombre : 'Cliente general' }}</p>
                </div>
                <div class="text-right">
                  <p><strong>Cajero:</strong> {{ venta.usuario ? venta.usuario.name : 'Usuario del sistema' }}</p>
                  <p><strong>Método de pago:</strong> {{ formatTipoPago(venta.tipo_pago) }}</p>
                </div>
              </div>
            </div>
            
            <!-- Detalle de productos -->
            <div class="mb-6">
              <h3 class="mb-3 text-lg font-semibold">Productos</h3>
              
              <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="px-4 py-2 text-left">Producto</th>
                      <th class="px-4 py-2 text-center">Cantidad</th>
                      <th class="px-4 py-2 text-right">Precio Unit.</th>
                      <th class="px-4 py-2 text-right">Descuento</th>
                      <th class="px-4 py-2 text-right">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="detalle in venta.detalles" :key="detalle.id" class="border-b">
                      <td class="px-4 py-2">{{ detalle.producto ? detalle.producto.nombre : 'Producto no disponible' }}</td>
                      <td class="px-4 py-2 text-center">{{ detalle.cantidad }}</td>
                      <td class="px-4 py-2 text-right">${{ formatNumero(detalle.precio) }}</td>
                      <td class="px-4 py-2 text-right">
                        <template v-if="detalle.descuento > 0">
                          {{ detalle.tipo_descuento === 'porcentaje' ? `${detalle.descuento}%` : `$${formatNumero(detalle.descuento)}` }}
                        </template>
                        <template v-else>-</template>
                      </td>
                      <td class="px-4 py-2 text-right">${{ formatNumero(detalle.subtotal) }}</td>
                    </tr>
                  </tbody>
                  <tfoot class="bg-gray-50">
                    <tr>
                      <td colspan="4" class="px-4 py-2 text-right font-bold">Subtotal sin descuentos:</td>
                      <td class="px-4 py-2 text-right">${{ formatNumero(venta.subtotal + (venta.descuentos || 0)) }}</td>
                    </tr>
                    <tr v-if="venta.descuentos > 0">
                      <td colspan="4" class="px-4 py-2 text-right font-bold text-red-600">Descuentos totales:</td>
                      <td class="px-4 py-2 text-right text-red-600">-${{ formatNumero(venta.descuentos) }}</td>
                    </tr>
                    <tr>
                      <td colspan="4" class="px-4 py-2 text-right font-bold text-lg">Total Final:</td>
                      <td class="px-4 py-2 text-right font-bold text-lg">${{ formatNumero(venta.total) }}</td>
                    </tr>
                    <!-- Mostrar información del pago en efectivo si aplica -->
                    <tr v-if="venta.tipo_pago === 'efectivo' && venta.monto_recibido">
                      <td colspan="4" class="px-4 py-2 text-right">Monto recibido:</td>
                      <td class="px-4 py-2 text-right">${{ formatNumero(venta.monto_recibido) }}</td>
                    </tr>
                    <tr v-if="venta.tipo_pago === 'efectivo' && venta.monto_recibido">
                      <td colspan="4" class="px-4 py-2 text-right">Cambio:</td>
                      <td class="px-4 py-2 text-right">${{ formatNumero(venta.monto_recibido - venta.total) }}</td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            
            <!-- Mensaje de agradecimiento -->
            <div class="text-center mt-8 mb-4">
              <p>¡Gracias por su compra!</p>
              <p class="text-sm text-gray-500">Este documento sirve como comprobante de su transacción</p>
            </div>
            
            <!-- Botones de acción -->
            <div class="flex justify-center gap-4 mt-8">
              <button 
                @click="imprimir" 
                class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-600 focus:outline-none focus:shadow-outline"
              >
                Imprimir
              </button>
              
              <button 
                @click="enviarComprobante" 
                class="px-4 py-2 font-bold text-white bg-green-500 rounded hover:bg-green-600 focus:outline-none focus:shadow-outline"
              >
                Enviar por Email
              </button>
              
              <button 
                @click="nuevaVenta" 
                class="px-4 py-2 font-bold text-white bg-indigo-500 rounded hover:bg-indigo-600 focus:outline-none focus:shadow-outline"
              >
                Nueva Venta
              </button>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Propiedades recibidas
const props = defineProps({
  venta: Object
});

// Funciones de formato
const formatNumero = (valor) => {
  const numero = parseFloat(valor);
  return isNaN(numero) ? '0.00' : numero.toFixed(2);
};

const formatFecha = (fecha) => {
  if (!fecha) return '';
  return new Date(fecha).toLocaleString();
};

const formatTipoPago = (tipo) => {
  if (!tipo) return 'No especificado';
  const tipos = {
    efectivo: 'Efectivo',
    tarjeta: 'Tarjeta',
    transferencia: 'Transferencia'
  };
  return tipos[tipo.toLowerCase()] || tipo;
};

// Métodos
const imprimir = () => {
  window.print();
};

const enviarComprobante = () => {
  // Aquí se implementaría la lógica para enviar el comprobante por email
  alert('Funcionalidad de envío por email no implementada aún');
};

const nuevaVenta = () => {
  router.visit(route('ventas.registro'));
};
</script>

<style>
@media print {
  header, footer, button, .no-print {
    display: none !important;
  }
  
  body, html {
    width: 210mm;
    height: 297mm;
    margin: 0;
    padding: 0;
  }
  
  @page {
    size: A4;
    margin: 10mm;
  }
}
</style>
