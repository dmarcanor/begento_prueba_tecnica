<?php

declare(strict_types=1);

namespace Tests\Unit\Divisa;

use App\Actions\Divisa\CrearDivisa;
use App\Models\Divisa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrearDivisaTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_and_returns_a_new_divisa(): void
    {
        $datos = [
            'name' => 'Euro',
            'symbol' => '€',
            'exchange_rate' => 0.92,
        ];

        $action = new CrearDivisa();
        $divisa = $action($datos);

        $this->assertInstanceOf(Divisa::class, $divisa);
        $this->assertEquals('Euro', $divisa->name);
        $this->assertEquals('€', $divisa->symbol);
        $this->assertEquals(0.92, $divisa->exchange_rate);
        $this->assertDatabaseHas('divisas', ['name' => 'Euro']);
    }
}
