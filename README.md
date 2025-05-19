# VentasPlus - Sistema POS

Sistema de Punto de Venta (POS) desarrollado con Laravel y Vue.js, diseñado para gestionar ventas, inventario, clientes, cierre de caja y reportes de manera eficiente.

## Inicio Rápido

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/ventasPlus.git
cd ventasPlus

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Crear y configurar base de datos en PostgreSQL
createdb ventasplus

# 5. Configurar la base de datos en el archivo .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=ventasplus
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_contraseña

# 6. Ejecutar migraciones y seeders
php artisan migrate:fresh --seed

# 7. Compilar assets
npm run build

# 8. Iniciar servidor de desarrollo
php artisan serve

# 9. En otra terminal, iniciar Vite para desarrollo frontend
npm run dev
```

Visita http://localhost:8000 en tu navegador.

## Stack Tecnológico

- **Backend:** Laravel 10.x
- **Frontend:** Vue.js + Inertia.js (via Laravel Breeze)
- **CSS:** Tailwind CSS
- **Base de datos:** PostgreSQL 14
- **Autenticación:** Laravel Breeze
- **Testing:** Pest PHP
- **Assets:** Vite
- **PDF Generation:** barryvdh/laravel-dompdf
- **Email:** Laravel Mail

## Requisitos

- PHP >= 8.1
- Composer
- Node.js y NPM
- PostgreSQL >= 14
- Git

## Instalación

1. Clonar el repositorio:
```bash
git clone <url-del-repositorio>
cd ventaplus
```

2. Instalar dependencias de PHP:
```bash
composer install
composer require barryvdh/laravel-dompdf
```

3. Instalar dependencias de Node.js:
```bash
npm install
npm run build
```

4. Configurar el entorno:
```bash
cp .env.example .env
php artisan key:generate
```

5. Configurar la base de datos en el archivo `.env`:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ventasplus
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

6. Ejecutar migraciones y seeders:
```bash
php artisan migrate:fresh --seed
```

7. Verificar la instalación:
```bash
php artisan test
```

## Usuarios por defecto

El sistema viene con tres usuarios predefinidos:

1. **Administrador**
   - Email: admin@ventaplus.com
   - Password: admin123
   - Rol: Administrador
   - Acceso a todas las funciones del sistema

2. **Vendedor**
   - Email: vendedor@ventaplus.com
   - Password: vendedor123
   - Rol: Vendedor
   - Acceso a: gestión de clientes, consulta de productos

3. **Cajero**
   - Email: cajero@ventaplus.com
   - Password: cajero123
   - Rol: Cajero
   - Acceso a: registro de ventas, consulta de productos

## Características

### Gestión de Caja

#### Funcionalidades Principales
- Apertura de caja con monto inicial
- Registro de movimientos (entradas/salidas)
- Cierre de caja con validación de montos
- Reapertura de cajas (solo administradores)
- Generación de reportes detallados
- Historial de cierres anteriores

#### Detalles del Cierre de Caja
- Desglose de ventas por método de pago (efectivo, tarjeta, transferencia)
- Cálculo automático de saldo esperado
- Comparación de monto esperado vs. real
- Justificación obligatoria de diferencias
- Impresión de reportes de cierre

### Registro de Ventas (POS)

#### Funcionalidades Principales
- Interfaz intuitiva de registro de ventas
- Búsqueda rápida de productos por nombre o código de barras
- Agregado de múltiples productos a la venta
- Control automático de inventario
- Selección de diferentes métodos de pago (efectivo, tarjeta, transferencia)

### Generación de Comprobantes

#### Funcionalidades
- Generación automática de comprobantes en formato PDF
- Opciones para imprimir o enviar por email
- Posibilidad de omitir el comprobante
- Soporte para pagos en efectivo con cálculo de cambio
- Vista previa del PDF mediante stream
- Plantillas HTML personalizadas para el comprobante y para correos
- Cálculo automático de cambio para pagos en efectivo
- Generación de comprobantes de venta
- Vista previa de venta antes de finalizar
- Opción para cancelar ventas
- Validaciones para evitar errores (stock disponible, montos correctos)

#### Proceso de Venta
1. El cajero busca productos por nombre o código
2. Agrega productos al carrito de la venta
3. Puede ajustar cantidades o eliminar productos
4. Selecciona el método de pago
5. En caso de pago en efectivo, registra el monto recibido
6. Finaliza la venta
7. El sistema genera automáticamente un comprobante
8. El inventario se actualiza en tiempo real

#### Beneficios
- Reduce tiempos de espera para clientes
- Minimiza errores humanos
- Simplifica el proceso de cierre de caja
- Mejora control de inventario
- Facilita seguimiento de ventas

## Guía de Uso: Registro de Ventas

El módulo de registro de ventas ha sido diseñado para ser intuitivo y eficiente, cumpliendo con la historia de usuario HU-01 "Como Cajero, necesito registrar una venta rápidamente para agilizar la atención al cliente y reducir tiempos de espera".

### Acceso al Módulo

1. Inicie sesión con un usuario que tenga rol de Cajero o Administrador.
2. En el menú principal, haga clic en "Registro de Ventas".

### Búsqueda de Productos

1. Utilice el campo de búsqueda para encontrar productos por nombre o código de barras.
2. Presione Enter o haga clic en el botón "Buscar".
3. Los resultados aparecerán en una tabla debajo del campo de búsqueda.
4. Para agregar un producto a la venta, haga clic en el botón "Agregar" junto al producto deseado.

### Gestión del Carrito

1. Los productos agregados aparecerán en la sección "Productos en la venta".
2. Para cada producto puede:
   - Ajustar la cantidad utilizando los botones + y - o ingresando un valor directamente.
   - Eliminar el producto de la venta haciendo clic en "Eliminar".
3. El sistema automáticamente recalcula el subtotal de cada producto y el total de la venta.

### Proceso de Pago

1. Seleccione el método de pago (Efectivo, Tarjeta o Transferencia).
2. Si selecciona Efectivo:
   - Ingrese el monto recibido del cliente.
   - El sistema calculará automáticamente el cambio a devolver.
3. El botón "Finalizar Venta" se habilitará una vez que se cumplan las condiciones necesarias (productos agregados y, en caso de efectivo, monto suficiente).

### Finalización de la Venta

1. Al hacer clic en "Finalizar Venta", se procesará la transacción.
2. Si todo es correcto, se mostrará un mensaje de éxito con la opción de:
   - Ver el comprobante de venta
   - Iniciar una nueva venta
3. Al ver el comprobante, se mostrará un resumen de la venta con todos los productos, cantidades, precios y el total.

### Cancelación de Venta

1. En cualquier momento durante el proceso, puede hacer clic en "Cancelar Venta".
2. Se solicitará confirmación antes de cancelar la venta.
3. Al confirmar, se descartarán todos los productos agregados.

## Resolución de Problemas Comunes

- **No se encuentran productos**: Verifique que esté ingresando correctamente el nombre o código. Si persiste, consulte con el administrador para confirmar que el producto existe en el sistema.
- **Error al finalizar venta**: Asegúrese que hay stock disponible para todos los productos y que el monto recibido (en caso de pago en efectivo) sea igual o mayor al total de la venta.
- **Cantidad excedida**: El sistema no permitirá agregar más unidades de un producto de las que hay en stock.

### Gestión de Usuarios y Permisos

#### Funcionalidades Principales
- Creación y edición de usuarios
- Asignación de roles predefinidos
- Personalización de permisos individuales
- Gestión de contraseñas seguras
- Desactivación temporal de usuarios
- Historial de auditoría de cambios

#### Roles y Permisos
- Sistema jerárquico de roles
- Permisos heredados del rol
- Permisos individuales por usuario
- Control granular de acceso

#### Seguridad
- Políticas de contraseñas robustas
  - Mínimo 8 caracteres
  - Al menos una mayúscula
  - Al menos una minúscula
  - Al menos un número
  - Al menos un carácter especial
- Protección contra ataques de fuerza bruta
- Registro de actividades de usuario

#### Interfaz de Usuario
- Dashboard intuitivo
- Gestión visual de permisos
- Feedback en tiempo real
- Diseño responsive

### Otras Características
- Gestión de ventas con interfaz intuitiva
- Control de inventario en tiempo real
- Gestión de clientes con historial de compras
- Reportes y estadísticas detallados
- Control de cajas con validación de montos
- Sistema de cotizaciones
- Comprobantes de compra personalizables
- Navegación sencilla con botones para volver al dashboard

### Comprobantes de Compra

#### Funcionalidades
- Generación automática de comprobantes al finalizar una venta
- Opciones para imprimir o enviar por correo electrónico
- Posibilidad de omitir la generación del comprobante
- Formato PDF profesional con toda la información de la venta
- Reimpresión de comprobantes para ventas anteriores
- Autocompletado de correo para clientes registrados
- En pagos en efectivo, se muestra el cambio a devolver

#### Información del Comprobante
- Número único de venta
- Fecha y hora de la transacción
- Información del cajero
- Lista detallada de productos comprados
- Precios unitarios y cantidades
- Subtotal de la compra
- Descuentos aplicados
- Total final
- En pagos en efectivo: monto entregado y cambio

#### Librerías y Configuración

##### Generación de PDFs
Se utiliza la librería `barryvdh/laravel-dompdf` para generar comprobantes en formato PDF:

```bash
composer require barryvdh/laravel-dompdf
```

Esta librería permite:
- Generar PDFs a partir de plantillas Blade
- Personalizar cabeceras, pie de página y estilos
- Entregar los PDFs como descargas o visualización en el navegador
- Guardar los PDFs en el servidor

##### Envío de Correos Electrónicos
Para el envío de comprobantes por correo, se utiliza el sistema de correo de Laravel. Es necesario configurar las credenciales SMTP en el archivo `.env`:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.ejemplo.com
MAIL_PORT=587
MAIL_USERNAME=tu_usuario
MAIL_PASSWORD=tu_contraseña
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=comprobantes@ventasplus.com
MAIL_FROM_NAME="VentasPlus"
```

