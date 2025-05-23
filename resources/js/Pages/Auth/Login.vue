<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const loading = ref(false);
const errorMessage = ref('');
const emailInput = ref(null);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// Enfoca el campo de correo después de que el componente se haya montado
onMounted(() => {
    // Pequeño retraso para asegurar que el DOM esté completamente cargado
    setTimeout(() => {
        if (emailInput.value) {
            emailInput.value.focus();
        }
    }, 100);
});

const submit = () => {
    loading.value = true;
    errorMessage.value = '';
    
    // Verificar CSRF token antes de enviar
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        errorMessage.value = 'Error de seguridad: CSRF token no encontrado. Por favor, recargue la página.';
        loading.value = false;
        return;
    }
    
    try {
        form.post(route('login'), {
            preserveScroll: true,
            onSuccess: () => {
                // Manejar éxito explícitamente
                form.reset('password');
                console.log('Login success, redirecting...');
            },
            onError: (errors) => {
                // Manejar errores específicos
                if (errors.email) {
                    errorMessage.value = errors.email;
                } else if (errors.password) {
                    errorMessage.value = errors.password;
                } else {
                    errorMessage.value = 'Ocurrió un error durante el inicio de sesión. Verifique sus credenciales.';
                }
                console.error('Login errors:', errors);
            },
            onFinish: () => {
                loading.value = false;
            }
        });
    } catch (err) {
        // Capturar cualquier error inesperado durante la solicitud
        console.error('Unexpected error during login:', err);
        errorMessage.value = 'Error inesperado. Por favor, inténtelo de nuevo más tarde.';
        loading.value = false;
    }
};
</script>

<template>
    <GuestLayout>
        <Head title="Login - VentaPlus" />

        <div class="text-center mb-8">
            <h1 class="text-5xl font-bold text-indigo-600 dark:text-indigo-400">VentaPlus</h1>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">Sistema de Gestión de Ventas</p>
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <div v-if="errorMessage" class="mb-4 font-medium text-sm text-red-600">
            {{ errorMessage }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Correo electrónico" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    ref="emailInput"
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Contraseña" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Recordarme</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    ¿Olvidaste tu contraseña?
                </Link>

                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing || loading }" :disabled="form.processing || loading">
                    <span v-if="loading">Procesando...</span>
                    <span v-else>Iniciar sesión</span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
