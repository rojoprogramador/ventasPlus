<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Registro de Venta</h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            
            <!-- Búsqueda de clientes -->
            <div class="mb-6">
              <h3 class="mb-3 text-lg font-semibold">Información del Cliente</h3>
              
              <div class="flex flex-col md:flex-row md:space-x-4">
                <div class="flex-1 mb-4 md:mb-0">
                  <label for="busquedaCliente" class="block mb-2 text-sm font-medium text-gray-700">
                    Buscar cliente (por nombre, teléfono o documento)
                  </label>
                  <div class="flex">
                    <input 
                      type="text" 
                      id="busquedaCliente" 
                      v-model="busquedaCliente" 
                      @input="buscarClientes"
                      @keyup.enter="buscarClientes"
                      class="flex-1 p-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Ingrese nombre, teléfono o documento del cliente"
                    />
                    <button 
                      @click="buscarClientes" 
                      class="px-4 py-2 font-bold text-white bg-blue-500 rounded-r-md hover:bg-blue-700 focus:outline-none focus:shadow-outline"
                    >
                      Buscar
                    </button>
                  </div>
                </div>
                
                <div class="flex items-end">
                  <button 
                    @click="mostrarModalNuevoCliente = true" 
                    class="px-4 py-2 font-bold text-white bg-green-500 rounded-md hover:bg-green-600 focus:outline-none focus:shadow-outline"
                  >
                    Nuevo Cliente
                  </button>
                </div>
              </div>
              
              <!-- Resultados de la búsqueda de clientes -->
              <div v-if="clientesEncontrados.length > 0" class="mt-4 overflow-y-auto" style="max-height: 200px;">
                <table class="min-w-full bg-white">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="px-4 py-2 text-left">Nombre</th>
                      <th class="px-4 py-2 text-left">Documento</th>
                      <th class="px-4 py-2 text-left">Teléfono</th>
                      <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="cliente in clientesEncontrados" :key="cliente.id" class="border-b hover:bg-gray-50">
                      <td class="px-4 py-2">{{ cliente.nombre }}</td>
                      <td class="px-4 py-2">{{ cliente.documento || 'No registrado' }}</td>
                      <td class="px-4 py-2">{{ cliente.telefono || 'No registrado' }}</td>
                      <td class="px-4 py-2">
                        <button 
                          @click="seleccionarCliente(cliente)" 
                          class="px-3 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600"
                        >
                          Seleccionar
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
              <!-- Cliente seleccionado -->
              <div v-if="clienteSeleccionado" class="p-4 mt-2 bg-white border rounded-md shadow-sm">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-semibold text-gray-800">Cliente seleccionado</h3>
                  <div class="flex items-center space-x-2">
                    <!-- Botón editar cliente -->
                    <button 
                      @click="editarCliente(clienteSeleccionado)" 
                      class="p-1 text-blue-500 hover:text-blue-700 focus:outline-none" 
                      title="Editar cliente"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <!-- Botón quitar cliente -->
                    <button 
                      @click="clienteSeleccionado = null" 
                      class="p-1 text-gray-500 hover:text-gray-700 focus:outline-none"
                      title="Quitar cliente"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  </div>
                </div>
                <div class="mt-2">
                  <p class="text-gray-700"><span class="font-semibold">Nombre:</span> {{ clienteSeleccionado.nombre }}</p>
                  <p v-if="clienteSeleccionado.documento" class="text-gray-700">
                    <span class="font-semibold">{{ clienteSeleccionado.tipo_documento ? clienteSeleccionado.tipo_documento.toUpperCase() : 'Documento' }}:</span> 
                    {{ clienteSeleccionado.documento }}
                  </p>
                  <p v-if="clienteSeleccionado.telefono" class="text-gray-700"><span class="font-semibold">Teléfono:</span> {{ clienteSeleccionado.telefono }}</p>
                  <p v-if="clienteSeleccionado.email" class="text-gray-700"><span class="font-semibold">Email:</span> {{ clienteSeleccionado.email }}</p>
                  <p v-if="clienteSeleccionado.direccion" class="text-gray-700"><span class="font-semibold">Dirección:</span> {{ clienteSeleccionado.direccion }}</p>
                </div>
              </div>
            </div>
            
            <!-- Búsqueda de productos -->
            <div class="mb-6">
              <label for="busqueda" class="block mb-2 text-sm font-medium text-gray-700">
                Buscar producto (por nombre o código de barras)
              </label>
              <div class="flex">
                <input 
                  type="text" 
                  id="busqueda" 
                  v-model="busqueda" 
                  @keyup.enter="buscarProductos"
                  class="flex-1 p-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{'border-red-500': errorBusqueda}"
                  placeholder="Ingrese nombre o código del producto"
                />
                <button 
                  @click="buscarProductos" 
                  class="px-4 py-2 font-bold text-white bg-blue-500 rounded-r-md hover:bg-blue-700 focus:outline-none focus:shadow-outline"
                >
                  Buscar
                </button>
              </div>
              <p v-if="errorBusqueda" class="mt-1 text-sm text-red-500">{{ errorBusqueda }}</p>
            </div>
            
            <!-- Resultados de la búsqueda -->
            <div v-if="productosEncontrados.length > 0" class="mb-6">
              <h3 class="mb-3 text-lg font-semibold">Resultados de la búsqueda</h3>
              <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="px-4 py-2 text-left">Código</th>
                      <th class="px-4 py-2 text-left">Nombre</th>
                      <th class="px-4 py-2 text-left">Descripción</th>
                      <th class="px-4 py-2 text-left">Precio</th>
                      <th class="px-4 py-2 text-left">Stock</th>
                      <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="producto in productosEncontrados" :key="producto.id" class="border-b hover:bg-gray-50">
                      <td class="px-4 py-2">{{ producto.codigo }}</td>
                      <td class="px-4 py-2">{{ producto.nombre }}</td>
                      <td class="px-4 py-2">{{ producto.descripcion }}</td>
                      <td class="px-4 py-2">${{ producto.precio_venta }}</td>
                      <td class="px-4 py-2">{{ producto.stock }}</td>
                      <td class="px-4 py-2">
                        <button 
                          @click="agregarAlCarrito(producto)" 
                          class="px-3 py-1 text-sm text-white bg-green-500 rounded hover:bg-green-600"
                        >
                          Agregar
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            
            <!-- Productos en el carrito -->
            <div class="mb-6">
              <h3 class="mb-3 text-lg font-semibold">Productos en la venta</h3>
              
              <div v-if="carrito.length === 0" class="p-4 text-center text-gray-500 bg-gray-100 rounded">
                Aún no hay productos agregados a la venta
              </div>
              
              <div v-else class="overflow-x-auto">
                <table class="min-w-full bg-white">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="px-4 py-2 text-left">Código</th>
                      <th class="px-4 py-2 text-left">Nombre</th>
                      <th class="px-4 py-2 text-left">Precio</th>
                      <th class="px-4 py-2 text-left">Cantidad</th>
                      <th class="px-4 py-2 text-left">Subtotal</th>
                      <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in carrito" :key="index" class="border-b hover:bg-gray-50">
                      <td class="px-4 py-2">{{ item.codigo }}</td>
                      <td class="px-4 py-2">{{ item.nombre }}</td>
                      <td class="px-4 py-2">${{ item.precio_venta }}</td>
                      <td class="px-4 py-2">
                        <div class="flex items-center">
                          <button 
                            @click="disminuirCantidad(index)" 
                            class="px-2 py-1 text-white bg-gray-500 rounded-l hover:bg-gray-600"
                            :disabled="item.cantidad <= 1"
                          >-</button>
                          <input 
                            type="number" 
                            v-model.number="item.cantidad" 
                            min="1" 
                            :max="item.stock"
                            class="w-16 px-2 py-1 text-center border"
                            @change="validarCantidad(index)"
                          />
                          <button 
                            @click="aumentarCantidad(index)" 
                            class="px-2 py-1 text-white bg-gray-500 rounded-r hover:bg-gray-600"
                            :disabled="item.cantidad >= item.stock"
                          >+</button>
                        </div>
                      </td>
                      <td class="px-4 py-2">${{ (item.precio_venta * item.cantidad).toFixed(2) }}</td>
                      <td class="px-4 py-2">
                        <button 
                          @click="eliminarDelCarrito(index)" 
                          class="px-3 py-1 text-sm text-white bg-red-500 rounded hover:bg-red-600"
                        >
                          Eliminar
                        </button>
                      </td>
                    </tr>
                  </tbody>
                  <tfoot class="bg-gray-50">
                    <tr>
                      <td colspan="4" class="px-4 py-2 text-right font-bold">Total:</td>
                      <td class="px-4 py-2 font-bold">${{ calcularTotal().toFixed(2) }}</td>
                      <td></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            
            <!-- Selección de método de pago -->
            <div class="mb-6">
              <h3 class="mb-3 text-lg font-semibold">Método de pago</h3>
              
              <div class="flex flex-wrap gap-4">
                <div v-for="metodo in metodosPago" :key="metodo.id" class="flex items-center">
                  <input 
                    type="radio" 
                    :id="metodo.id" 
                    :value="metodo.id" 
                    v-model="metodoPagoSeleccionado" 
                    class="mr-2"
                  />
                  <label :for="metodo.id" class="text-gray-700">{{ metodo.nombre }}</label>
                </div>
              </div>
              
              <!-- Pago en efectivo - Cálculo del cambio -->
              <div v-if="metodoPagoSeleccionado === 'efectivo'" class="mt-4">
                <label for="monto_recibido" class="block mb-2 text-sm font-medium text-gray-700">
                  Monto recibido
                </label>
                <div class="flex items-center">
                  <span class="mr-2">$</span>
                  <input 
                    type="number" 
                    id="monto_recibido" 
                    v-model.number="montoRecibido" 
                    class="p-2 border border-gray-300 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    min="0"
                    step="0.01"
                  />
                </div>
                
                <div v-if="montoRecibido >= calcularTotal()" class="mt-2">
                  <p class="font-semibold text-green-600">
                    Cambio a devolver: ${{ (montoRecibido - calcularTotal()).toFixed(2) }}
                  </p>
                </div>
                <div v-else-if="montoRecibido > 0" class="mt-2">
                  <p class="font-semibold text-red-600">
                    Monto insuficiente. Faltan: ${{ (calcularTotal() - montoRecibido).toFixed(2) }}
                  </p>
                </div>
              </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="flex justify-end gap-4 mt-6">
              <button 
                @click="confirmarCancelarVenta" 
                class="px-6 py-2 font-bold text-white bg-gray-500 rounded hover:bg-gray-600 focus:outline-none focus:shadow-outline"
              >
                Cancelar Venta
              </button>
              
              <button 
                @click="finalizarVenta" 
                class="px-6 py-2 font-bold text-white bg-green-500 rounded hover:bg-green-600 focus:outline-none focus:shadow-outline"
                :disabled="!puedeFinalizarVenta"
              >
                Finalizar Venta
              </button>
            </div>
            
            <!-- Modal de confirmación para cancelar venta -->
            <div v-if="mostrarModalCancelar" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
              <div class="p-8 bg-white rounded shadow-xl">
                <h3 class="mb-4 text-lg font-bold">Confirmar cancelación</h3>
                <p>¿Estás seguro de que deseas cancelar esta venta? Se perderán todos los productos agregados.</p>
                <div class="flex justify-end gap-4 mt-6">
                  <button 
                    @click="mostrarModalCancelar = false" 
                    class="px-4 py-2 font-bold text-white bg-gray-500 rounded hover:bg-gray-600"
                  >
                    No, continuar
                  </button>
                  <button 
                    @click="cancelarVenta" 
                    class="px-4 py-2 font-bold text-white bg-red-500 rounded hover:bg-red-600"
                  >
                    Sí, cancelar
                  </button>
                </div>
              </div>
            </div>
            
            <!-- Modal de éxito -->
            <!-- Modal para crear nuevo cliente -->
            <div v-if="mostrarModalNuevoCliente" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
              <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-xl">
                <h3 class="mb-4 text-xl font-bold">Registrar Nuevo Cliente</h3>
                
                <div class="mb-4">
                  <label for="nombre" class="block mb-2 text-sm font-medium text-gray-700">Nombre *</label>
                  <input 
                    type="text" 
                    id="nombre" 
                    v-model="nuevoCliente.nombre" 
                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    required
                  />
                  <p v-if="erroresCliente.nombre" class="mt-1 text-sm text-red-500">{{ erroresCliente.nombre }}</p>
                </div>
                
                <div class="mb-4">
                  <label for="documento" class="block mb-2 text-sm font-medium text-gray-700">Documento</label>
                  <input 
                    type="text" 
                    id="documento" 
                    v-model="nuevoCliente.documento" 
                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                
                <div class="mb-4">
                  <label for="tipo_documento" class="block mb-2 text-sm font-medium text-gray-700">Tipo de Documento</label>
                  <select 
                    id="tipo_documento" 
                    v-model="nuevoCliente.tipo_documento" 
                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="">Seleccione...</option>
                    <option value="dni">DNI</option>
                    <option value="ruc">RUC</option>
                    <option value="pasaporte">Pasaporte</option>
                    <option value="otro">Otro</option>
                  </select>
                </div>
                
                <div class="mb-4">
                  <label for="telefono" class="block mb-2 text-sm font-medium text-gray-700">Teléfono</label>
                  <input 
                    type="text" 
                    id="telefono" 
                    v-model="nuevoCliente.telefono" 
                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                
                <div class="mb-4">
                  <label for="email" class="block mb-2 text-sm font-medium text-gray-700">Email</label>
                  <input 
                    type="email" 
                    id="email" 
                    v-model="nuevoCliente.email" 
                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                
                <div class="mb-4">
                  <label for="direccion" class="block mb-2 text-sm font-medium text-gray-700">Dirección</label>
                  <input 
                    type="text" 
                    id="direccion" 
                    v-model="nuevoCliente.direccion" 
                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                
                <div class="flex justify-end gap-4 mt-6">
                  <button 
                    @click="mostrarModalNuevoCliente = false" 
                    class="px-4 py-2 font-bold text-gray-700 bg-gray-200 rounded hover:bg-gray-300 focus:outline-none focus:shadow-outline"
                  >
                    Cancelar
                  </button>
                  
                  <button 
                    @click="guardarNuevoCliente" 
                    class="px-4 py-2 font-bold text-white bg-green-500 rounded hover:bg-green-600 focus:outline-none focus:shadow-outline"
                  >
                    Guardar Cliente
                  </button>
                </div>
              </div>
            </div>
            
            <!-- Modal para editar cliente -->
            <div v-if="mostrarModalEditarCliente" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
              <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-xl">
                <h3 class="mb-4 text-xl font-bold">Editar Cliente</h3>
                
                <div class="mb-4">
                  <label for="nombre_editar" class="block mb-2 text-sm font-medium text-gray-700">Nombre *</label>
                  <input 
                    type="text" 
                    id="nombre_editar" 
                    v-model="clienteEditar.nombre" 
                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    required
                  />
                  <p v-if="erroresClienteEditar.nombre" class="mt-1 text-sm text-red-500">{{ erroresClienteEditar.nombre }}</p>
                </div>
                
                <div class="mb-4">
                  <label for="documento_editar" class="block mb-2 text-sm font-medium text-gray-700">Documento</label>
                  <input 
                    type="text" 
                    id="documento_editar" 
                    v-model="clienteEditar.documento" 
                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                  <p v-if="erroresClienteEditar.documento" class="mt-1 text-sm text-red-500">{{ erroresClienteEditar.documento }}</p>
                </div>
                
                <div class="mb-4">
                  <label for="tipo_documento_editar" class="block mb-2 text-sm font-medium text-gray-700">Tipo de Documento</label>
                  <select 
                    id="tipo_documento_editar" 
                    v-model="clienteEditar.tipo_documento" 
                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="">Seleccione...</option>
                    <option value="dni">DNI</option>
                    <option value="ruc">RUC</option>
                    <option value="pasaporte">Pasaporte</option>
                    <option value="otro">Otro</option>
                  </select>
                </div>
                
                <div class="mb-4">
                  <label for="telefono_editar" class="block mb-2 text-sm font-medium text-gray-700">Teléfono</label>
                  <input 
                    type="text" 
                    id="telefono_editar" 
                    v-model="clienteEditar.telefono" 
                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                  <p v-if="erroresClienteEditar.telefono" class="mt-1 text-sm text-red-500">{{ erroresClienteEditar.telefono }}</p>
                </div>
                
                <div class="mb-4">
                  <label for="email_editar" class="block mb-2 text-sm font-medium text-gray-700">Email</label>
                  <input 
                    type="email" 
                    id="email_editar" 
                    v-model="clienteEditar.email" 
                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                  <p v-if="erroresClienteEditar.email" class="mt-1 text-sm text-red-500">{{ erroresClienteEditar.email }}</p>
                </div>
                
                <div class="mb-4">
                  <label for="direccion_editar" class="block mb-2 text-sm font-medium text-gray-700">Dirección</label>
                  <input 
                    type="text" 
                    id="direccion_editar" 
                    v-model="clienteEditar.direccion" 
                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                  <p v-if="erroresClienteEditar.direccion" class="mt-1 text-sm text-red-500">{{ erroresClienteEditar.direccion }}</p>
                </div>
                
                <div class="flex justify-end gap-4 mt-6">
                  <button 
                    @click="mostrarModalEditarCliente = false" 
                    class="px-4 py-2 font-bold text-gray-700 bg-gray-200 rounded hover:bg-gray-300 focus:outline-none focus:shadow-outline"
                  >
                    Cancelar
                  </button>
                  
                  <button 
                    @click="actualizarCliente" 
                    class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-600 focus:outline-none focus:shadow-outline"
                  >
                    Guardar Cambios
                  </button>
                </div>
              </div>
            </div>
            
            <div v-if="mostrarModalExito" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
              <div class="p-8 bg-white rounded shadow-xl">
                <h3 class="mb-4 text-xl font-bold text-green-600">¡Venta registrada exitosamente!</h3>
                <p>La venta se ha registrado correctamente.</p>
                <p v-if="metodoPagoSeleccionado === 'efectivo'" class="mt-2 font-semibold">
                  Cambio a devolver: ${{ cambioCalculado.toFixed(2) }}
                </p>
                <div class="flex justify-end gap-4 mt-6">
                  <button 
                    @click="verComprobante" 
                    class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-600"
                  >
                    Ver comprobante
                  </button>
                  <button 
                    @click="nuevaVenta" 
                    class="px-4 py-2 font-bold text-white bg-green-500 rounded hover:bg-green-600"
                  >
                    Nueva venta
                  </button>
                </div>
              </div>
            </div>
            
            <!-- Modal de error -->
            <div v-if="mostrarModalError" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
              <div class="p-8 bg-white rounded shadow-xl">
                <h3 class="mb-4 text-xl font-bold text-red-600">Error al procesar la venta</h3>
                <p>{{ mensajeError }}</p>
                <div class="flex justify-end mt-6">
                  <button 
                    @click="mostrarModalError = false" 
                    class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-600"
                  >
                    Entendido
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
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Variables reactivas
const busqueda = ref('');
const errorBusqueda = ref('');
const productosEncontrados = ref([]);
const carrito = ref([]);
const metodoPagoSeleccionado = ref('efectivo');
const montoRecibido = ref(0);
const mostrarModalCancelar = ref(false);
const mostrarModalExito = ref(false);
const mostrarModalError = ref(false);
const mensajeError = ref('');
const cambioCalculado = ref(0);
const ventaId = ref(null);

