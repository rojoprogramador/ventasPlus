<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Categoria;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear categorías primero
        $categorias = [
            ['nombre' => 'Electrónicos', 'descripcion' => 'Productos electrónicos y gadgets'],
            ['nombre' => 'Hogar', 'descripcion' => 'Artículos para el hogar'],
            ['nombre' => 'Alimentos', 'descripcion' => 'Alimentos y bebidas'],
            ['nombre' => 'Papelería', 'descripcion' => 'Artículos de oficina y papelería'],
            ['nombre' => 'Ropa', 'descripcion' => 'Prendas de vestir y accesorios']
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }

        // Crear productos
        $productos = [
            // Electrónicos
            [
                'codigo' => 'E001',
                'nombre' => 'Audífonos Bluetooth',
                'descripcion' => 'Audífonos inalámbricos con cancelación de ruido',
                'precio_compra' => 35.00,
                'precio_venta' => 59.99,
                'stock' => 25,
                'stock_minimo' => 5,
                'categoria_id' => 1,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'E002',
                'nombre' => 'Cargador USB-C',
                'descripcion' => 'Cargador rápido para dispositivos USB-C',
                'precio_compra' => 12.50,
                'precio_venta' => 24.99,
                'stock' => 40,
                'stock_minimo' => 10,
                'categoria_id' => 1,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'E003',
                'nombre' => 'Mouse Inalámbrico',
                'descripcion' => 'Mouse ergonómico con sensor óptico',
                'precio_compra' => 15.00,
                'precio_venta' => 29.99,
                'stock' => 30,
                'stock_minimo' => 5,
                'categoria_id' => 1,
                'estado' => 'activo'
            ],

            // Hogar
            [
                'codigo' => 'H001',
                'nombre' => 'Juego de Toallas',
                'descripcion' => 'Set de 3 toallas 100% algodón',
                'precio_compra' => 18.00,
                'precio_venta' => 32.50,
                'stock' => 20,
                'stock_minimo' => 5,
                'categoria_id' => 2,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'H002',
                'nombre' => 'Sartén Antiadherente',
                'descripcion' => 'Sartén de 28cm con mango ergonómico',
                'precio_compra' => 22.50,
                'precio_venta' => 39.99,
                'stock' => 15,
                'stock_minimo' => 3,
                'categoria_id' => 2,
                'estado' => 'activo'
            ],

            // Alimentos
            [
                'codigo' => 'A001',
                'nombre' => 'Café Premium',
                'descripcion' => 'Café molido 100% arábica 500g',
                'precio_compra' => 8.50,
                'precio_venta' => 14.99,
                'stock' => 50,
                'stock_minimo' => 10,
                'categoria_id' => 3,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'A002',
                'nombre' => 'Chocolate Orgánico',
                'descripcion' => 'Barra de chocolate 70% cacao 100g',
                'precio_compra' => 3.20,
                'precio_venta' => 6.50,
                'stock' => 60,
                'stock_minimo' => 15,
                'categoria_id' => 3,
                'estado' => 'activo'
            ],

            // Papelería
            [
                'codigo' => 'P001',
                'nombre' => 'Cuaderno Profesional',
                'descripcion' => 'Cuaderno de 100 hojas rayadas',
                'precio_compra' => 2.80,
                'precio_venta' => 5.99,
                'stock' => 100,
                'stock_minimo' => 20,
                'categoria_id' => 4,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'P002',
                'nombre' => 'Set de Bolígrafos',
                'descripcion' => 'Paquete de 5 bolígrafos de colores',
                'precio_compra' => 3.50,
                'precio_venta' => 7.99,
                'stock' => 80,
                'stock_minimo' => 15,
                'categoria_id' => 4,
                'estado' => 'activo'
            ],

            // Ropa
            [
                'codigo' => 'R001',
                'nombre' => 'Camiseta Básica',
                'descripcion' => 'Camiseta de algodón unisex talla M',
                'precio_compra' => 8.00,
                'precio_venta' => 15.99,
                'stock' => 35,
                'stock_minimo' => 5,
                'categoria_id' => 5,
                'estado' => 'activo'
            ],
            [
                'codigo' => 'R002',
                'nombre' => 'Calcetines Pack',
                'descripcion' => 'Pack de 3 pares de calcetines deportivos',
                'precio_compra' => 5.50,
                'precio_venta' => 11.99,
                'stock' => 45,
                'stock_minimo' => 10,
                'categoria_id' => 5,
                'estado' => 'activo'
            ]
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}
