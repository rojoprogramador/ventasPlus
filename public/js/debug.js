// Guardar en public/js/debug.js
(function() {
    // Función para imprimir mensajes de depuración
    function debug(message, data = null) {
        const timestamp = new Date().toISOString().substr(11, 8);
        console.log(`[${timestamp}] ${message}`, data || '');
    }

    // Verificar que los scripts principales están cargados
    debug('Inicio de depuración de JavaScript');

    // Verificar que la API Fetch está disponible
    if (window.fetch) {
        debug('✅ Fetch API disponible');
    } else {
        debug('❌ Fetch API no disponible');
    }

    // Verificar que axios está disponible
    if (window.axios) {
        debug('✅ Axios disponible', window.axios.defaults.headers.common);
    } else {
        debug('❌ Axios no disponible');
    }

    // Verificar que Vue está disponible
    if (window.Vue) {
        debug('✅ Vue disponible', window.Vue.version);
    } else {
        debug('⚠️ Vue no está explícitamente en el ámbito global (normal en Vue 3)');
    }

    // Verificar el CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        debug('✅ CSRF Token encontrado', csrfToken.content);
    } else {
        debug('❌ CSRF Token no encontrado');
    }

    // Verificar el estado de la sesión de Laravel
    const laravelSession = document.cookie.includes('laravel_session');
    debug(laravelSession ? '✅ Cookie de sesión Laravel encontrada' : '❌ Cookie de sesión Laravel no encontrada');

    // Verificar el estado de XSRF token
    const xsrfToken = document.cookie.includes('XSRF-TOKEN');
    debug(xsrfToken ? '✅ Cookie XSRF-TOKEN encontrada' : '❌ Cookie XSRF-TOKEN no encontrada');

    // Monitorear solicitudes de red
    if (window.axios) {
        window.axios.interceptors.request.use(
            config => {
                debug('🔄 Solicitud saliente', {
                    url: config.url,
                    method: config.method,
                    headers: config.headers,
                    data: config.data
                });
                return config;
            },
            error => {
                debug('❌ Error en solicitud', error);
                return Promise.reject(error);
            }
        );

        window.axios.interceptors.response.use(
            response => {
                debug('✅ Respuesta recibida', {
                    url: response.config.url,
                    status: response.status,
                    data: response.data
                });
                return response;
            },
            error => {
                debug('❌ Error en respuesta', {
                    url: error.config?.url,
                    status: error.response?.status,
                    statusText: error.response?.statusText,
                    data: error.response?.data
                });
                return Promise.reject(error);
            }
        );
    }

    // Monitorear problemas de autofocus
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            const autofocusElements = document.querySelectorAll('[autofocus]');
            if (autofocusElements.length > 1) {
                debug('⚠️ Múltiples elementos con autofocus detectados', autofocusElements);
            } else if (autofocusElements.length === 1) {
                debug('✅ Un solo elemento con autofocus', autofocusElements[0]);
            }
        }, 500);
    });

    // Insertar mensaje de depuración en la página
    document.addEventListener('DOMContentLoaded', () => {
        const debugElement = document.createElement('div');
        debugElement.style.position = 'fixed';
        debugElement.style.bottom = '10px';
        debugElement.style.right = '10px';
        debugElement.style.padding = '5px 10px';
        debugElement.style.background = 'rgba(0, 0, 0, 0.7)';
        debugElement.style.color = 'white';
        debugElement.style.borderRadius = '5px';
        debugElement.style.fontSize = '12px';
        debugElement.style.fontFamily = 'monospace';
        debugElement.style.zIndex = '9999';
        debugElement.textContent = '🛠️ Modo Depuración Activo';
        debugElement.title = 'Abra la consola del navegador para ver los mensajes de depuración';
        document.body.appendChild(debugElement);
    });

    debug('Depuración de JavaScript inicializada');
})();
