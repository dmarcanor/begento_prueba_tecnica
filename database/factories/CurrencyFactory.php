<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Currency>
 */
class CurrencyFactory extends Factory
{
    public function definition(): array
    {
        return [
            "name" => fake()->currencyCode(),
            "symbol" => fake()->randomElement(["$", "€", "£", "¥", "Bs.", "₡", "S/."]),
            "exchange_rate" => fake()->randomFloat(8, 0.5, 50),
        ];
    }
}