##### Almacenamiento Temporal
Los comprobantes generados para envío por correo se almacenan temporalmente en:
```
storage/app/public/comprobantes/
```

Asegúrate de que esta carpeta existe y tiene permisos de escritura.

## Tests

### Tests de Usuario y Permisos

Ejecutar los tests específicos del módulo de usuarios:

```bash
php artisan test tests/Feature/UserControllerTest.php
```

Los tests cubren:
- Listado de usuarios
- Creación de usuarios
- Actualización de datos
- Gestión de permisos individuales
- Validación de contraseñas
- Control de acceso

## Estructura del Proyecto

```
ventaplus/
├── app/
│   ├── Http/Controllers/    # Controladores
│   ├── Models/              # Modelos Eloquent
│   └── Providers/           # Proveedores de servicios
├── database/
│   ├── migrations/         # Migraciones de base de datos
│   └── seeders/           # Seeders para datos iniciales
├── resources/
│   ├── js/                # Componentes Vue.js
│   └── views/             # Vistas Blade
├── routes/                # Definición de rutas
└── tests/                # Tests automatizados
```

## Herramientas del Proyecto

### Artisan - La Interfaz de Comandos de Laravel

Artisan es la interfaz de línea de comandos incluida en Laravel. Proporciona comandos útiles para ayudarte mientras construyes tu aplicación. Para ver una lista de todos los comandos disponibles, ejecuta:

