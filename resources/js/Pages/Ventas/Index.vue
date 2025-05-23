<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Gestión de Ventas</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="flex justify-between mb-6">
              <h3 class="text-lg font-semibold">Listado de Ventas</h3>
              <Link :href="route('ventas.registro')" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">
                Nueva Venta
              </Link>
            </div>

            <div v-if="ventas.data.length > 0" class="overflow-x-auto">
              <table class="min-w-full bg-white border border-gray-200">
                <thead>
                  <tr>
                    <th class="px-4 py-2 text-left bg-gray-100 border-b">ID</th>
                    <th class="px-4 py-2 text-left bg-gray-100 border-b">Fecha</th>
                    <th class="px-4 py-2 text-left bg-gray-100 border-b">Cliente</th>
                    <th class="px-4 py-2 text-left bg-gray-100 border-b">Total</th>
                    <th class="px-4 py-2 text-left bg-gray-100 border-b">Estado</th>
                    <th class="px-4 py-2 text-left bg-gray-100 border-b">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="venta in ventas.data" :key="venta.id" class="hover:bg-gray-50">
                    <td class="px-4 py-2 border-b">{{ venta.id }}</td>
                    <td class="px-4 py-2 border-b">{{ formatDate(venta.created_at) }}</td>
                    <td class="px-4 py-2 border-b">{{ venta.cliente ? venta.cliente.nombre : 'Cliente no registrado' }}</td>
                    <td class="px-4 py-2 border-b">${{ venta.total.toFixed(2) }}</td>
                    <td class="px-4 py-2 border-b">
                      <span 
                        :class="{
                          'px-2 py-1 text-xs font-semibold rounded': true,
                          'bg-green-100 text-green-800': venta.estado === 'completada',
                          'bg-red-100 text-red-800': venta.estado === 'cancelada',
                          'bg-yellow-100 text-yellow-800': venta.estado === 'pendiente'
                        }"
                      >
                        {{ venta.estado }}
                      </span>
                    </td>
                    <td class="px-4 py-2 border-b">
                      <div class="flex space-x-2">
                        <Link :href="route('ventas.comprobante', venta.id)" class="text-blue-600 hover:text-blue-800">
                          Ver comprobante
                        </Link>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-else class="p-4 text-center bg-gray-50">
              No hay ventas registradas.
            </div>

            <!-- Paginación -->
            <div v-if="ventas.data.length > 0" class="mt-4">
              <Pagination :links="ventas.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';

// Definir props
const props = defineProps({
  ventas: Object
});

// Función para formatear fechas
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};
</script>
