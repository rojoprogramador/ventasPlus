<template>
  <div class="p-6">
    <div class="mb-6 flex justify-between items-center">
      <h2 class="text-2xl font-bold">Gestión de Usuarios</h2>
      <Link
        :href="route('dashboard')"
        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 flex items-center"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Volver al Dashboard
      </Link>
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
          <div class="md:col-span-2" v-if="form.password">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Requisitos de contraseña:</h3>
            <ul class="text-sm text-gray-600 space-y-1">
              <li :class="passwordRequirements.minLength ? 'text-green-600' : ''">✓ Mínimo 8 caracteres</li>
              <li :class="passwordRequirements.hasLower ? 'text-green-600' : ''">✓ Al menos una minúscula</li>
              <li :class="passwordRequirements.hasUpper ? 'text-green-600' : ''">✓ Al menos una mayúscula</li>
              <li :class="passwordRequirements.hasNumber ? 'text-green-600' : ''">✓ Al menos un número</li>
              <li :class="passwordRequirements.hasSpecial ? 'text-green-600' : ''">✓ Al menos un carácter especial (@$!%*#?&)</li>
            </ul>
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
            <td class="px-6 py-4 whitespace-nowrap space-x-2">
              <button @click="editUser(user)" class="text-indigo-600 hover:text-indigo-900">Editar</button>
              <button @click="openPermisosModal(user)" class="text-green-600 hover:text-green-900">Permisos</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <!-- Modal de Permisos -->
  <div v-if="showPermisosModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
      <div class="p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium">Permisos de {{ selectedUser?.name }}</h3>
          <button @click="showPermisosModal = false" class="text-gray-400 hover:text-gray-500">
            <span class="sr-only">Cerrar</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="space-y-4">
          <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="font-medium text-gray-700 mb-2">Permisos del Rol</h4>
            <div class="grid grid-cols-2 gap-4">
              <div v-for="permiso in selectedUser?.rol?.permisos" :key="'rol-'+permiso.id" 
                   class="text-sm text-gray-600 bg-gray-100 p-2 rounded">
                {{ permiso.nombre }}
              </div>
            </div>
          </div>

          <div>
            <h4 class="font-medium text-gray-700 mb-2">Permisos Individuales</h4>
            <div class="grid grid-cols-2 gap-4">
              <div v-for="permiso in permisos" :key="permiso.id" class="flex items-center space-x-2">
                <input type="checkbox" 
                       :id="'permiso-'+permiso.id"
                       v-model="permisosUsuario"
                       :value="permiso.id"
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <label :for="'permiso-'+permiso.id" class="text-sm text-gray-700">{{ permiso.nombre }}</label>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
          <button @click="showPermisosModal = false" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">
            Cancelar
          </button>
          <button @click="savePermisos" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            Guardar Permisos
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import { router } from '@inertiajs/vue3'

export default defineComponent({
  props: {
    users: Array,
    roles: Array,
    permisos: Array
  },
  data() {
    return {
      form: {
        id: null,
        name: '',
        email: '',
        password: '',
        rol_id: '',
        estado: true,
        permisos: []
      },
      showPermisosModal: false,
      selectedUser: null,
      permisosUsuario: [],
      passwordRequirements: {
        minLength: false,
        hasLower: false,
        hasUpper: false,
        hasNumber: false,
        hasSpecial: false
      }
    }
  },
  watch: {
    'form.password': {
      handler(newVal) {
        this.passwordRequirements = {
          minLength: newVal.length >= 8,
          hasLower: /[a-z]/.test(newVal),
          hasUpper: /[A-Z]/.test(newVal),
          hasNumber: /[0-9]/.test(newVal),
          hasSpecial: /[@$!%*#?&]/.test(newVal)
        }
      },
      immediate: true
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
        estado: user.estado,
        permisos: user.permisos || []
      }
    },
    resetForm() {
      this.form = {
        id: null,
        name: '',
        email: '',
        password: '',
        rol_id: '',
        estado: true,
        permisos: []
      }
    },
    openPermisosModal(user) {
      this.selectedUser = user;
      this.permisosUsuario = user.permisos.map(p => p.id);
      this.showPermisosModal = true;
    },
    savePermisos() {
      const permisosData = this.permisos.map(p => ({
        id: p.id,
        habilitado: this.permisosUsuario.includes(p.id)
      }));

      router.put(`/users/${this.selectedUser.id}`, {
        ...this.selectedUser,
        permisos: permisosData
      }, {
        preserveScroll: true,
        onSuccess: () => {
          this.showPermisosModal = false;
        }
      });
    }
  }
})
</script>
