<?php

declare(strict_types=1);

namespace Tests\Feature\Producto;

use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductosGetControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser(): string
    {
        $usuario = Usuario::factory()->create();
        return $usuario->createToken('test-token')->plainTextToken;
    }

    /**
     * Test GET /products returns paginated products list
     */
    public function test_index_should_list_products_with_default_pagination(): void
    {
        $token = $this->createAuthenticatedUser();
        Producto::factory()->count(15)->create();

        $response = $this->withHeader('Authorization', "Bearer $token")->get('/api/products');

        $json = $response->json();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'error',
            'body' => [
                'products' => [
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
        $this->assertEquals(1, $json['body']['products']['current_page']);
        $this->assertEquals(10, $json['body']['products']['per_page']);
        $this->assertEquals(15, $json['body']['products']['total']);
        $this->assertEquals(2, $json['body']['products']['last_page']);
        $this->assertCount(10, $json['body']['products']['data']);
    }

    /**
     * Test GET /products supports custom pagination
     */
    public function test_index_should_support_custom_pagination(): void
    {
        $token = $this->createAuthenticatedUser();
        Producto::factory()->count(25)->create();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->get('/api/products?per_page=5&page=2');

        $json = $response->json();

        $response->assertStatus(200);
        $this->assertEquals(2, $json['body']['products']['current_page']);
        $this->assertEquals(5, $json['body']['products']['per_page']);
        $this->assertEquals(25, $json['body']['products']['total']);
        $this->assertEquals(5, $json['body']['products']['last_page']);
        $this->assertCount(5, $json['body']['products']['data']);
    }

    /**
     * Test GET /products supports custom sorting
     */
    public function test_index_should_support_custom_sorting(): void
    {
        $token = $this->createAuthenticatedUser();

        $product1 = Producto::factory()->create(['price' => 100]);
        $product2 = Producto::factory()->create(['price' => 300]);
        $product3 = Producto::factory()->create(['price' => 200]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->get('/api/products?sort_by=price&sort_direction=asc');

        $json = $response->json();

        $response->assertStatus(200);
        $this->assertEquals($product1->id, $json['body']['products']['data'][0]['id']);
        $this->assertEquals($product3->id, $json['body']['products']['data'][1]['id']);
        $this->assertEquals($product2->id, $json['body']['products']['data'][2]['id']);
    }

    /**
     * Test GET /products validates sort parameters
     */
    public function test_index_should_validate_sort_parameters(): void
    {
        $token = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->get('/api/products?sort_by=invalid_field');

        $json = $response->json();

        $response->assertStatus(400);
        $this->assertArrayHasKey('sort_by', $json['error']);
        $this->assertEmpty($json['body']);
    }

    /**
     * Test GET /products returns 401 without authentication
     */
    public function test_index_with_invalid_token(): void
    {
        $response = $this->withHeaders([
                'Authorization' => 'Bearer invalid-token',
                'Accept' => 'application/json',
            ])->get('/api/products');

        $json = $response->json();

        $response->assertStatus(401);
        $this->assertEquals(['Unauthenticated'], $json['error']);
        $this->assertEmpty($json['body']);
    }
}
