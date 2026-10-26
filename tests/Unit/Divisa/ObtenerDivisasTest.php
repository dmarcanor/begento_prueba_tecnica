<?php

declare(strict_types=1);

namespace Tests\Unit\Divisa;

use App\Actions\Divisa\ObtenerDivisas;
use App\Models\Divisa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObtenerDivisasTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_paginated_divisas_with_default_params(): void
    {
        Divisa::factory()->count(15)->create();

        $action = new ObtenerDivisas();
        $result = $action([]);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(15, $result->total());
        $this->assertCount(10, $result->items());
    }

    public function test_respects_custom_per_page_parameter(): void
    {
        Divisa::factory()->count(25)->create();

        $action = new ObtenerDivisas();
        $result = $action(['per_page' => 5]);

        $this->assertEquals(5, $result->perPage());
        $this->assertCount(5, $result->items());
    }

    public function test_sorts_by_specified_field_and_direction(): void
    {
        $divisa1 = Divisa::factory()->create(['exchange_rate' => 0.5]);
        $divisa2 = Divisa::factory()->create(['exchange_rate' => 2.0]);
        $divisa3 = Divisa::factory()->create(['exchange_rate' => 1.0]);

        $action = new ObtenerDivisas();
        $result = $action(['sort_by' => 'exchange_rate', 'sort_direction' => 'asc']);

        $items = $result->items();
        $this->assertEquals($divisa1->id, $items[0]->id);
        $this->assertEquals($divisa3->id, $items[1]->id);
        $this->assertEquals($divisa2->id, $items[2]->id);
    }
}
