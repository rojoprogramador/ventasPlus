<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Gestión de Clientes</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="flex justify-between mb-6">
              <h3 class="text-lg font-semibold">Listado de Clientes</h3>
              <button 
                @click="mostrarModalNuevoCliente = true"
                class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600"
              >
                Nuevo Cliente
              </button>
            </div>

            <!-- Búsqueda -->
            <div class="mb-4">
              <input 
                v-model="busqueda"
                type="text"
                class="w-full px-4 py-2 border rounded"
                placeholder="Buscar por nombre, documento o email..."
                @input="buscarClientes"
              >
            </div>

            <!-- Tabla de clientes -->
            <div class="overflow-x-auto">
              <table class="min-w-full bg-white border border-gray-200">
                <thead>
                  <tr>
                    <th class="px-4 py-2 text-left bg-gray-100 border-b">ID</th>
                    <th class="px-4 py-2 text-left bg-gray-100 border-b">Nombre</th>
                    <th class="px-4 py-2 text-left bg-gray-100 border-b">Documento</th>
                    <th class="px-4 py-2 text-left bg-gray-100 border-b">Email</th>
                    <th class="px-4 py-2 text-left bg-gray-100 border-b">Teléfono</th>
                    <th class="px-4 py-2 text-left bg-gray-100 border-b">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="cliente in clientes.data" :key="cliente.id" class="hover:bg-gray-50">
                    <td class="px-4 py-2 border-b">{{ cliente.id }}</td>
                    <td class="px-4 py-2 border-b">{{ cliente.nombre }}</td>
                    <td class="px-4 py-2 border-b">{{ cliente.documento }}</td>
                    <td class="px-4 py-2 border-b">{{ cliente.email }}</td>
                    <td class="px-4 py-2 border-b">{{ cliente.telefono }}</td>
                    <td class="px-4 py-2 border-b">
                      <div class="flex space-x-2">
                        <button 
                          @click="editarCliente(cliente)"
                          class="text-blue-600 hover:text-blue-800"
                        >
                          Editar
                        </button>
                        <button 
                          @click="confirmarEliminar(cliente)"
                          class="text-red-600 hover:text-red-800"
                        >
                          Eliminar
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              
              <!-- Paginación -->
              <nav v-if="clientes.links.length > 3" class="flex justify-center mt-4">
                <ul class="flex space-x-2">
                  <li v-for="(link, i) in clientes.links" :key="i">
                    <a 
                      :href="link.url" 
                      v-html="link.label"
                      :class="[
                        'px-3 py-1 rounded',
                        link.active ? 'bg-blue-500 text-white' : 'text-blue-500 hover:bg-blue-100',
                      ]"
                    ></a>
                  </li>
                </ul>
              </nav>
            </div>

            <!-- Modal Nuevo/Editar Cliente -->
            <div v-if="mostrarModalNuevoCliente || mostrarModalEditarCliente" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
              <div class="w-full max-w-lg p-6 bg-white rounded-lg">
                <h3 class="mb-4 text-lg font-bold">
                  {{ mostrarModalEditarCliente ? 'Editar Cliente' : 'Nuevo Cliente' }}
                </h3>
                
                <form @submit.prevent="guardarCliente">
                  <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                      <label class="block mb-1 font-medium">Nombre</label>
                      <input 
                        v-model="clienteForm.nombre"
                        type="text"
                        class="w-full px-3 py-2 border rounded"
                        required
                      >
                    </div>
                    
                    <div>
                      <label class="block mb-1 font-medium">Documento</label>
                      <input 
                        v-model="clienteForm.documento"
                        type="text"
                        class="w-full px-3 py-2 border rounded"
                      >
                    </div>
                    
                    <div>
                      <label class="block mb-1 font-medium">Email</label>
                      <input 
                        v-model="clienteForm.email"
                        type="email"
                        class="w-full px-3 py-2 border rounded"
                      >
                    </div>
                    
                    <div>
                      <label class="block mb-1 font-medium">Teléfono</label>
                      <input 
                        v-model="clienteForm.telefono"
                        type="text"
                        class="w-full px-3 py-2 border rounded"
                      >
                    </div>
                    
                    <div class="col-span-2">
                      <label class="block mb-1 font-medium">Dirección</label>
                      <input 
                        v-model="clienteForm.direccion"
                        type="text"
                        class="w-full px-3 py-2 border rounded"
                      >
                    </div>
                  </div>
                  
                  <div class="flex justify-end gap-4">
                    <button 
                      type="button"
                      @click="cerrarModal"
                      class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300"
                    >
                      Cancelar
                    </button>
                    <button 
                      type="submit"
                      class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600"
                    >
                      {{ mostrarModalEditarCliente ? 'Guardar cambios' : 'Crear cliente' }}
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Modal Confirmar Eliminación -->
            <div v-if="mostrarModalEliminar" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
              <div class="w-full max-w-md p-6 bg-white rounded-lg">
                <h3 class="mb-4 text-lg font-bold">Confirmar eliminación</h3>
                <p>¿Está seguro que desea eliminar al cliente {{ clienteAEliminar?.nombre }}?</p>
                
                <div class="flex justify-end gap-4 mt-6">
                  <button 
                    @click="mostrarModalEliminar = false"
                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300"
                  >
                    Cancelar
                  </button>
                  <button 
                    @click="eliminarCliente"
                    class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-600"
                  >
                    Eliminar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import debounce from 'lodash/debounce';

defineProps({
  clientes: {
    type: Object,
    required: true
  }
});

const busqueda = ref('');
const mostrarModalNuevoCliente = ref(false);
const mostrarModalEditarCliente = ref(false);
const mostrarModalEliminar = ref(false);
const clienteAEliminar = ref(null);
const clienteForm = ref({
  nombre: '',
  documento: '',
  email: '',
  telefono: '',
  direccion: ''
});

const buscarClientes = debounce(() => {
  router.get(route('clientes.index'), { busqueda: busqueda.value }, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  });
}, 300);

const editarCliente = (cliente) => {
  clienteForm.value = { ...cliente };
  mostrarModalEditarCliente.value = true;
};

const confirmarEliminar = (cliente) => {
  clienteAEliminar.value = cliente;
  mostrarModalEliminar.value = true;
};

const eliminarCliente = () => {
  if (clienteAEliminar.value) {
    router.delete(route('clientes.destroy', clienteAEliminar.value.id));
    mostrarModalEliminar.value = false;
  }
};

const guardarCliente = () => {
  if (mostrarModalEditarCliente.value) {
    router.put(route('clientes.update', clienteForm.value.id), clienteForm.value);
  } else {
    router.post(route('clientes.store'), clienteForm.value);
  }
  cerrarModal();
};

const cerrarModal = () => {
  mostrarModalNuevoCliente.value = false;
  mostrarModalEditarCliente.value = false;
  clienteForm.value = {
    nombre: '',
    documento: '',
    email: '',
    telefono: '',
    direccion: ''
  };
};
</script>
