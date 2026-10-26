<?php

declare(strict_types=1);

namespace Tests\Unit\Producto;

use App\Actions\Producto\EliminarProducto;
use App\Exceptions\ProductoNoEncontradoException;
use App\Models\Producto;
use App\Services\Producto\BuscarProducto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EliminarProductoTest extends TestCase
{
    use RefreshDatabase;

    public function test_deletes_the_producto(): void
    {
        $producto = Producto::factory()->create();

        $action = new EliminarProducto(new BuscarProducto());
        $action($producto->id);

        $this->assertSoftDeleted('productos', ['id' => $producto->id]);
    }

    public function test_throws_when_producto_does_not_exist(): void
    {
        $action = new EliminarProducto(new BuscarProducto());

        $this->expectException(ProductoNoEncontradoException::class);

        $action(99999);
    }
}
