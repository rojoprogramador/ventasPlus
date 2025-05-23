import _ from 'lodash';
window._ = _;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Mejorar la detección y configuración del CSRF token
function setupCsrfToken() {
    let token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        console.info('CSRF token successfully loaded: ', token.content.substring(0, 8) + '...');
        return true;
    } else {
        console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
        return false;
    }
}

// Ejecutar configuración inicial
let csrfConfigured = setupCsrfToken();

// Si no se pudo configurar el token, intentarlo de nuevo cuando el DOM esté completamente cargado
if (!csrfConfigured) {
    document.addEventListener('DOMContentLoaded', () => {
        csrfConfigured = setupCsrfToken();
        if (!csrfConfigured) {
            console.warn('CSRF token still not found after DOM loaded. Some requests may fail with 419 errors.');
        }
    });
}

// Asegurar que las cookies se envían con cada solicitud (importante para CSRF y autenticación)
window.axios.defaults.withCredentials = true;

// Manejar errores de red globalmente con mejor gestión de errores CSRF
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 419) {
            // Error de CSRF token expirado
            console.error('CSRF token mismatch (419 error). Attempting to refresh the token...');
            
            // Guardar la URL actual para volver después de recargar
            const currentPath = window.location.pathname + window.location.search;
            sessionStorage.setItem('redirect_after_refresh', currentPath);
            
            // Intentar obtener un nuevo token mediante una solicitud GET
            axios.get('/sanctum/csrf-cookie')
                .then(() => {
                    console.info('New CSRF token obtained. Reconfiguring...');
                    setupCsrfToken();
                    
                    // Si la ruta actual no es sensible, intentar repetir la operación
                    if (!currentPath.includes('/login') && !currentPath.includes('/logout')) {
                        console.info('Redirecting back to the original page');
                        window.location.href = currentPath;
                    } else {
                        // Para rutas sensibles, mejor recargar toda la página
                        window.location.reload();
                    }
                })
                .catch(refreshError => {
                    console.error('Failed to refresh CSRF token:', refreshError);
                    // Si falla el refresco del token, recargar la página como último recurso
                    window.location.reload();
                });
        }
        else if (error.response && error.response.status === 401) {
            // Error de autenticación
            console.error('Authentication error (401). You need to log in again.');
            
            // Guardar la URL actual para volver después del login
            const currentPath = window.location.pathname + window.location.search;
            sessionStorage.setItem('redirect_after_login', currentPath);
            
            // Redirigir al login si es necesario
            if (window.location.pathname !== '/login') {
                window.location.href = '/login';
            }
        }
        else if (!error.response) {
            // Error de red o servidor no disponible
            console.error('Network error or server unavailable:', error);
        }
        return Promise.reject(error);
    }
);

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
