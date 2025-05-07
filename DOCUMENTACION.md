# Documentación del Sistema de Roles y Permisos

## Descripción General
El sistema de roles y permisos permite gestionar el acceso a diferentes funcionalidades del sistema POS según el rol del usuario. Solo los administradores pueden gestionar roles y usuarios.

## Funcionalidades Implementadas

### 1. Gestión de Roles
- **Ruta**: `/roles` (solo accesible para administradores)
- **Funcionalidades**:
  - Crear nuevos roles
  - Editar roles existentes
  - Asignar permisos a roles
  - Ver lista de roles

### 2. Gestión de Usuarios
- **Ruta**: `/users` (solo accesible para administradores)
- **Funcionalidades**:
  - Crear nuevos usuarios
  - Editar usuarios existentes
  - Asignar roles a usuarios
  - Activar/desactivar usuarios
  - Ver lista de usuarios

### 3. Sistema de Logs
- Registro automático de actividades:
  - Creación de roles
  - Modificación de roles
  - Creación de usuarios
  - Modificación de usuarios

## Roles Predefinidos
1. **Admin**
   - Acceso total al sistema
   - Gestión de roles y permisos
   - Gestión de usuarios

2. **Vendedor**
   - Acceso limitado según permisos asignados
   - No puede gestionar roles ni usuarios

## Seguridad
- Middleware de autenticación en todas las rutas
- Middleware de verificación de roles
- Validación de datos en formularios
- Hashing de contraseñas
- Protección CSRF en formularios
- Registro de actividades en logs

## Interfaz de Usuario
- Diseño responsivo con Tailwind CSS
- Formularios intuitivos para gestión
- Tablas con información clara
- Indicadores visuales de estado
- Mensajes de confirmación/error

## Pruebas
Se han implementado pruebas unitarias y funcionales que cubren:
- Restricciones de acceso según rol
- Creación y modificación de roles
- Creación y modificación de usuarios
- Sistema de logs
- Desactivación de usuarios

Para ejecutar las pruebas:
```bash
php artisan test
```

## Integración en el Dashboard
Para integrar los enlaces en el dashboard de administración:

```php
// Para usuarios admin, mostrar estos enlaces
@if(auth()->user()->rol->nombre === 'admin')
    <a href="{{ route('roles.index') }}">Gestión de Roles</a>
    <a href="{{ route('users.index') }}">Gestión de Usuarios</a>
@endif
```

## Mantenimiento
- Los logs se almacenan en la tabla `logs`
- Se recomienda implementar una política de retención de logs
- Revisar periódicamente los roles y permisos asignados
- Mantener actualizada la lista de usuarios activos