// Variables para la búsqueda de clientes
const busquedaCliente = ref('');
const clientesEncontrados = ref([]);
const clienteSeleccionado = ref(null);
const mostrarModalNuevoCliente = ref(false);
const nuevoCliente = ref({
  nombre: '',
  documento: '',
  tipo_documento: '',
  telefono: '',
  email: '',
  direccion: ''
});
const erroresCliente = ref({});

// Variables para la edición de clientes
const mostrarModalEditarCliente = ref(false);
const clienteEditar = ref({
  id: null,
  nombre: '',
  documento: '',
  tipo_documento: '',
  telefono: '',
  email: '',
  direccion: ''
});
const erroresClienteEditar = ref({});

// Métodos de pago disponibles
const metodosPago = [
  { id: 'efectivo', nombre: 'Efectivo' },
  { id: 'tarjeta', nombre: 'Tarjeta' },
  { id: 'transferencia', nombre: 'Transferencia' }
];

// Computed properties
const puedeFinalizarVenta = computed(() => {
  if (carrito.value.length === 0) return false;
  
  if (metodoPagoSeleccionado.value === 'efectivo') {
    return montoRecibido.value >= calcularTotal();
  }
  
  return true;
});

// Métodos
const buscarProductos = async () => {
  if (!busqueda.value) {
    errorBusqueda.value = 'Debe ingresar un texto para buscar';
    return;
  }
  
  errorBusqueda.value = '';
  
  try {
    const response = await axios.get(route('ventas.buscar-productos'), {
      params: { busqueda: busqueda.value }
    });
    
    productosEncontrados.value = response.data.productos;
    
    if (productosEncontrados.value.length === 0) {
      errorBusqueda.value = 'No se encontraron productos';
    }
  } catch (error) {
    console.error('Error al buscar productos:', error);
    errorBusqueda.value = 'Error al buscar productos';
  }
};

