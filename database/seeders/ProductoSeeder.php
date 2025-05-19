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
            [
                'codigo' => 'P001',
                'nombre' => 'Smartphone Samsung Galaxy S22',
                'descripcion' => 'Smartphone de última generación con pantalla AMOLED y cámara de 108MP',
                'precio_venta' => 899.99,
                'precio_compra' => 699.99,
                'stock' => 25,
                'stock_minimo' => 5,
                'categoria_id' => 1, // Asume que tienes categorías (1 = Electrónicos)
                'imagen' => 'productos/samsung-s22.jpg',
                'estado' => true,
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
                'stock_minimo' => 3,
                'categoria_id' => 1, // Electrónicos
                'imagen' => 'productos/hp-pavilion.jpg',
                'estado' => true,
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
                'categoria_id' => 1, // Electrónicos
                'imagen' => 'productos/sony-wh1000xm4.jpg',
                'estado' => true,
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
                'categoria_id' => 1, // Electrónicos
                'imagen' => 'productos/lg-monitor.jpg',
                'estado' => true,
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
                'categoria_id' => 1, // Electrónicos
                'imagen' => 'productos/ipad-pro.jpg',
                'estado' => true,
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
                'categoria_id' => 1, // Electrónicos
                'imagen' => 'productos/logitech-teclado.jpg',
                'estado' => true,
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
                'categoria_id' => 1, // Electrónicos
                'imagen' => 'productos/razer-mouse.jpg',
                'estado' => true,
                'precio_promocional' => 69.99,
                'fecha_inicio_promocion' => Carbon::now()->subDays(3),
                'fecha_fin_promocion' => Carbon::now()->addDays(12),
                'permite_descuentos' => false,
            ],
            [
                'codigo' => 'P008',
                'nombre' => 'Cámara Canon EOS',
                'descripcion' => 'Cámara DSLR profesional con lente 18-55mm',
                'precio_venta' => 849.99,
                'precio_compra' => 699.99,
                'stock' => 12,
                'stock_minimo' => 2,
                'categoria_id' => 1, // Electrónicos
                'imagen' => 'productos/canon-eos.jpg',
                'estado' => true,
                'precio_promocional' => null,
                'fecha_inicio_promocion' => null,
                'fecha_fin_promocion' => null,
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
