<?php

namespace Database\Factories;

use App\Models\Divisa;
use App\Models\PrecioProducto;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrecioProducto>
 */
class PrecioProductoFactory extends Factory
{
    public function definition(): array
    {
        return [
            "product_id" => Producto::factory(),
            "currency_id" => Divisa::factory(),
            "price" => fake()->randomFloat(2, 10, 1000),
        ];
    }
}
