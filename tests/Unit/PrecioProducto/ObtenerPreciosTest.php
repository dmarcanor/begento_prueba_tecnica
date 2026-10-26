<?php

declare(strict_types=1);

namespace Tests\Unit\PrecioProducto;

use App\Actions\PrecioProducto\ObtenerPrecios;
use App\Exceptions\ProductoNoEncontradoException;
use App\Models\PrecioProducto;
use App\Models\Producto;
use App\Services\Producto\BuscarProducto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObtenerPreciosTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_paginated_precios_for_a_producto(): void
    {
        $producto = Producto::factory()->create();
        PrecioProducto::factory()->count(3)->create(['product_id' => $producto->id]);

        $action = new ObtenerPrecios(new BuscarProducto());
        $result = $action($producto->id, ['per_page' => 10]);

        $this->assertEquals(3, $result->total());
    }

    public function test_throws_when_producto_does_not_exist(): void
    {
        $action = new ObtenerPrecios(new BuscarProducto());

        $this->expectException(ProductoNoEncontradoException::class);

        $action(99999, ['per_page' => 10]);
    }
}
