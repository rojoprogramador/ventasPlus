<template>
    <AppLayout title="Crear Producto">
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Crear Nuevo Producto
                </h2>
                <Link
                    :href="route('productos.index')"
                    class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                >
                    Volver a Productos
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Código -->
                            <div>
                                <Label for="codigo" value="Código de Barras" />
                                <Input
                                    id="codigo"
                                    type="text"
                                    v-model="form.codigo"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.codigo" class="mt-2" />
                            </div>

                            <!-- Nombre -->
                            <div>
                                <Label for="nombre" value="Nombre del Producto" />
                                <Input
                                    id="nombre"
                                    type="text"
                                    v-model="form.nombre"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.nombre" class="mt-2" />
                            </div>                            <!-- Descripción -->
                            <div>
                                <Label for="descripcion" value="Descripción" />
                                <Input
                                    id="descripcion"
                                    type="text"
                                    v-model="form.descripcion"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.descripcion" class="mt-2" />
                            </div>

                            <!-- Precio de Compra -->
                            <div>
                                <Label for="precio_compra" value="Precio de Compra" />
                                <Input
                                    id="precio_compra"
                                    type="number"
                                    step="0.01"
                                    v-model="form.precio_compra"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.precio_compra" class="mt-2" />
                            </div>

                            <!-- Precio de Venta -->
                            <div>
                                <Label for="precio_venta" value="Precio de Venta" />
                                <Input
                                    id="precio_venta"
                                    type="number"
                                    step="0.01"
                                    v-model="form.precio_venta"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.precio_venta" class="mt-2" />
                            </div>

                            <!-- Stock -->
                            <div>
                                <Label for="stock" value="Stock Inicial" />
                                <Input
                                    id="stock"
                                    type="number"
                                    v-model="form.stock"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.stock" class="mt-2" />
                            </div>

                            <!-- Imagen -->
                            <div>
                                <Label for="imagen" value="Imagen del Producto (opcional)" />
                                <Input
                                    id="imagen"
                                    type="file"
                                    @input="form.imagen = $event.target.files[0]"
                                    class="mt-1 block w-full"
                                    accept="image/*"
                                />
                                <InputError :message="form.errors.imagen" class="mt-2" />
                            </div>

                            <!-- Botón de Submit -->
                            <div class="flex items-center justify-end mt-4">
                                <Button
                                    class="ml-4"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Crear Producto
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import Label from '@/Components/InputLabel.vue';
import Input from '@/Components/TextInput.vue';
import Button from '@/Components/PrimaryButton.vue';

import InputError from '@/Components/InputError.vue';

const form = useForm({
    codigo: '',
    nombre: '',
    descripcion: '',
    precio_compra: '',
    precio_venta: '',
    stock: '',
    imagen: null
});

const submit = () => {
    form.post(route('productos.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>