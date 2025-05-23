<template>
    <AppLayout title="Productos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Productos</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between mb-6">
                            <h3 class="text-lg font-medium text-gray-900">Lista de Productos</h3>
                        </div>

                        <div class="overflow-x-auto bg-white rounded shadow">
                            <table class="w-full whitespace-nowrap">
                                <thead>
                                    <tr class="text-left font-bold">
                                        <th class="px-6 pt-5 pb-4">Código</th>
                                        <th class="px-6 pt-5 pb-4">Nombre</th>
                                        <th class="px-6 pt-5 pb-4">Descripción</th>
                                        <th class="px-6 pt-5 pb-4">Precio</th>
                                        <th class="px-6 pt-5 pb-4">Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="productos.length === 0">
                                        <td colspan="5" class="px-6 py-4 border-t text-center">
                                            No hay productos registrados.
                                        </td>
                                    </tr>
                                    <tr v-for="producto in productos" :key="producto.id" class="hover:bg-gray-100">
                                        <td class="border-t px-6 py-4">{{ producto.codigo }}</td>
                                        <td class="border-t px-6 py-4">{{ producto.nombre }}</td>
                                        <td class="border-t px-6 py-4">{{ producto.descripcion }}</td>
                                        <td class="border-t px-6 py-4">{{ formatCurrency(producto.precio_venta) }}</td>
                                        <td class="border-t px-6 py-4">{{ producto.stock }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

// Productos - están disponibles a través de props
const props = defineProps({
    productos: {
        type: Array,
        default: () => []
    }
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
    }).format(value);
};
</script>