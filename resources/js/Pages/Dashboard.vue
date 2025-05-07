<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    auth: Object
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Panel de Control</h3>
                        
                        <!-- Enlaces de administración -->
                        <div v-if="auth.user.rol.nombre === 'admin'" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <Link 
                                href="/roles"
                                class="flex items-center p-4 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors">
                                <div>
                                    <h4 class="font-semibold">Gestión de Roles</h4>
                                    <p class="text-sm">Administrar roles y permisos del sistema</p>
                                </div>
                            </Link>
                            
                            <Link 
                                href="/users"
                                class="flex items-center p-4 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                                <div>
                                    <h4 class="font-semibold">Gestión de Usuarios</h4>
                                    <p class="text-sm">Administrar usuarios del sistema</p>
                                </div>
                            </Link>
                        </div>

                        <!-- Mensaje de bienvenida -->
                        <p>Bienvenido al sistema de ventas VentaPlus</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
