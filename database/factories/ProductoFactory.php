<?php

namespace Database\Factories;

use App\Models\Divisa;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Producto>
 */
class ProductoFactory extends Factory
{
    public function definition(): array
    {
        return [
            "name" => fake()->words(3, true),
            "description" => fake()->paragraph(),
            "price" => fake()->randomFloat(2, 10, 1000),
            "currency_id" => Divisa::factory(),
            "tax_cost" => fake()->randomFloat(2, 0, 100),
            "manufacturing_cost" => fake()->randomFloat(2, 5, 500),
        ];
    }
}
