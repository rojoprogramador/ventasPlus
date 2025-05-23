# Guía para el Mantenimiento de Pruebas en VentaPlus

## Introducción

Este documento proporciona directrices para mantener y ampliar las pruebas automatizadas del sistema VentaPlus. Seguir estas directrices asegurará que las pruebas sean robustas, fáciles de mantener y útiles para detectar problemas.

## Estructura de Pruebasgit pull origin


El sistema VentaPlus utiliza PHPUnit y Pest para las pruebas. La estructura de pruebas es la siguiente:

```
tests/
├── Feature/            # Pruebas de funcionalidad (controladores, middleware, etc.)
│   ├── ClienteControllerTest.php
│   ├── ExportacionControllerTest.php
│   └── ...
├── Unit/               # Pruebas unitarias (modelos, repositorios, etc.)
├── TestCase.php        # Clase base para todas las pruebas
└── CreatesApplication.php  # Trait para crear la aplicación
```

## Uso de Factories

Para simplificar la creación de datos de prueba, hemos implementado factories para los modelos principales:

```php
// Crear un cliente
$cliente = Cliente::factory()->create();

// Crear un cliente con atributos específicos
$cliente = Cliente::factory()->create([
    'nombre' => 'Cliente Personalizado',
    'email' => 'cliente@ejemplo.com'
]);

// Crear un producto
$producto = Producto::factory()->create();
```

## Uso de Mocks

Para aislar las pruebas de la base de datos real o simular comportamientos, utilizamos mocks:

```php
// Mock de un controlador
$this->mock(ClienteController::class, function ($mock) {
    $mock->shouldReceive('index')->andReturn(
        Inertia::render('Clientes/Index', [
            'clientes' => [/* Datos simulados */]
        ])
    );
});

// Mock para evitar middleware
$this->withoutMiddleware(\App\Http\Middleware\VerificarPermiso::class);
```

## Buenas Prácticas

1. **Mantener pruebas independientes**: Cada prueba debe poder ejecutarse independientemente de las demás.

2. **Usar RefreshDatabase**: Para asegurar que la base de datos esté en un estado conocido antes de cada prueba.

3. **Hacer comprobaciones específicas**: Verificar exactamente lo que se espera, no más.

4. **Usar nombres descriptivos**: Los nombres de los métodos de prueba deben describir lo que se está probando.

5. **Incluir pruebas para casos de éxito y error**: No solo probar el camino feliz, sino también los casos de error.

6. **Verificar la estructura de la base de datos**: Usar la prueba `DatabaseStructureTest` para verificar que la estructura de la base de datos sea correcta.

## Ejecutando Pruebas

Para ejecutar las pruebas:

```powershell
# Ejecutar todas las pruebas
php artisan test

# Ejecutar una prueba específica
php artisan test tests/Feature/ClienteControllerTest.php

# Ejecutar un método específico de una prueba
php artisan test tests/Feature/ClienteControllerTest.php --filter=puede_crear_cliente_rapido

# Ver resultados detallados
php artisan test --testdox
```

## Solución de Problemas Comunes

1. **Errores de estructura de BD**: Si hay errores relacionados con la estructura de la base de datos, revisar las migraciones y ejecutar el test `DatabaseStructureTest`.

2. **Problemas con las relaciones**: Verificar que las claves foráneas están correctamente definidas.

3. **Problemas con los mocks**: Asegurarse de que el mock está recibiendo el método correcto y retornando los valores esperados.

4. **Problemas con los factories**: Revisar que los factories incluyan todos los campos requeridos.

## Referencias

- [Documentación de PHPUnit](https://phpunit.de/documentation.html)
- [Documentación de Laravel Testing](https://laravel.com/docs/9.x/testing)
- [Documentación de Pest](https://pestphp.com/docs/)
