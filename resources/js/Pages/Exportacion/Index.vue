<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Exportación de Datos</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <h3 class="mb-6 text-lg font-semibold">Configuración de Exportación</h3>

            <!-- Formulario de exportación -->
            <form @submit.prevent="exportarDatos" class="space-y-6">
              <!-- Selección de tabla -->
              <div>
                <label for="tabla" class="block mb-2 text-sm font-medium text-gray-700">Seleccione la tabla a exportar</label>
                <select 
                  id="tabla" 
                  v-model="formulario.tabla" 
                  class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  @change="cambiarTabla"
                  required
                >
                  <option value="">Seleccione una tabla</option>
                  <option v-for="tabla in tablas" :key="tabla.id" :value="tabla.id">{{ tabla.nombre }}</option>
                </select>
              </div>

              <!-- Selección de campos -->
              <div v-if="formulario.tabla">
                <label class="block mb-2 text-sm font-medium text-gray-700">Seleccione los campos a exportar</label>
                <div class="p-4 border border-gray-300 rounded-md max-h-60 overflow-y-auto">
                  <div class="flex items-center mb-2">
                    <input 
                      type="checkbox" 
                      id="seleccionar-todos" 
                      v-model="seleccionarTodos" 
                      @change="toggleSeleccionarTodos" 
                      class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                    />
                    <label for="seleccionar-todos" class="ml-2 text-sm font-medium text-gray-700">Seleccionar todos</label>
                  </div>
                  <div class="grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="campo in camposDisponibles" :key="campo.id" class="flex items-center">
                      <input 
                        type="checkbox" 
                        :id="'campo-' + campo.id" 
                        v-model="formulario.campos" 
                        :value="campo.id" 
                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                      <label :for="'campo-' + campo.id" class="ml-2 text-sm text-gray-700">{{ campo.nombre }}</label>
                    </div>
                  </div>
                </div>
                <p v-if="errores.campos" class="mt-1 text-sm text-red-600">{{ errores.campos }}</p>
              </div>

              <!-- Filtros -->
              <div v-if="formulario.tabla" class="p-4 border border-gray-300 rounded-md">
                <h4 class="mb-4 text-md font-medium">Filtros (Opcional)</h4>
                
                <!-- Filtro de estado -->
                <div v-if="tieneColumnaEstado" class="mb-4">
                  <label for="estado" class="block mb-2 text-sm font-medium text-gray-700">Estado</label>
                  <select 
                    id="estado" 
                    v-model="formulario.filtros.estado" 
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="todos">Todos</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                  </select>
                </div>
                
                <!-- Filtro de fechas -->
                <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                  <div>
                    <label for="fecha_inicio" class="block mb-2 text-sm font-medium text-gray-700">Fecha de inicio</label>
                    <input 
                      type="date" 
                      id="fecha_inicio" 
                      v-model="formulario.filtros.fecha_inicio" 
                      class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                  <div>
                    <label for="fecha_fin" class="block mb-2 text-sm font-medium text-gray-700">Fecha de fin</label>
                    <input 
                      type="date" 
                      id="fecha_fin" 
                      v-model="formulario.filtros.fecha_fin" 
                      class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                </div>
                
                <!-- Búsqueda general -->
                <div>
                  <label for="busqueda" class="block mb-2 text-sm font-medium text-gray-700">Búsqueda general</label>
                  <input 
                    type="text" 
                    id="busqueda" 
                    v-model="formulario.filtros.busqueda" 
                    placeholder="Buscar por cualquier campo de texto..." 
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
              </div>

              <!-- Formato de exportación -->
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Formato de exportación</label>
                <div class="flex flex-wrap gap-4">
                  <div v-for="formato in formatos" :key="formato.id" class="flex items-center">
                    <input 
                      type="radio" 
                      :id="'formato-' + formato.id" 
                      v-model="formulario.formato" 
                      :value="formato.id" 
                      class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                      required
                    />
                    <label :for="'formato-' + formato.id" class="ml-2 text-sm text-gray-700">{{ formato.nombre }}</label>
                  </div>
                </div>
                <p v-if="errores.formato" class="mt-1 text-sm text-red-600">{{ errores.formato }}</p>
              </div>

              <!-- Botones de acción -->
              <div class="flex justify-end space-x-4">
                <button 
                  type="button" 
                  @click="limpiarFormulario" 
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Limpiar
                </button>
                <button 
                  type="submit" 
                  class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  :disabled="cargando"
                >
                  <span v-if="cargando">
                    <svg class="inline w-4 h-4 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Exportando...
                  </span>
                  <span v-else>Exportar Datos</span>
                </button>
              </div>
            </form>

            <!-- Historial de exportaciones -->
            <div v-if="exportacionesRecientes.length > 0" class="mt-8">
              <h3 class="mb-4 text-lg font-semibold">Exportaciones Recientes</h3>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Archivo</th>
                      <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tabla</th>
                      <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Formato</th>
                      <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Fecha</th>
                      <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Acción</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(exportacion, index) in exportacionesRecientes" :key="index">
                      <td class="px-6 py-4 whitespace-nowrap">{{ exportacion.nombre_archivo }}</td>
                      <td class="px-6 py-4 whitespace-nowrap">{{ obtenerNombreTabla(exportacion.tabla) }}</td>
                      <td class="px-6 py-4 whitespace-nowrap">{{ obtenerNombreFormato(exportacion.formato) }}</td>
                      <td class="px-6 py-4 whitespace-nowrap">{{ formatearFecha(exportacion.fecha) }}</td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <a :href="exportacion.url" target="_blank" class="text-indigo-600 hover:text-indigo-900">Descargar</a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de éxito -->
    <div v-if="mostrarModalExito" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="p-8 bg-white rounded shadow-xl">
        <h3 class="mb-4 text-xl font-bold text-green-600">¡Exportación completada!</h3>
        <p>Los datos se han exportado correctamente.</p>
        <div class="flex justify-end gap-4 mt-6">
          <button 
            @click="mostrarModalExito = false" 
            class="px-4 py-2 font-bold text-white bg-gray-500 rounded hover:bg-gray-600"
          >
            Cerrar
          </button>
          <a 
            :href="urlDescarga" 
            target="_blank" 
            class="px-4 py-2 font-bold text-white bg-green-500 rounded hover:bg-green-600"
          >
            Descargar archivo
          </a>
        </div>
      </div>
    </div>

    <!-- Modal de error -->
    <div v-if="mostrarModalError" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="p-8 bg-white rounded shadow-xl">
        <h3 class="mb-4 text-xl font-bold text-red-600">Error en la exportación</h3>
        <p>{{ mensajeError }}</p>
        <div class="flex justify-end mt-6">
          <button 
            @click="mostrarModalError = false" 
            class="px-4 py-2 font-bold text-white bg-red-500 rounded hover:bg-red-600"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Props
