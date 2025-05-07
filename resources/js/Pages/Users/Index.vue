<template>
  <div class="p-6">
    <div class="mb-6">
      <h2 class="text-2xl font-bold">Gestión de Usuarios</h2>
    </div>

    <!-- Formulario para crear/editar usuario -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
      <form @submit.prevent="form.id ? updateUser() : createUser()">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" v-model="form.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" v-model="form.email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Contraseña</label>
            <input type="password" v-model="form.password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" :required="!form.id">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Rol</label>
            <select v-model="form.rol_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
              <option v-for="rol in roles" :key="rol.id" :value="rol.id">{{ rol.nombre }}</option>
            </select>
          </div>
          <div v-if="form.id">
            <label class="block text-sm font-medium text-gray-700">Estado</label>
            <select v-model="form.estado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
              <option :value="true">Activo</option>
              <option :value="false">Inactivo</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
              {{ form.id ? 'Actualizar' : 'Crear' }} Usuario
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

    <!-- Lista de usuarios -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead>
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ user.rol.nombre }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="user.estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ user.estado ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <button @click="editUser(user)" class="text-indigo-600 hover:text-indigo-900">Editar</button>
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
    users: Array,
    roles: Array
  },
  data() {
    return {
      form: {
        id: null,
        name: '',
        email: '',
        password: '',
        rol_id: '',
        estado: true
      }
    }
  },
  methods: {
    createUser() {
      router.post('/users', this.form)
    },
    updateUser() {
      router.put(`/users/${this.form.id}`, this.form)
    },
    editUser(user) {
      this.form = {
        id: user.id,
        name: user.name,
        email: user.email,
        password: '',
        rol_id: user.rol_id,
        estado: user.estado
      }
    },
    resetForm() {
      this.form = {
        id: null,
        name: '',
        email: '',
        password: '',
        rol_id: '',
        estado: true
      }
    }
  }
})
</script>