const agregarAlCarrito = (producto) => {
  // Verificar si el producto ya está en el carrito
  const indiceExistente = carrito.value.findIndex(item => item.id === producto.id);
  
  if (indiceExistente >= 0) {
    // Si ya existe, aumentar cantidad si hay stock disponible
    if (carrito.value[indiceExistente].cantidad < producto.stock) {
      carrito.value[indiceExistente].cantidad++;
    }
  } else {
    // Si no existe, agregarlo con cantidad 1
    carrito.value.push({
      ...producto,
      cantidad: 1
    });
  }
  
  // Limpiar resultados de búsqueda
  productosEncontrados.value = [];
  busqueda.value = '';
};

const eliminarDelCarrito = (index) => {
  carrito.value.splice(index, 1);
};

const aumentarCantidad = (index) => {
  const item = carrito.value[index];
  if (item.cantidad < item.stock) {
    item.cantidad++;
  }
};

const disminuirCantidad = (index) => {
  if (carrito.value[index].cantidad > 1) {
    carrito.value[index].cantidad--;
  }
};

const validarCantidad = (index) => {
  const item = carrito.value[index];
  
  // Asegurar que la cantidad sea un número válido
  if (isNaN(item.cantidad) || item.cantidad < 1) {
    item.cantidad = 1;
  }
  
  // Asegurar que no exceda el stock disponible
  if (item.cantidad > item.stock) {
    item.cantidad = item.stock;
  }
};

