<?php

declare(strict_types=1);

namespace Tests\Unit\Producto;

use App\Actions\Producto\CrearProducto;
use App\Models\Divisa;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrearProductoTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_and_returns_a_new_producto(): void
    {
        $divisa = Divisa::factory()->create();
        
        $datos = [
            'name' => 'Producto de prueba',
            'description' => 'Descripción del producto',
            'price' => 100.50,
            'currency_id' => $divisa->id,
            'tax_cost' => 16.00,
            'manufacturing_cost' => 50.00,
        ];

        $action = new CrearProducto();
        $producto = $action($datos);

        $this->assertInstanceOf(Producto::class, $producto);
        $this->assertEquals('Producto de prueba', $producto->name);
        $this->assertEquals('Descripción del producto', $producto->description);
        $this->assertEquals(100.50, $producto->price);
        $this->assertEquals($divisa->id, $producto->currency_id);
        $this->assertEquals(16.00, $producto->tax_cost);
        $this->assertEquals(50.00, $producto->manufacturing_cost);
        $this->assertDatabaseHas('productos', ['name' => 'Producto de prueba']);
    }
}
