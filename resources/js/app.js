import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { Link } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

// Definir el nombre de la aplicación
const appName = 'Ventas Plus';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        // Configurar manejo global de errores para promesas no capturadas
        window.addEventListener('unhandledrejection', event => {
            console.error('Unhandled Promise Rejection:', event.reason);
            // Evitar que el error no capturado cause problemas en la UI
            event.preventDefault();
            
            // Check if the error is CSRF related and refresh if needed
            if (event.reason && event.reason.response && event.reason.response.status === 419) {
                console.error('CSRF token mismatch detected. Reloading page...');
                window.location.reload();
            }
        });

        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component('Link', Link);

        // Manejo global de errores de Vue
        app.config.errorHandler = (error, vm, info) => {
            console.error('Vue Error Handler:', error);
            console.error('Component:', vm);
            console.error('Info:', info);
        };

        // App mounted
        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
        showSpinner: true,
    },
});
