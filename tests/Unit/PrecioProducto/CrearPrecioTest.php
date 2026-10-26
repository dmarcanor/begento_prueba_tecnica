<?php

declare(strict_types=1);

namespace Tests\Unit\PrecioProducto;

use App\Actions\PrecioProducto\CrearPrecio;
use App\Exceptions\ProductoNoEncontradoException;
use App\Models\Divisa;
use App\Models\PrecioProducto;
use App\Models\Producto;
use App\Services\Producto\BuscarProducto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrearPrecioTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_a_precio_for_an_existing_producto(): void
    {
        $producto = Producto::factory()->create();
        $divisa = Divisa::factory()->create();

        $action = new CrearPrecio(new BuscarProducto());
        $precio = $action($producto->id, [
            'price' => 100.00,
            'currency_id' => $divisa->id,
        ]);

        $this->assertInstanceOf(PrecioProducto::class, $precio);
        $this->assertEquals($producto->id, $precio->product_id);
        $this->assertEquals(100.00, $precio->price);
        $this->assertTrue($precio->relationLoaded('divisa'));
    }

    public function test_throws_when_producto_does_not_exist(): void
    {
        $divisa = Divisa::factory()->create();
        $action = new CrearPrecio(new BuscarProducto());

        $this->expectException(ProductoNoEncontradoException::class);

        $action(99999, [
            'price' => 100.00,
            'currency_id' => $divisa->id,
        ]);
    }
}
