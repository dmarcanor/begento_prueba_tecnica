<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsuarioSeeder::class,
            DivisaSeeder::class,
            ProductoSeeder::class,
            PrecioProductoSeeder::class,
        ]);
    }
}
