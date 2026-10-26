<?php

namespace Database\Seeders;

use App\Models\Divisa;
use Illuminate\Database\Seeder;

class DivisaSeeder extends Seeder
{
    public function run(): void
    {
        Divisa::factory()->create([
            "name" => "USD",
            "symbol" => "$",
            "exchange_rate" => 1.00000000,
        ]);

        Divisa::factory()->create([
            "name" => "VES",
            "symbol" => "Bs.",
            "exchange_rate" => 887.30200000,
        ]);
    }
}
