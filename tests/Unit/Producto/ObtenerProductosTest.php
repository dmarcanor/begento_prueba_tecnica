<?php

declare(strict_types=1);

namespace Tests\Unit\Producto;

use App\Actions\Producto\ObtenerProductos;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObtenerProductosTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_paginated_productos_with_default_params(): void
    {
        Producto::factory()->count(15)->create();

        $action = new ObtenerProductos();
        $result = $action([]);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(15, $result->total());
        $this->assertCount(10, $result->items());
    }

    public function test_respects_custom_per_page_parameter(): void
    {
        Producto::factory()->count(25)->create();

        $action = new ObtenerProductos();
        $result = $action(['per_page' => 5]);

        $this->assertEquals(5, $result->perPage());
        $this->assertCount(5, $result->items());
    }

    public function test_sorts_by_specified_field_and_direction(): void
    {
        $producto1 = Producto::factory()->create(['price' => 100]);
        $producto2 = Producto::factory()->create(['price' => 300]);
        $producto3 = Producto::factory()->create(['price' => 200]);

        $action = new ObtenerProductos();
        $result = $action(['sort_by' => 'price', 'sort_direction' => 'asc']);

        $items = $result->items();
        $this->assertEquals($producto1->id, $items[0]->id);
        $this->assertEquals($producto3->id, $items[1]->id);
        $this->assertEquals($producto2->id, $items[2]->id);
    }
}
