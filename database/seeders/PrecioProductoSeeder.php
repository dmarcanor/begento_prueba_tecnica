<?php

namespace Database\Seeders;

use App\Models\PrecioProducto;
use Illuminate\Database\Seeder;

class PrecioProductoSeeder extends Seeder
{
    public function run(): void
    {
        PrecioProducto::factory()->count(10)->create();
    }
}