const calcularTotal = () => {
  return carrito.value.reduce((total, item) => {
    return total + (item.precio_venta * item.cantidad);
  }, 0);
};

const confirmarCancelarVenta = () => {
  if (carrito.value.length > 0) {
    mostrarModalCancelar.value = true;
  } else {
    // Si no hay productos, simplemente redireccionar al dashboard
    router.visit(route('dashboard'));
  }
};

const cancelarVenta = () => {
  axios.post(route('ventas.cancelar'))
    .then(() => {
      router.visit(route('dashboard'));
    })
    .catch(error => {
      console.error('Error al cancelar la venta:', error);
    });
};

const finalizarVenta = async () => {
  if (carrito.value.length === 0) {
    mensajeError.value = 'Debe agregar al menos un producto para registrar la venta';
    mostrarModalError.value = true;
    return;
  }
  
  if (metodoPagoSeleccionado.value === 'efectivo' && montoRecibido.value < calcularTotal()) {
    mensajeError.value = 'El monto recibido es menor al total de la venta';
    mostrarModalError.value = true;
    return;
  }
  
  try {
    const response = await axios.post(route('ventas.store'), {
      productos: carrito.value,
      tipo_pago: metodoPagoSeleccionado.value,
      monto_recibido: montoRecibido.value,
      cliente_id: clienteSeleccionado.value ? clienteSeleccionado.value.id : null
    });
    
    // Si la venta se procesó correctamente
    if (response.data.success) {
      ventaId.value = response.data.venta.id;
      cambioCalculado.value = response.data.cambio;
      mostrarModalExito.value = true;
    }
  } catch (error) {
    console.error('Error al procesar la venta:', error);
    
    // Mostrar mensaje de error
    if (error.response && error.response.data.message) {
      mensajeError.value = error.response.data.message;
    } else {
      mensajeError.value = 'Ha ocurrido un error al procesar la venta';
    }
    
    mostrarModalError.value = true;
  }
};