```bash
php artisan list
```
#### Comandos de Generación de Código
```bash
# Crear un nuevo controlador - Maneja las peticiones HTTP y la lógica de la aplicación
php artisan make:controller NombreController

# Crear un nuevo modelo con migración - Representa una tabla en la base de datos
# La opción -m crea automáticamente la migración asociada
php artisan make:model Nombre -m

# Crear un nuevo seeder - Para poblar la base de datos con datos iniciales
php artisan make:seeder NombreSeeder

# Crear un nuevo test - Para pruebas automatizadas
php artisan make:test NombreTest
```

#### Comandos de Inspección y Mantenimiento
```bash
# Listar todas las rutas registradas en la aplicación
php artisan route:list

# Limpiar diferentes tipos de caché:
php artisan cache:clear    # Limpia el caché de la aplicación
php artisan config:clear   # Limpia el caché de configuración
php artisan view:clear     # Limpia el caché de vistas compiladas
```

### Migraciones - Control de Versiones para Base de Datos

Las migraciones son como un control de versiones para tu base de datos. Permiten que tu equipo modifique y comparta el esquema de la base de datos de manera sencilla. Son especialmente útiles cuando trabajas en equipo o necesitas mantener un historial de cambios en la estructura de la base de datos.

```bash
# Ejecutar migraciones pendientes
php artisan migrate

# Revertir última migración
php artisan migrate:rollback

# Revertir todas las migraciones
php artisan migrate:reset

# Revertir y volver a ejecutar migraciones
php artisan migrate:refresh

# Eliminar todas las tablas y ejecutar migraciones
php artisan migrate:fresh

# Ejecutar migraciones con seeders
php artisan migrate:fresh --seed
```

