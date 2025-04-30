# VentaPlus - Sistema POS

Sistema de Punto de Venta (POS) desarrollado con Laravel, diseñado para gestionar ventas, inventario, clientes y reportes de manera eficiente.

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

- **Administrador**
  - Email: admin@ventaplus.com
  - Password: admin123

- **Vendedor**
  - Email: vendedor@ventaplus.com
  - Password: vendedor123

- **Cajero**
  - Email: cajero@ventaplus.com
  - Password: cajero123

## Características

- Gestión de ventas
- Control de inventario
- Gestión de clientes
- Gestión de usuarios y roles
- Reportes y estadísticas
- Control de cajas
- Sistema de cotizaciones

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
