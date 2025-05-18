<template>
  <div class="container mx-auto py-4">
    <div class="flex justify-between items-center mb-4">
      <div class="flex items-center">
        <Link
          :href="route('dashboard')"
          class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 flex items-center mr-4"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
          </svg>
          Volver al Dashboard
        </Link>
        <h1 class="text-2xl font-bold">Nueva Venta</h1>
      </div>
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

  <!-- Modal de Comprobante -->
  <div v-if="mostrarModalComprobante" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-10">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
      <h2 class="text-xl font-bold mb-4">Comprobante de Compra</h2>
      
      <div class="mb-4">
        <p class="mb-2">¿Cómo desea recibir su comprobante?</p>
        <div class="space-y-2">
          <button @click="imprimirComprobante" class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Imprimir comprobante
          </button>
          <button @click="mostrarEnvioEmail = true" class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
            Enviar por correo electrónico
          </button>
          <button @click="omitirComprobante" class="w-full px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
            No deseo comprobante
          </button>
        </div>
      </div>

      <!-- Sección de envío por email -->
      <div v-if="mostrarEnvioEmail" class="mt-4 border-t pt-4">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
          <input 
            v-model="emailCliente" 
            type="email" 
            class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500"
            placeholder="correo@ejemplo.com"
          />
        </div>
        <div class="flex space-x-3">
          <button @click="enviarComprobantePorEmail" class="flex-1 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Enviar
          </button>
          <button @click="mostrarEnvioEmail = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
            Cancelar
          </button>
        </div>
      </div>

      <!-- Sección de pago en efectivo -->
      <div v-if="metodoPago === 'efectivo' && !mostrarEnvioEmail" class="mt-4 border-t pt-4">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Monto entregado</label>
          <input 
            v-model.number="montoEntregado" 
            type="number" 
            class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500"
            placeholder="0.00"
          />
        </div>
        <div v-if="montoEntregado >= total" class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Cambio a devolver</label>
          <p class="text-lg font-bold">${{ formatPrice(montoEntregado - total) }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import axios from 'axios'

const productos = ref([])
const carrito = ref([])
const busqueda = ref('')

// Variables para el comprobante
const mostrarModalComprobante = ref(false)
const mostrarEnvioEmail = ref(false)
const emailCliente = ref('')
const metodoPago = ref('efectivo') // Opciones: 'efectivo', 'tarjeta', 'transferencia'
const montoEntregado = ref(0)
const numeroVenta = ref(0)
const ventaData = ref(null)

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

const finalizarVenta = async () => {
  try {
    // Validar carrito no vacío
    if (!carrito.value.length) {
      alert('Debe agregar productos al carrito antes de finalizar la venta.')
      return
    }

    // Generar número único de venta (esto normalmente vendría del backend)
    numeroVenta.value = Date.now().toString().slice(-8)

    // Crear objeto de datos para enviar
    ventaData.value = {
      numero_venta: numeroVenta.value,
      cajero: usePage().props.auth.user?.name || 'Usuario',
      detalles: carrito.value.map(item => ({
        producto_id: item.producto.id,
        codigo: item.producto.codigo_barras,
        nombre: item.producto.nombre,
        cantidad: item.cantidad,
        precio_unitario: item.precio_unitario,
        descuento: item.descuento || 0,
        tipo_descuento: item.tipo_descuento || 'monto',
        subtotal: item.precio_unitario * item.cantidad
      })),
      subtotal: subtotal.value,
      descuentos: descuentosTotales.value,
      total: total.value,
      fecha: new Date().toISOString(),
      metodo_pago: metodoPago.value
    }

    console.log('Datos de la venta:', ventaData.value)
    
    // Guardar la venta en el backend
    try {
      const response = await axios.post(route('ventas.guardar'), ventaData.value)
      console.log('Venta guardada correctamente:', response.data)
      
      // Si la venta se guardó exitosamente, mostrar el modal de comprobante
      if (response.data.success) {
        // Almacenar el ID de la venta generado por el backend
        ventaData.value.venta_id = response.data.venta_id
        
        // Mostrar modal de comprobante
        mostrarModalComprobante.value = true
        
        // Si el cliente está registrado, autocompletar el email
        if (usePage().props.auth?.cliente?.email) {
          emailCliente.value = usePage().props.auth.cliente.email
        }
      } else {
        throw new Error(response.data.error || 'Error desconocido al guardar la venta')
      }
    } catch (apiError) {
      console.error('Error al guardar la venta en el backend:', apiError)
      
      // Mostrar mensaje de error específico si está disponible
      if (apiError.response && apiError.response.data && apiError.response.data.error) {
        alert('Error: ' + apiError.response.data.error)
      } else {
        alert('Error al guardar la venta. Por favor, verifique si hay una caja abierta e intente nuevamente.')
      }
    }
  } catch (error) {
    console.error('Error en finalizarVenta:', error)
    alert('Ocurrió un error inesperado. Por favor, inténtelo nuevamente.')
  }
}

// Métodos para el manejo de comprobantes
const generarComprobante = () => {
  // Esta función generaría el HTML del comprobante
  // Este es un ejemplo simplificado
  const fecha = new Date().toLocaleString()
  
  let contenido = `
    <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto;">
      <h1 style="text-align: center;">VentasPlus</h1>
      <h2 style="text-align: center;">Comprobante de Compra</h2>
      <div style="border-top: 1px solid #eee; border-bottom: 1px solid #eee; padding: 10px 0; margin: 20px 0;">
        <p><strong>No. Venta:</strong> ${ventaData.value.numero_venta}</p>
        <p><strong>Fecha:</strong> ${fecha}</p>
        <p><strong>Cajero:</strong> ${ventaData.value.cajero}</p>
      </div>
      <table style="width: 100%; border-collapse: collapse;">
        <thead>
          <tr style="background-color: #f3f4f6;">
            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #ddd;">Producto</th>
            <th style="padding: 8px; text-align: right; border-bottom: 1px solid #ddd;">Precio Unit.</th>
            <th style="padding: 8px; text-align: center; border-bottom: 1px solid #ddd;">Cant.</th>
            <th style="padding: 8px; text-align: right; border-bottom: 1px solid #ddd;">Subtotal</th>
          </tr>
        </thead>
        <tbody>
  `
  
  ventaData.value.detalles.forEach(item => {
    contenido += `
      <tr>
        <td style="padding: 8px; text-align: left; border-bottom: 1px solid #ddd;">${item.nombre}</td>
        <td style="padding: 8px; text-align: right; border-bottom: 1px solid #ddd;">$${formatPrice(item.precio_unitario)}</td>
        <td style="padding: 8px; text-align: center; border-bottom: 1px solid #ddd;">${item.cantidad}</td>
        <td style="padding: 8px; text-align: right; border-bottom: 1px solid #ddd;">$${formatPrice(item.subtotal)}</td>
      </tr>
    `
  })
  
  contenido += `
        </tbody>
      </table>
      <div style="margin-top: 20px; text-align: right;">
        <p><strong>Subtotal:</strong> $${formatPrice(ventaData.value.subtotal)}</p>
        <p><strong>Descuentos:</strong> $${formatPrice(ventaData.value.descuentos)}</p>
        <p style="font-size: 1.2em;"><strong>Total:</strong> $${formatPrice(ventaData.value.total)}</p>
  `
  
  // Si fue pago en efectivo, mostrar el monto entregado y el cambio
  if (metodoPago.value === 'efectivo' && montoEntregado.value > 0) {
    const cambio = montoEntregado.value - ventaData.value.total
    contenido += `
      <p><strong>Monto entregado:</strong> $${formatPrice(montoEntregado.value)}</p>
      <p><strong>Cambio:</strong> $${formatPrice(cambio)}</p>
    `
  }
  
  contenido += `
      </div>
      <div style="margin-top: 40px; text-align: center; font-size: 0.9em; color: #666;">
        <p>Gracias por su compra</p>
        <p>Para cualquier duda o aclaración, conserve este comprobante</p>
      </div>
    </div>
  `
  
  return contenido
}

const imprimirComprobante = async () => {
  try {
    console.log('Iniciando generación de comprobante...')
    
    // Usar axios para generar el PDF directamente
    const datosCompletosVenta = {
      ...ventaData.value,
      monto_entregado: montoEntregado.value || 0
    }
    
    console.log('Datos enviados al servidor:', JSON.stringify(datosCompletosVenta))
    
    // Usar axios para hacer la petición con el token CSRF automáticamente incluido
    const response = await axios.post(route('ventas.comprobante.generar'), datosCompletosVenta, {
      responseType: 'blob', // Importante para recibir datos binarios (PDF)
    })
    
    console.log('Respuesta recibida:', response.status, response.headers)
    
    // Verificar si la respuesta es un error en formato JSON (no un PDF)
    if (response.data.type && response.data.type.includes('application/json')) {
      // Convertir el blob a texto para leer el mensaje de error
      const errorText = await new Response(response.data).text()
      console.error('Error en formato JSON:', errorText)
      const errorJson = JSON.parse(errorText)
      throw new Error(errorJson.error || 'Error desconocido al generar el PDF')
    }
    
    // Crear blob y URL para descargar el PDF directamente
    const blob = new Blob([response.data], { type: 'application/pdf' })
    const fileURL = window.URL.createObjectURL(blob)
    
    // Crear un enlace de descarga y hacer clic en él automáticamente
    const link = document.createElement('a')
    link.href = fileURL
    link.setAttribute('download', `comprobante_${ventaData.value.numero_venta}.pdf`)
    document.body.appendChild(link)
    link.click()
    
    // Limpiar
    document.body.removeChild(link)
    window.URL.revokeObjectURL(fileURL) // Liberar memoria
    
    // Limpiar el carrito después de imprimir
    finalizarProceso()
  } catch (error) {
    console.error('Error al imprimir comprobante:', error)
    
    // Mostrar mensaje de error más detallado
    let errorMessage = 'Ocurrió un error al intentar imprimir.'
    
    if (error.response) {
      console.error('Error de respuesta:', error.response.data, error.response.status)
      if (error.response.data && typeof error.response.data === 'object') {
        errorMessage += ' Detalles: ' + JSON.stringify(error.response.data)
      }
    } else if (error.request) {
      console.error('Error de solicitud:', error.request)
      errorMessage += ' No se recibió respuesta del servidor.'
    } else {
      console.error('Error:', error.message)
      errorMessage += ' ' + error.message
    }
    
    alert(errorMessage + ' Por favor, inténtelo nuevamente.')
  }
}

const enviarComprobantePorEmail = async () => {
  try {
    if (!emailCliente.value || !emailCliente.value.includes('@')) {
      alert('Por favor, introduzca un correo electrónico válido.')
      return
    }
    
    // Preparar los datos con el email
    const datosComprobante = {
      ...ventaData.value,
      email: emailCliente.value,
      monto_entregado: montoEntregado.value || 0
    }
    
    // Mostrar mensaje de espera
    alert(`El comprobante será enviado a ${emailCliente.value} en breve.`)
    
    // Llamar a la API para enviar el comprobante por email
    const response = await axios.post(route('ventas.comprobante.email'), datosComprobante)
    
    console.log('Comprobante enviado por email:', response.data)
    
    // Limpiar después de enviar
    finalizarProceso()
  } catch (error) {
    console.error('Error al enviar comprobante por email:', error)
    alert('Ocurrió un error al enviar el correo. Por favor, inténtelo nuevamente.')
  }
}

const omitirComprobante = () => {
  // Simplemente cerramos el modal y limpiamos
  finalizarProceso()
}

const finalizarProceso = () => {
  // Cerrar modal y limpiar
  mostrarModalComprobante.value = false
  mostrarEnvioEmail.value = false
  emailCliente.value = ''
  montoEntregado.value = 0
  ventaData.value = null
  
  // Limpiar el carrito
  carrito.value = []
  
  // Mostrar mensaje de éxito
  alert('Venta finalizada con éxito')
}
</script>