### NPM - Gestor de Paquetes de Node.js

NPM (Node Package Manager) se usa para gestionar las dependencias de frontend y compilar assets. En nuestro proyecto, lo usamos principalmente para:
- Gestionar dependencias de JavaScript
- Compilar archivos Vue.js
- Procesar CSS con Tailwind
- Ejecutar Vite para el desarrollo

```bash
# Instalar dependencias
npm install

# Compilar assets para desarrollo
npm run dev

# Compilar assets para producción
npm run build
```

### Testing - Pruebas Automatizadas

Utilizamos Pest PHP, un elegante framework de testing que hace que las pruebas sean más expresivas y fáciles de escribir. Las pruebas nos ayudan a:
- Verificar que el código funciona como se espera
- Detectar errores temprano
- Facilitar el mantenimiento y refactorización
- Documentar el comportamiento esperado

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar tests específicos
php artisan test --filter NombreTest

# Ejecutar tests con cobertura
php artisan test --coverage
```

## Laravel Breeze - Sistema de Autenticación

Laravel Breeze es un starter kit que proporciona una implementación mínima y simple de todas las características de autenticación de Laravel, incluyendo:

- Login
- Registro
- Recuperación de contraseña
- Verificación de email
- Confirmación de contraseña
- Gestión de perfil

### Personalización de Breeze

Los archivos principales se encuentran en:

```
resources/js/Pages/Auth/      # Componentes de autenticación
resources/js/Layouts/         # Layouts principales
resources/js/Components/      # Componentes reutilizables
app/Http/Controllers/Auth/    # Controladores de autenticación
routes/auth.php              # Rutas de autenticación
```

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Solución de Problemas Comunes

### Error 404 en el botón de Logout
Si experimentas un error 404 al hacer clic en el botón de Logout, verifica que las rutas de autenticación estén correctamente definidas en el archivo `routes/auth.php` y que los componentes de layout estén utilizando `route('logout')` en lugar de `$page.props.ziggy.routes.logout`.

### Problemas con las migraciones
Si encuentras errores al ejecutar las migraciones, asegúrate de que:
- La base de datos existe y está correctamente configurada en el archivo `.env`
- El usuario tiene permisos suficientes para crear tablas y modificar la base de datos
- Ejecuta `php artisan migrate:status` para verificar el estado de las migraciones

### Errores de JavaScript o Vue.js
Si encuentras errores en la consola del navegador relacionados con Vue.js:
- Asegúrate de haber ejecutado `npm install` y `npm run dev` o `npm run build`
- Limpia la caché del navegador
- Verifica que Vite esté ejecutándose correctamente

### Problemas con la generación de PDFs
Si tienes problemas con la generación de comprobantes en PDF:
- Verifica que la librería `barryvdh/laravel-dompdf` esté instalada correctamente
- Asegúrate de que las plantillas HTML estén correctamente formateadas
- Revisa los permisos de escritura en el directorio temporal donde se almacenan los PDFs

### Problemas con el envío de correos
Si los correos no se envían correctamente:
- Verifica la configuración de correo en el archivo `.env`
- Considera usar servicios como Mailtrap para pruebas en desarrollo
- Ejecuta `php artisan queue:work` si estás utilizando colas para el envío de correos
