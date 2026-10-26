<?php

declare(strict_types=1);

namespace Tests\Feature\Currency;

use App\Models\Divisa;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DivisaControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser(): string
    {
        $usuario = Usuario::factory()->create();
        return $usuario->createToken('test-token')->plainTextToken;
    }

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

    public function test_index_should_list_divisas_with_default_pagination(): void
    {
        $token = $this->createAuthenticatedUser();
        Divisa::factory()->count(15)->create();

        $response = $this->withHeader('Authorization', "Bearer $token")->get('/api/divisas');

        $json = $response->json();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'error',
            'body' => [
                'divisas' => [
                    'current_page',
                    'data',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ],
            ],
        ]);

        $this->assertEmpty($json['error']);
        $this->assertEquals(1, $json['body']['divisas']['current_page']);
        $this->assertEquals(10, $json['body']['divisas']['per_page']);
        $this->assertEquals(15, $json['body']['divisas']['total']);
        $this->assertEquals(2, $json['body']['divisas']['last_page']);
        $this->assertCount(10, $json['body']['divisas']['data']);
    }

    public function test_index_should_support_custom_pagination(): void
    {
        $token = $this->createAuthenticatedUser();
        Divisa::factory()->count(25)->create();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->get('/api/divisas?per_page=5&page=2');

        $json = $response->json();

        $response->assertStatus(200);
        $this->assertEquals(2, $json['body']['divisas']['current_page']);
        $this->assertEquals(5, $json['body']['divisas']['per_page']);
        $this->assertEquals(25, $json['body']['divisas']['total']);
        $this->assertEquals(5, $json['body']['divisas']['last_page']);
        $this->assertCount(5, $json['body']['divisas']['data']);
    }

    public function test_index_should_support_custom_sorting(): void
    {
        $token = $this->createAuthenticatedUser();

        $divisa1 = Divisa::factory()->create(['exchange_rate' => 0.5]);
        $divisa2 = Divisa::factory()->create(['exchange_rate' => 2.0]);
        $divisa3 = Divisa::factory()->create(['exchange_rate' => 1.0]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->get('/api/divisas?sort_by=exchange_rate&sort_direction=asc');

        $json = $response->json();

        $response->assertStatus(200);
        $this->assertEquals($divisa1->id, $json['body']['divisas']['data'][0]['id']);
        $this->assertEquals($divisa3->id, $json['body']['divisas']['data'][1]['id']);
        $this->assertEquals($divisa2->id, $json['body']['divisas']['data'][2]['id']);
    }

    public function test_index_should_validate_sort_parameters(): void
    {
        $token = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->get('/api/divisas?sort_by=invalid_field');

        $json = $response->json();

        $response->assertStatus(400);
        $this->assertArrayHasKey('sort_by', $json['error']);
        $this->assertEmpty($json['body']);
    }

    public function test_index_with_invalid_token(): void
    {
        $response = $this->withHeaders([
                'Authorization' => 'Bearer invalid-token',
                'Accept' => 'application/json',
            ])->get('/api/divisas');

        $json = $response->json();

        $response->assertStatus(401);
        $this->assertEquals(['Unauthenticated'], $json['error']);
        $this->assertEmpty($json['body']);
    }
}
