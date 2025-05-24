<template>
  <div 
    @click="$emit('select', product)"
    class="product-card cursor-pointer bg-white rounded-lg shadow-md p-4 transition-transform hover:scale-105 hover:shadow-lg"
  >
    <div class="overflow-hidden h-32 mb-3 bg-gray-100 rounded flex justify-center items-center">
      <img 
        v-if="product.imagen" 
        :src="'/storage/' + product.imagen" 
        :alt="product.nombre" 
        class="object-contain h-full"
      >
      <div v-else class="text-gray-400 text-center">
        <i class="fas fa-image text-3xl mb-1"></i>
        <p>Sin imagen</p>
      </div>
    </div>
    <h3 class="text-lg font-semibold text-gray-800 mb-1 truncate">{{ product.nombre }}</h3>
    <p class="text-gray-600 text-sm mb-2 truncate">{{ product.codigo }}</p>
    <div class="flex justify-between items-center">
      <span class="font-bold text-blue-600">
        <span v-if="product.precio_promocional" class="flex flex-col">
          <span class="text-lg">{{ formatCurrency(product.precio_promocional) }}</span>
          <span class="text-xs line-through text-gray-500">{{ formatCurrency(product.precio_venta) }}</span>
        </span>
        <span v-else class="text-lg">{{ formatCurrency(product.precio_venta) }}</span>
      </span>
      <span class="text-sm" :class="product.stock > 0 ? 'text-green-600' : 'text-red-600'">
        {{ product.stock }} disponibles
      </span>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    product: {
      type: Object,
      required: true
    }
  },
  methods: {
    formatCurrency(value) {
      return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
      }).format(value);
    }
  }
}
</script>

<style scoped>
.product-card {
  width: 200px;
  height: 240px;
  display: flex;
  flex-direction: column;
}
</style>
