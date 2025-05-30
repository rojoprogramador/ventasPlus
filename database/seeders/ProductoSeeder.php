<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use Carbon\Carbon;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productos = [
            // Categoría Electrónicos (ID 1)
            [
                'codigo' => 'P001',
                'nombre' => 'Smartphone Samsung Galaxy S22',
                'descripcion' => 'Smartphone de última generación con pantalla AMOLED y cámara de 108MP',
                'precio_venta' => 899.99,
                'precio_compra' => 699.99,
                'stock' => 25,
                'stock_minimo' => 5,
                'categoria_id' => 1,
                'imagen' => 'productos/samsung-s22.jpg',
                'estado' => 'activo',
                'precio_promocional' => 849.99,
                'fecha_inicio_promocion' => Carbon::now()->subDays(5),
                'fecha_fin_promocion' => Carbon::now()->addDays(10),
                'permite_descuentos' => true,
            ],
            [
                'codigo' => 'P002',
                'nombre' => 'Laptop HP Pavilion',
                'descripcion' => 'Laptop con procesador i7, 16GB RAM y 512GB SSD',
                'precio_venta' => 1199.99,
                'precio_compra' => 899.99,
                'stock' => 15,
                'stock_minimo' => 3,                'categoria_id' => 1, // Electrónicos
                'imagen' => 'productos/hp-pavilion.jpg',
                'estado' => 'activo',
                'precio_promocional' => null,
                'fecha_inicio_promocion' => null,
                'fecha_fin_promocion' => null,
                'permite_descuentos' => true,
            ],
            [
                'codigo' => 'P003',
                'nombre' => 'Audífonos Sony WH-1000XM4',
                'descripcion' => 'Audífonos inalámbricos con cancelación de ruido',
                'precio_venta' => 349.99,
                'precio_compra' => 249.99,
                'stock' => 30,
                'stock_minimo' => 5,
                'categoria_id' => 1, // Electrónicos                'imagen' => 'productos/sony-wh1000xm4.jpg',
                'estado' => 'activo',
                'precio_promocional' => 299.99,
                'fecha_inicio_promocion' => Carbon::now()->subDays(2),
                'fecha_fin_promocion' => Carbon::now()->addDays(15),
                'permite_descuentos' => false,
            ],
            [
                'codigo' => 'P004',
                'nombre' => 'Monitor LG 27"',
                'descripcion' => 'Monitor UHD 4K de 27 pulgadas',
                'precio_venta' => 399.99,
                'precio_compra' => 299.99,
                'stock' => 20,
                'stock_minimo' => 4,
                'categoria_id' => 1, // Electrónicos                'imagen' => 'productos/lg-monitor.jpg',
                'estado' => 'activo',
                'precio_promocional' => null,
                'fecha_inicio_promocion' => null,
                'fecha_fin_promocion' => null,
                'permite_descuentos' => true,
            ],
            [
                'codigo' => 'P005',
                'nombre' => 'Tableta iPad Pro',
                'descripcion' => 'Tableta de 11 pulgadas con 256GB de almacenamiento',
                'precio_venta' => 799.99,
                'precio_compra' => 649.99,
                'stock' => 18,
                'stock_minimo' => 3,
                'categoria_id' => 1, // Electrónicos                'imagen' => 'productos/ipad-pro.jpg',
                'estado' => 'activo',
                'precio_promocional' => 749.99,
                'fecha_inicio_promocion' => Carbon::now()->subDays(1),
                'fecha_fin_promocion' => Carbon::now()->addDays(7),
                'permite_descuentos' => true,
            ],
            [
                'codigo' => 'P006',
                'nombre' => 'Teclado mecánico Logitech',
                'descripcion' => 'Teclado mecánico RGB para gaming',
                'precio_venta' => 129.99,
                'precio_compra' => 89.99,
                'stock' => 35,
                'stock_minimo' => 5,
                'categoria_id' => 1, // Electrónicos                'imagen' => 'productos/logitech-teclado.jpg',
                'estado' => 'activo',
                'precio_promocional' => null,
                'fecha_inicio_promocion' => null,
                'fecha_fin_promocion' => null,
                'permite_descuentos' => true,
            ],
            [
                'codigo' => 'P007',
                'nombre' => 'Mouse inalámbrico Razer',
                'descripcion' => 'Mouse inalámbrico con 8 botones programables',
                'precio_venta' => 79.99,
                'precio_compra' => 49.99,
                'stock' => 40,
                'stock_minimo' => 8,
                'categoria_id' => 1, // Electrónicos                'imagen' => 'productos/razer-mouse.jpg',
                'estado' => 'activo',
                'precio_promocional' => 69.99,
                'fecha_inicio_promocion' => Carbon::now()->subDays(3),
                'fecha_fin_promocion' => Carbon::now()->addDays(12),
                'permite_descuentos' => false,
            ],            [
                'codigo' => 'P008',
                'nombre' => 'Cámara Canon EOS',
                'descripcion' => 'Cámara DSLR profesional con lente 18-55mm',
                'precio_venta' => 849.99,
                'precio_compra' => 699.99,
                'stock' => 12,
                'stock_minimo' => 2,
                'categoria_id' => 1, 
                'imagen' => 'productos/canon-eos.jpg',
                'estado' => 'activo',
                'precio_promocional' => null,
                'fecha_inicio_promocion' => null,
                'fecha_fin_promocion' => null,
                'permite_descuentos' => true,
            ],
            
            // Categoría Hogar (ID 2)
            [
                'codigo' => 'H001',
                'nombre' => 'Juego de Sábanas Premium',
                'descripcion' => 'Juego de sábanas de algodón egipcio, 1000 hilos, tamaño King',
                'precio_venta' => 89.99,
                'precio_compra' => 45.99,
                'stock' => 30,
                'stock_minimo' => 5,
                'categoria_id' => 2,
                'imagen' => 'productos/sabanas.jpg',
                'estado' => 'activo',
                'precio_promocional' => 79.99,
                'fecha_inicio_promocion' => Carbon::now()->subDays(2),
                'fecha_fin_promocion' => Carbon::now()->addDays(10),
                'permite_descuentos' => true,
            ],
            [
                'codigo' => 'H002',
                'nombre' => 'Licuadora Oster 3 velocidades',
                'descripcion' => 'Licuadora de 600W con jarra de vidrio y 3 velocidades + pulso',
                'precio_venta' => 59.99,
                'precio_compra' => 32.99,
                'stock' => 25,
                'stock_minimo' => 5,
                'categoria_id' => 2,
                'imagen' => 'productos/licuadora.jpg',
                'estado' => 'activo',
                'precio_promocional' => null,
                'fecha_inicio_promocion' => null,
                'fecha_fin_promocion' => null,
                'permite_descuentos' => true,
            ],
            [
                'codigo' => 'H003',
                'nombre' => 'Set de Ollas Tramontina',
                'descripcion' => 'Juego de 5 ollas de acero inoxidable con tapas de vidrio',
                'precio_venta' => 149.99,
                'precio_compra' => 89.99,
                'stock' => 15,
                'stock_minimo' => 3,
                'categoria_id' => 2,
                'imagen' => 'productos/ollas.jpg',
                'estado' => 'activo',
                'precio_promocional' => 129.99,
                'fecha_inicio_promocion' => Carbon::now()->subDays(10),
                'fecha_fin_promocion' => Carbon::now()->addDays(5),
                'permite_descuentos' => true,
            ],
            
            // Categoría Ropa (ID 3)
            [
                'codigo' => 'R001',
                'nombre' => 'Camisa Manga Larga',
                'descripcion' => 'Camisa formal para hombre, 100% algodón, color azul',
                'precio_venta' => 45.99,
                'precio_compra' => 22.50,
                'stock' => 40,
                'stock_minimo' => 10,
                'categoria_id' => 3,
                'imagen' => 'productos/camisa.jpg',
                'estado' => 'activo',
                'precio_promocional' => null,
                'fecha_inicio_promocion' => null,
                'fecha_fin_promocion' => null,
                'permite_descuentos' => true,
            ],
            [
                'codigo' => 'R002',
                'nombre' => 'Jeans Clásicos',
                'descripcion' => 'Jeans de corte recto para hombre, color azul oscuro',
                'precio_venta' => 59.99,
                'precio_compra' => 29.99,
                'stock' => 35,
                'stock_minimo' => 7,
                'categoria_id' => 3,
                'imagen' => 'productos/jeans.jpg',
                'estado' => 'activo',
                'precio_promocional' => 49.99,
                'fecha_inicio_promocion' => Carbon::now()->subDays(3),
                'fecha_fin_promocion' => Carbon::now()->addDays(15),
                'permite_descuentos' => true,
            ],
            [
                'codigo' => 'R003',
                'nombre' => 'Vestido Casual',
                'descripcion' => 'Vestido corto para mujer, estampado floral, talla M',
                'precio_venta' => 65.99,
                'precio_compra' => 32.99,
                'stock' => 20,
                'stock_minimo' => 5,
                'categoria_id' => 3,
                'imagen' => 'productos/vestido.jpg',
                'estado' => 'activo',
                'precio_promocional' => 55.99,
                'fecha_inicio_promocion' => Carbon::now()->subDays(5),
                'fecha_fin_promocion' => Carbon::now()->addDays(10),
                'permite_descuentos' => true,
            ],
            
            // Categoría Alimentos (ID 4)
            [
                'codigo' => 'A001',
                'nombre' => 'Arroz Premium',
                'descripcion' => 'Arroz de grano largo, paquete de 5kg',
                'precio_venta' => 12.99,
                'precio_compra' => 7.50,
                'stock' => 100,
                'stock_minimo' => 20,
                'categoria_id' => 4,
                'imagen' => 'productos/arroz.jpg',
                'estado' => 'activo',
                'precio_promocional' => null,
                'fecha_inicio_promocion' => null,
                'fecha_fin_promocion' => null,
                'permite_descuentos' => true,
            ],
            [
                'codigo' => 'A002',
                'nombre' => 'Aceite de Oliva Extra Virgen',
                'descripcion' => 'Aceite de oliva extra virgen, botella de 1L',
                'precio_venta' => 15.99,
                'precio_compra' => 8.99,
                'stock' => 80,
                'stock_minimo' => 15,
                'categoria_id' => 4,
                'imagen' => 'productos/aceite.jpg',
                'estado' => 'activo',
                'precio_promocional' => 13.99,
                'fecha_inicio_promocion' => Carbon::now()->subDays(7),
                'fecha_fin_promocion' => Carbon::now()->addDays(7),
                'permite_descuentos' => false,
            ],
            [
                'codigo' => 'A003',
                'nombre' => 'Café Gourmet',
                'descripcion' => 'Café molido premium, paquete de 500g',
                'precio_venta' => 9.99,
                'precio_compra' => 5.50,
                'stock' => 60,
                'stock_minimo' => 12,
                'categoria_id' => 4,
                'imagen' => 'productos/cafe.jpg',
                'estado' => 'activo',
                'precio_promocional' => 8.99,
                'fecha_inicio_promocion' => Carbon::now()->subDays(2),
                'fecha_fin_promocion' => Carbon::now()->addDays(12),
                'permite_descuentos' => true,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::updateOrCreate(
                ['codigo' => $producto['codigo']],
                $producto
            );
        }

        $this->command->info('Productos de prueba añadidos con éxito.');
    }
}