const props = defineProps({
  tablas: Array,
  formatos: Array,
  campos: Object
});

// Variables reactivas
const formulario = ref({
  tabla: '',
  campos: [],
  formato: '',
  filtros: {
    estado: 'todos',
    fecha_inicio: '',
    fecha_fin: '',
    busqueda: ''
  }
});

const seleccionarTodos = ref(false);
const camposDisponibles = ref([]);
const errores = ref({});
const cargando = ref(false);
const mostrarModalExito = ref(false);
const mostrarModalError = ref(false);
const mensajeError = ref('');
const urlDescarga = ref('');
const exportacionesRecientes = ref([]);

// Computed properties
const tieneColumnaEstado = computed(() => {
  if (!formulario.value.tabla) return false;
  
  return camposDisponibles.value.some(campo => campo.id === 'estado');
});

// Métodos
const cambiarTabla = () => {
  formulario.value.campos = [];
  seleccionarTodos.value = false;
  
  if (formulario.value.tabla && props.campos[formulario.value.tabla]) {
    camposDisponibles.value = props.campos[formulario.value.tabla];
  } else {
    camposDisponibles.value = [];
  }
};

const toggleSeleccionarTodos = () => {
  if (seleccionarTodos.value) {
    formulario.value.campos = camposDisponibles.value.map(campo => campo.id);
  } else {
    formulario.value.campos = [];
  }
};

