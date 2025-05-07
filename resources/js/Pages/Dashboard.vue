<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    auth: {
        type: Object,
        required: true
    }
});

// Métodos para verificar permisos
const tienePermiso = (permiso) => {
    return props.auth?.user?.rol?.permisos?.some(p => p.nombre === permiso);
};

const tieneAlgunPermiso = (permisos) => {
    return props.auth?.user?.rol?.permisos?.some(p => permisos.includes(p.nombre));
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Panel de Administración</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Información del usuario -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-2">Bienvenido, {{ auth.user.name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Rol: {{ auth.user.rol.nombre }}</p>
                    </div>
                </div>

                <!-- Módulos del Sistema -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Administración -->
                    <div v-if="tieneAlgunPermiso(['gestion_usuarios', 'gestion_roles'])" 
                         class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Administración</h3>
                            <div class="space-y-3">
                                <Link v-if="tienePermiso('gestion_roles')"
                                      href="/roles"
                                      class="flex items-center p-3 bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800 transition-colors">
                                    <div>
                                        <h4 class="font-semibold">Roles</h4>
                                        <p class="text-sm">Gestionar roles y permisos</p>
                                    </div>
                                </Link>
                                <Link v-if="tienePermiso('gestion_usuarios')"
                                      href="/users"
                                      class="flex items-center p-3 bg-green-50 dark:bg-green-900 text-green-700 dark:text-green-200 rounded-lg hover:bg-green-100 dark:hover:bg-green-800 transition-colors">
                                    <div>
                                        <h4 class="font-semibold">Usuarios</h4>
                                        <p class="text-sm">Gestionar usuarios del sistema</p>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Ventas -->
                    <div v-if="tieneAlgunPermiso(['gestion_ventas', 'gestion_clientes'])" 
                         class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Ventas</h3>
                            <div class="space-y-3">
                                <Link v-if="tienePermiso('gestion_ventas')"
                                      href="/ventas"
                                      class="flex items-center p-3 bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-200 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-800 transition-colors">
                                    <div>
                                        <h4 class="font-semibold">Ventas</h4>
                                        <p class="text-sm">Gestionar ventas y facturas</p>
                                    </div>
                                </Link>
                                <Link v-if="tienePermiso('gestion_clientes')"
                                      href="/clientes"
                                      class="flex items-center p-3 bg-purple-50 dark:bg-purple-900 text-purple-700 dark:text-purple-200 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-800 transition-colors">
                                    <div>
                                        <h4 class="font-semibold">Clientes</h4>
                                        <p class="text-sm">Gestionar clientes</p>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Inventario -->
                    <div v-if="tienePermiso('gestion_productos')" 
                         class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Inventario</h3>
                            <div class="space-y-3">
                                <Link href="/productos"
                                      class="flex items-center p-3 bg-amber-50 dark:bg-amber-900 text-amber-700 dark:text-amber-200 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-800 transition-colors">
                                    <div>
                                        <h4 class="font-semibold">Productos</h4>
                                        <p class="text-sm">Gestionar productos e inventario</p>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
