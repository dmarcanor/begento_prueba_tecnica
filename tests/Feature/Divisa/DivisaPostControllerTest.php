<?php

declare(strict_types=1);

namespace Tests\Feature\Divisa;

use App\Models\Divisa;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DivisaPostControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser(): string
    {
        $usuario = Usuario::factory()->create();
        return $usuario->createToken('test-token')->plainTextToken;
    }

    /**
     * Test POST /divisas creates a new divisa
     */
    public function test_store_should_create_a_new_divisa(): void
    {
        $token = $this->createAuthenticatedUser();
        $divisa = Divisa::factory()->make();

        $response = $this->withHeader('Authorization', "Bearer $token")->post('/api/divisas', [
            'name' => $divisa->name,
            'symbol' => $divisa->symbol,
            'exchange_rate' => $divisa->exchange_rate,
        ]);

        $json = $response->json();

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'error',
            'body' => [
                'divisa' => [
                    'id',
                    'name',
                    'symbol',
                    'exchange_rate',
                    'created_at',
                    'updated_at',
                ],
            ]
        ]);

        $this->assertEmpty($json['error']);
        $this->assertEquals($divisa->name, $json['body']['divisa']['name']);
        $this->assertEquals($divisa->symbol, $json['body']['divisa']['symbol']);
        $this->assertEquals($divisa->exchange_rate, $json['body']['divisa']['exchange_rate']);
    }

    /**
     * Test POST /divisas returns 400 with invalid data
     */
    public function test_store_with_invalid_request(): void
    {
        $token = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer $token")->post('/api/divisas', [
            'name' => '',
            'symbol' => '',
            'exchange_rate' => -1,
        ]);

        $json = $response->json();

        $response->assertStatus(400);
        $response->assertJsonStructure([
            'error' => [
                'name',
                'symbol',
                'exchange_rate',
            ],
            'body'
        ]);

        $this->assertEmpty($json['body']);
        $this->assertEquals(['The name field is required.'], $json['error']['name']);
        $this->assertEquals(['The symbol field is required.'], $json['error']['symbol']);
        $this->assertEquals(['The exchange rate field must be at least 0.'], $json['error']['exchange_rate']);
    }

    /**
     * Test POST /divisas returns 401 without authentication
     */
    public function test_store_with_invalid_token(): void
    {
        $response = $this->withHeaders([
                'Authorization' => 'Bearer invalid-token',
                'Accept' => 'application/json',
            ])->post('/api/divisas', [
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => 0.92,
            ]);

        $json = $response->json();

        $response->assertStatus(401);
        $this->assertEquals(['Unauthenticated'], $json['error']);
        $this->assertEmpty($json['body']);
    }
}
