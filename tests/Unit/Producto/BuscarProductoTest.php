<?php

declare(strict_types=1);

namespace Tests\Unit\Producto;

use App\Exceptions\ProductoNoEncontradoException;
use App\Models\Producto;
use App\Services\Producto\BuscarProducto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuscarProductoTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_the_producto_when_found(): void
    {
        $producto = Producto::factory()->create();

        $service = new BuscarProducto();
        $result = $service($producto->id);

        $this->assertInstanceOf(Producto::class, $result);
        $this->assertEquals($producto->id, $result->id);
    }

    public function test_throws_producto_no_encontrado_exception_when_not_found(): void
    {
        $service = new BuscarProducto();

        $this->expectException(ProductoNoEncontradoException::class);
        $this->expectExceptionMessage('Product not found');

        $service(99999);
    }
}
