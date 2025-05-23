<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Producto::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'codigo' => $this->faker->unique()->ean13(),
            'nombre' => $this->faker->words(3, true),
            'descripcion' => $this->faker->paragraph,
            'precio_compra' => $this->faker->randomFloat(2, 10, 500),
            'precio_venta' => $this->faker->randomFloat(2, 20, 1000),
            'stock' => $this->faker->numberBetween(0, 100),
            'stock_minimo' => $this->faker->numberBetween(1, 10),
            'categoria_id' => function () {
                return \App\Models\Categoria::factory()->create()->id;
            },
            'estado' => 'activo',
            'permite_descuentos' => $this->faker->boolean,
        ];
    }
}
