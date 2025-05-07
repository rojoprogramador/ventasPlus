<template>
  <div class="p-6">
    <div class="mb-6">
      <h2 class="text-2xl font-bold">Gestión de Roles</h2>
    </div>

    <!-- Formulario para crear/editar rol -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
      <form @submit.prevent="form.id ? updateRol() : createRol()">
        <div class="grid grid-cols-1 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" v-model="form.nombre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Descripción</label>
            <input type="text" v-model="form.descripcion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Permisos</label>
            <div class="mt-2 grid grid-cols-3 gap-2">
              <div v-for="permiso in permisos" :key="permiso.id" class="flex items-center">
                <input type="checkbox" 
                       :value="permiso.id" 
                       v-model="form.permisos"
                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                <label class="ml-2 text-sm text-gray-600">{{ permiso.nombre }}</label>
              </div>
            </div>
          </div>
          <div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
              {{ form.id ? 'Actualizar' : 'Crear' }} Rol
            </button>
            <button v-if="form.id" 
                    type="button" 
                    @click="resetForm" 
                    class="ml-2 bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
              Cancelar
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- Lista de roles -->
    <div class="bg-white rounded-lg shadow">
      <table class="min-w-full divide-y divide-gray-200">
        <thead>
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permisos</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="rol in roles" :key="rol.id">
            <td class="px-6 py-4 whitespace-nowrap">{{ rol.nombre }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ rol.descripcion }}</td>
            <td class="px-6 py-4">
              <span v-for="permiso in rol.permisos" 
                    :key="permiso.id" 
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                {{ permiso.nombre }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <button @click="editRol(rol)" class="text-indigo-600 hover:text-indigo-900">Editar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import { router } from '@inertiajs/vue3'

export default defineComponent({
  props: {
    roles: Array,
    permisos: Array
  },
  data() {
    return {
      form: {
        id: null,
        nombre: '',
        descripcion: '',
        permisos: []
      }
    }
  },
  methods: {
    createRol() {
      router.post('/roles', this.form)
    },
    updateRol() {
      router.put(`/roles/${this.form.id}`, this.form)
    },
    editRol(rol) {
      this.form = {
        id: rol.id,
        nombre: rol.nombre,
        descripcion: rol.descripcion,
        permisos: rol.permisos.map(p => p.id)
      }
    },
    resetForm() {
      this.form = {
        id: null,
        nombre: '',
        descripcion: '',
        permisos: []
      }
    }
  }
})
</script>