const verComprobante = () => {
  if (ventaId.value) {
    router.visit(route('ventas.comprobante', ventaId.value));
  }
};

const nuevaVenta = () => {
  // Reiniciar todo
  carrito.value = [];
  busqueda.value = '';
  errorBusqueda.value = '';
  productosEncontrados.value = [];
  metodoPagoSeleccionado.value = 'efectivo';
  montoRecibido.value = 0;
  // No reiniciamos el cliente seleccionado para mantener la continuidad
};

// Funciones para la búsqueda y gestión de clientes
const buscarClientes = async () => {
  // Si la búsqueda está vacía, no hacemos nada
  if (busquedaCliente.value.trim() === '') {
    clientesEncontrados.value = [];
    return;
  }
  
  try {
    const response = await axios.get(route('clientes.buscar'), {
      params: { busqueda: busquedaCliente.value }
    });
    
    clientesEncontrados.value = response.data.clientes;
  } catch (error) {
    console.error('Error al buscar clientes:', error);
  }
};

const seleccionarCliente = (cliente) => {
  clienteSeleccionado.value = cliente;
  clientesEncontrados.value = [];
  busquedaCliente.value = '';
};

const guardarNuevoCliente = async () => {
  // Validar que el nombre no esté vacío
  erroresCliente.value = {};
  
  if (!nuevoCliente.value.nombre || nuevoCliente.value.nombre.trim() === '') {
    erroresCliente.value.nombre = 'El nombre del cliente es obligatorio';
    return;
  }
  
  try {
    const response = await axios.post(route('clientes.guardar-rapido'), nuevoCliente.value);
    
    if (response.data.success) {
      // Seleccionar el cliente recién creado
      seleccionarCliente(response.data.cliente);
      
      // Limpiar el formulario y cerrar el modal
      nuevoCliente.value = {
        nombre: '',
        documento: '',
        tipo_documento: '',
        telefono: '',
        email: '',
        direccion: ''
      };
      mostrarModalNuevoCliente.value = false;
    } else if (response.data.tipo === 'existente') {
      // Si el cliente ya existe, preguntar al usuario si desea seleccionarlo
      if (confirm(`${response.data.message}. ¿Desea seleccionar este cliente?`)) {
        seleccionarCliente(response.data.cliente);
        
        // Limpiar el formulario y cerrar el modal
        nuevoCliente.value = {
          nombre: '',
          documento: '',
          tipo_documento: '',
          telefono: '',
          email: '',
          direccion: ''
        };
        mostrarModalNuevoCliente.value = false;
      } else {
        // Si el usuario no quiere seleccionar el cliente existente, mostrar el error
        erroresCliente.value.general = response.data.message;
      }
    }
  } catch (error) {
    console.error('Error al guardar el cliente:', error);
    
    if (error.response && error.response.data.errors) {
      // Mapear los errores de validación
      const validationErrors = error.response.data.errors;
      Object.keys(validationErrors).forEach(key => {
        erroresCliente.value[key] = validationErrors[key][0];
      });
    } else {
      erroresCliente.value.general = 'Error al guardar el cliente';
    }
  }
};

