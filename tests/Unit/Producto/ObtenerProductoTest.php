<?php

declare(strict_types=1);

namespace Tests\Unit\Producto;

use App\Actions\Producto\ObtenerProducto;
use App\Exceptions\ProductoNoEncontradoException;
use App\Models\Producto;
use App\Services\Producto\BuscarProducto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObtenerProductoTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_the_producto_when_found(): void
    {
        $producto = Producto::factory()->create();

        $action = new ObtenerProducto(new BuscarProducto());
        $result = $action($producto->id);

        $this->assertInstanceOf(Producto::class, $result);
        $this->assertEquals($producto->id, $result->id);
        $this->assertEquals($producto->name, $result->name);
    }

    public function test_throws_when_producto_does_not_exist(): void
    {
        $action = new ObtenerProducto(new BuscarProducto());

        $this->expectException(ProductoNoEncontradoException::class);

        $action(99999);
    }
}
