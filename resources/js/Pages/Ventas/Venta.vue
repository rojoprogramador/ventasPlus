<template>
  <div class="container mx-auto py-4">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Nueva Venta</h1>
      <button 
        @click="finalizarVenta"
        :disabled="carrito.length === 0"
        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Finalizar Venta
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Sección de productos -->
      <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-xl font-semibold mb-4">Productos</h2>
        <div class="flex space-x-2 mb-4">
          <input
            v-model="busqueda"
            type="text"
            placeholder="Buscar producto..."
            class="flex-1 px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500"
          />
          <button
            @click="agregarProducto"
            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
          >
            Agregar
          </button>
        </div>
        <div class="overflow-y-auto max-h-64">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-100">
                <th class="px-4 py-2">Código</th>
                <th class="px-4 py-2">Nombre</th>
                <th class="px-4 py-2">Precio</th>
                <th class="px-4 py-2">Stock</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="producto in productosFiltrados"
                :key="producto.id"
                @click="seleccionarProducto(producto)"
                class="hover:bg-gray-50 cursor-pointer"
              >
                <td class="px-4 py-2">{{ producto.codigo_barras }}</td>
                <td class="px-4 py-2">{{ producto.nombre }}</td>
                <td class="px-4 py-2">${{ formatPrice(producto.precio) }}</td>
                <td class="px-4 py-2">{{ producto.stock }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Sección de carrito -->
      <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-xl font-semibold mb-4">Carrito</h2>
        <div class="space-y-4">
          <div
            v-for="(item, index) in carrito"
            :key="item.id"
            class="border-b pb-4 last:border-0"
          >
            <div class="flex justify-between items-center">
              <div>
                <h3 class="font-semibold">{{ item.producto.nombre }}</h3>
                <p class="text-sm text-gray-600">${{ item.precio_unitario }}</p>
              </div>
              <div class="flex space-x-2">
                <button
                  @click="disminuirCantidad(index)"
                  class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300"
                >
                  -
                </button>
                <span class="px-2 py-1 bg-gray-100 rounded">{{ item.cantidad }}</span>
                <button
                  @click="aumentarCantidad(index)"
                  class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300"
                >
                  +
                </button>
                <button
                  @click="eliminarItem(index)"
                  class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                >
                  Eliminar
                </button>
              </div>
            </div>
            
            <!-- Sección de descuentos del producto -->
            <div class="mt-2">
              <div class="flex items-center space-x-2">
                <input
                  v-model="item.descuento"
                  type="text"
                  placeholder="0"
                  class="w-24 px-2 py-1 border rounded"
                />
                <select
                  v-model="item.tipo_descuento"
                  class="px-2 py-1 border rounded"
                >
                  <option value="porcentaje">%</option>
                  <option value="fijo">$</option>
                </select>
                <button
                  @click="aplicarDescuento(item)"
                  class="px-4 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                >
                  Aplicar
                </button>
              </div>
              <div v-if="item.historial_descuentos.length > 0" class="mt-2">
                <h4 class="font-semibold text-sm">Historial de descuentos:</h4>
                <ul class="space-y-1 text-sm">
                  <li
                    v-for="descuento in item.historial_descuentos"
                    :key="descuento.id"
                    class="text-gray-600"
                  >
                    {{ descuento.tipo }}: {{ descuento.valor }} por {{ descuento.aplicado_por }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
          
          <!-- Resumen de venta -->
          <div class="mt-4 pt-4 border-t">
            <div class="flex justify-between items-center">
              <span class="font-semibold">Subtotal:</span>
              <span>${{ subtotal }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="font-semibold">Descuentos:</span>
              <span>${{ descuentosTotales }}</span>
            </div>
            <div class="flex justify-between items-center font-bold mt-2">
              <span>Total:</span>
              <span>${{ total }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

const productos = ref([])
const carrito = ref([])
const busqueda = ref('')

// Cargar productos al inicio
const cargarProductos = async () => {
  try {
    const response = await fetch('/api/productos')
    if (!response.ok) {
      console.error('Error al cargar productos:', response.status)
      productos.value = [] // Asignar array vacío en caso de error
      return
    }
    const data = await response.json()
    productos.value = Array.isArray(data) ? data : [] // Asegurar que sea un array
  } catch (error) {
    console.error('Error al cargar productos:', error)
    productos.value = [] // Asignar array vacío en caso de error
  }
}
cargarProductos()

// Filtros y cálculos
const productosFiltrados = computed(() => {
  if (!Array.isArray(productos.value)) {
    return []
  }
  return productos.value.filter(producto => {
    if (!producto) return false
    return (producto.codigo_barras && producto.codigo_barras.includes(busqueda.value)) ||
      (producto.nombre && producto.nombre.toLowerCase().includes(busqueda.value.toLowerCase()))
  })
})

const subtotal = computed(() => {
  return carrito.value.reduce((total, item) => 
    total + (item.precio_unitario * item.cantidad), 0)
})

const descuentosTotales = computed(() => {
  return carrito.value.reduce((total, item) => 
    total + (item.descuento ? calcularDescuento(item) : 0), 0)
})

const total = computed(() => subtotal.value - descuentosTotales.value)

// Métodos
const seleccionarProducto = (producto) => {
  const item = {
    id: producto.id,
    producto,
    cantidad: 1,
    precio_unitario: producto.precio_actual,
    descuento: null,
    tipo_descuento: 'porcentaje',
    historial_descuentos: []
  }
  carrito.value.push(item)
}

const aumentarCantidad = (index) => {
  carrito.value[index].cantidad++
}

const disminuirCantidad = (index) => {
  if (carrito.value[index].cantidad > 1) {
    carrito.value[index].cantidad--
  }
}

const eliminarItem = (index) => {
  carrito.value.splice(index, 1)
}

const aplicarDescuento = async (item) => {
  try {
    // Mock the discount application client-side for demo purposes
    // In a real application, this would call your backend API
    console.log('Aplicando descuento a:', item)
    
    // Simulate successful discount application
    const descuentoAplicado = {
      tipo: item.tipo_descuento || 'porcentaje',
      valor: parseFloat(item.descuento || 10),
      fecha: new Date().toISOString(),
      usuario: usePage().props.auth.user?.name || 'Usuario'
    }
    
    // Update item with discount
    item.descuento = descuentoAplicado.valor
    item.tipo_descuento = descuentoAplicado.tipo
    
    // Calculate new price after discount
    const descuentoValor = calcularDescuento(item)
    const precioOriginal = item.precio || item.precio_unitario || 0
    item.precio_unitario = precioOriginal - descuentoValor
    
    // Add to discount history if it exists
    if (!item.historial_descuentos) {
      item.historial_descuentos = []
    }
    item.historial_descuentos.push(descuentoAplicado)
    
    console.log('Descuento aplicado correctamente:', descuentoAplicado)
    return true
  } catch (error) {
    console.error('Error al aplicar descuento:', error)
    return false
  }
}

// Helper para formatear precios con 2 decimales
const formatPrice = (price) => {
  return Number(price).toFixed(2)
}

const calcularDescuento = (item) => {
  if (!item.descuento) return 0
  
  if (item.tipo_descuento === 'porcentaje') {
    return item.producto.precio_actual * (item.descuento / 100)
  }
  return item.descuento
}

const finalizarVenta = () => {
  try {
    // Validar carrito no vacío
    if (!carrito.value.length) {
      alert('Debe agregar productos al carrito antes de finalizar la venta.')
      return
    }

    // Crear objeto de datos para enviar (solo para mostrar en la consola)
    const ventaData = {
      detalles: carrito.value.map(item => ({
        producto_id: item.producto.id,
        cantidad: item.cantidad,
        precio_unitario: item.precio_unitario,
        descuento: item.descuento || 0,
        tipo_descuento: item.tipo_descuento || 'monto'
      })),
      total: subtotal.value,
      fecha: new Date().toISOString()
    }

    console.log('Datos de la venta:', ventaData)
    
    // Mostrar mensaje de éxito
    alert('Venta finalizada con éxito')
    
    // Limpiar el carrito
    carrito.value = []
  } catch (error) {
    console.error('Error en finalizarVenta:', error)
    alert('Ocurrió un error inesperado. Por favor, inténtelo nuevamente.')
  }
}
</script>
