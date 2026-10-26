<?php

namespace Database\Factories;

use App\Models\Divisa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Divisa>
 */
class DivisaFactory extends Factory
{
    public function definition(): array
    {
        return [
            "name" => fake()->currencyCode(),
            "symbol" => fake()->randomElement(["$", "€", "Bs."]),
            "exchange_rate" => fake()->randomFloat(8, 0.5, 50),
        ];
    }
}
