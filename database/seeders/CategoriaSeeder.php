<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categorias = [
            [
                'nombre' => 'Electrónicos',
                'descripcion' => 'Productos electrónicos y tecnológicos',
                'estado' => true,
            ],
            [
                'nombre' => 'Hogar',
                'descripcion' => 'Productos para el hogar',
                'estado' => true,
            ],
            [
                'nombre' => 'Ropa',
                'descripcion' => 'Prendas de vestir y accesorios',
                'estado' => true,
            ],
            [
                'nombre' => 'Alimentos',
                'descripcion' => 'Productos alimenticios',
                'estado' => true,
            ],
        ];

        foreach ($categorias as $categoria) {
            Categoria::updateOrCreate(
                ['nombre' => $categoria['nombre']],
                $categoria
            );
        }

        $this->command->info('Categorías de prueba añadidas con éxito.');
    }
}