// Función para abrir el modal de edición de cliente
const editarCliente = (cliente) => {
  // Copiar los datos del cliente al formulario de edición
  clienteEditar.value = {
    id: cliente.id,
    nombre: cliente.nombre,
    documento: cliente.documento || '',
    tipo_documento: cliente.tipo_documento || '',
    telefono: cliente.telefono || '',
    email: cliente.email || '',
    direccion: cliente.direccion || ''
  };
  
  // Limpiar los errores
  erroresClienteEditar.value = {};
  
  // Mostrar el modal de edición
  mostrarModalEditarCliente.value = true;
};

// Función para actualizar un cliente existente
const actualizarCliente = async () => {
  // Validar que el nombre no esté vacío
  erroresClienteEditar.value = {};
  
  if (!clienteEditar.value.nombre || clienteEditar.value.nombre.trim() === '') {
    erroresClienteEditar.value.nombre = 'El nombre del cliente es obligatorio';
    return;
  }
  
  try {
    const response = await axios.put(route('clientes.actualizar', clienteEditar.value.id), clienteEditar.value);
    
    if (response.data.success) {
      // Actualizar el cliente seleccionado
      clienteSeleccionado.value = response.data.cliente;
      
      // Cerrar el modal de edición
      mostrarModalEditarCliente.value = false;
      
      // Mostrar mensaje de éxito
      alert('Cliente actualizado correctamente');
    }
  } catch (error) {
    console.error('Error al actualizar el cliente:', error);
    
    if (error.response && error.response.data.errors) {
      // Mapear los errores de validación
      const validationErrors = error.response.data.errors;
      Object.keys(validationErrors).forEach(key => {
        erroresClienteEditar.value[key] = validationErrors[key][0];
      });
    } else if (error.response && error.response.data.message) {
      erroresClienteEditar.value.general = error.response.data.message;
    } else {
      erroresClienteEditar.value.general = 'Error al actualizar el cliente';
    }
  }
};
</script>
