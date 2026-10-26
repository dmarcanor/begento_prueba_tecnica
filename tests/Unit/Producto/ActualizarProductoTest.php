<?php

declare(strict_types=1);

namespace Tests\Unit\Producto;

use App\Actions\Producto\ActualizarProducto;
use App\Exceptions\ProductoNoEncontradoException;
use App\Models\Producto;
use App\Services\Producto\BuscarProducto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActualizarProductoTest extends TestCase
{
    use RefreshDatabase;

    public function test_updates_and_returns_the_fresh_producto(): void
    {
        $producto = Producto::factory()->create();

        $action = new ActualizarProducto(new BuscarProducto());
        $result = $action($producto->id, ['name' => 'Actualizado']);

        $this->assertEquals('Actualizado', $result->name);
    }

    public function test_throws_when_producto_does_not_exist(): void
    {
        $action = new ActualizarProducto(new BuscarProducto());

        $this->expectException(ProductoNoEncontradoException::class);

        $action(99999, ['name' => 'Actualizado']);
    }
}
