<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        Currency::factory()->create([
            "name" => "USD",
            "symbol" => "$",
            "exchange_rate" => 1.000000,
        ]);

        Currency::factory()->create([
            "name" => "EUR",
            "symbol" => "€",
            "exchange_rate" => 0.920000,
        ]);

        Currency::factory()->create([
            "name" => "VES",
            "symbol" => "Bs.",
            "exchange_rate" => 36.500000,
        ]);

        Currency::factory()->count(5)->create();
    }
}