const limpiarFormulario = () => {
  formulario.value = {
    tabla: '',
    campos: [],
    formato: '',
    filtros: {
      estado: 'todos',
      fecha_inicio: '',
      fecha_fin: '',
      busqueda: ''
    }
  };
  seleccionarTodos.value = false;
  camposDisponibles.value = [];
  errores.value = {};
};

const exportarDatos = async () => {
  // Validar el formulario
  errores.value = {};
  
  if (!formulario.value.tabla) {
    errores.value.tabla = 'Debe seleccionar una tabla';
    return;
  }
  
  if (formulario.value.campos.length === 0) {
    errores.value.campos = 'Debe seleccionar al menos un campo';
    return;
  }
  
  if (!formulario.value.formato) {
    errores.value.formato = 'Debe seleccionar un formato de exportación';
    return;
  }
  
  // Validar fechas
  if (formulario.value.filtros.fecha_inicio && formulario.value.filtros.fecha_fin) {
    const fechaInicio = new Date(formulario.value.filtros.fecha_inicio);
    const fechaFin = new Date(formulario.value.filtros.fecha_fin);
    
    if (fechaFin < fechaInicio) {
      errores.value.fecha = 'La fecha de fin debe ser posterior a la fecha de inicio';
      return;
    }
  }
  
  try {
    cargando.value = true;
    
    const response = await axios.post(route('exportacion.exportar'), formulario.value);
    
    if (response.data.success) {
      // Guardar la URL de descarga
      urlDescarga.value = response.data.url;
      
      // Agregar a exportaciones recientes
      exportacionesRecientes.value.unshift({
        nombre_archivo: response.data.nombre_archivo,
        tabla: formulario.value.tabla,
        formato: formulario.value.formato,
        fecha: new Date(),
        url: response.data.url
      });
      
      // Limitar a 5 exportaciones recientes
      if (exportacionesRecientes.value.length > 5) {
        exportacionesRecientes.value = exportacionesRecientes.value.slice(0, 5);
      }
      
      // Guardar en localStorage
      localStorage.setItem('exportaciones_recientes', JSON.stringify(exportacionesRecientes.value));
      
      // Mostrar modal de éxito
      mostrarModalExito.value = true;
    } else {
      mensajeError.value = response.data.message || 'Error al exportar los datos';
      mostrarModalError.value = true;
    }
  } catch (error) {
    console.error('Error al exportar datos:', error);
    
    if (error.response && error.response.data.message) {
      mensajeError.value = error.response.data.message;
    } else {
      mensajeError.value = 'Error al exportar los datos';
    }
    
    mostrarModalError.value = true;
  } finally {
    cargando.value = false;
  }
};

const obtenerNombreTabla = (tablaId) => {
  const tabla = props.tablas.find(t => t.id === tablaId);
  return tabla ? tabla.nombre : tablaId;
};

const obtenerNombreFormato = (formatoId) => {
  const formato = props.formatos.find(f => f.id === formatoId);
  return formato ? formato.nombre : formatoId;
};

const formatearFecha = (fecha) => {
  return new Date(fecha).toLocaleString();
};

// Cargar exportaciones recientes al montar el componente
onMounted(() => {
  const exportacionesGuardadas = localStorage.getItem('exportaciones_recientes');
  
  if (exportacionesGuardadas) {
    try {
      exportacionesRecientes.value = JSON.parse(exportacionesGuardadas);
    } catch (error) {
      console.error('Error al cargar exportaciones recientes:', error);
    }
  }
});
</script>
