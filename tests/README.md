# Pruebas del Sistema VentaPlus

Este directorio contiene las pruebas automatizadas para el sistema VentaPlus, enfocadas en la validación del sistema de permisos y roles.

## Estructura de Pruebas

### 1. Pruebas de Roles (`RolTest.php`)
- `test_admin_puede_ver_lista_de_roles`: Verifica que un admin puede ver roles
- `test_usuario_sin_permiso_no_puede_ver_roles`: Verifica restricción de acceso
- `test_admin_puede_crear_rol`: Valida la creación de roles con permisos

### 2. Pruebas de Usuarios (`UserTest.php`)
- `test_admin_puede_ver_lista_de_usuarios`: Verifica listado de usuarios
- `test_admin_puede_crear_usuario`: Valida creación de usuarios
- `test_usuario_sin_permiso_no_puede_ver_usuarios`: Verifica restricciones

### 3. Pruebas de Permisos (`PermisoTest.php`)
- `test_verifica_permisos_de_admin`: Valida permisos de administrador
- `test_verifica_permisos_de_vendedor`: Verifica permisos limitados
- `test_middleware_verifica_permiso`: Prueba el middleware de permisos

## Ejecutar las Pruebas

Para ejecutar todas las pruebas:
```bash
php artisan test
```

Para ejecutar pruebas específicas:
```bash
# Solo pruebas de roles
php artisan test tests/Feature/RolTest.php

# Solo pruebas de usuarios
php artisan test tests/Feature/UserTest.php

# Solo pruebas de permisos
php artisan test tests/Feature/PermisoTest.php
```

## Cobertura de Pruebas

Las pruebas cubren:
- ✅ Validación de permisos
- ✅ Gestión de roles
- ✅ Gestión de usuarios
- ✅ Middleware de protección
- ✅ Relaciones entre modelos
- ✅ Acceso a rutas protegidas

## Requisitos

- Laravel 9.x
- PHP 8.0+
- Base de datos de prueba configurada
- Seeders de roles y permisos ejecutados
