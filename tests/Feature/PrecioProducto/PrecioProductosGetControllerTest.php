<?php

declare(strict_types=1);

namespace Tests\Feature\PrecioProducto;

use App\Models\Divisa;
use App\Models\PrecioProducto;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrecioProductosGetControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser(): string
    {
        $usuario = Usuario::factory()->create();
        return $usuario->createToken('test-token')->plainTextToken;
    }

    /**
     * Test GET /products/{id}/prices returns prices for existing product
     */
    public function test_list_prices_for_product_success(): void
    {
        $token = $this->createAuthenticatedUser();
        
        $divisa = Divisa::factory()->create();
        $producto = Producto::factory()->create();
        
        // Create 3 prices for the product
        PrecioProducto::factory()->count(3)->create([
            'product_id' => $producto->id,
            'currency_id' => $divisa->id,
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->get("/api/products/{$producto->id}/prices");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'error',
                'body' => [
                    'prices' => [
                        'current_page',
                        'data' => [
                            '*' => [
                                'id',
                                'product_id',
                                'currency_id',
                                'price',
                                'created_at',
                                'updated_at',
                                'divisa' => [
                                    'id',
                                    'name',
                                    'symbol',
                                    'exchange_rate',
                                ],
                            ],
                        ],
                        'per_page',
                        'total',
                    ],
                ],
            ]);
        
        $this->assertEmpty($response->json('error'));
        $responseData = $response->json('body.prices');
        $this->assertEquals(3, $responseData['total']);
        $this->assertCount(3, $responseData['data']);
    }

    /**
     * Test GET /products/{id}/prices returns empty array for product with no prices
     */
    public function test_list_prices_for_product_with_no_prices(): void
    {
        $token = $this->createAuthenticatedUser();
        
        $producto = Producto::factory()->create();
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->get("/api/products/{$producto->id}/prices");
        
        $response->assertStatus(200);
        
        $this->assertEmpty($response->json('error'));
        $responseData = $response->json('body.prices');
        $this->assertEquals(0, $responseData['total']);
        $this->assertCount(0, $responseData['data']);
    }

    /**
     * Test GET /products/{id}/prices returns 404 for non-existent product
     */
    public function test_list_prices_for_non_existent_product(): void
    {
        $token = $this->createAuthenticatedUser();
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->get('/api/products/999999/prices');
        
        $json = $response->json();
        
        $response->assertStatus(404);
        $this->assertEquals(['Product not found'], $json['error']);
        $this->assertEmpty($json['body']);
    }

    /**
     * Test GET /products/{id}/prices returns 401 without authentication
     */
    public function test_list_prices_without_authentication(): void
    {
        $producto = Producto::factory()->create();
        
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get("/api/products/{$producto->id}/prices");
        
        $json = $response->json();
        
        $response->assertStatus(401);
        $this->assertEquals(['Unauthenticated'], $json['error']);
        $this->assertEmpty($json['body']);
    }

    /**
     * Test GET /products/{id}/prices supports custom pagination
     */
    public function test_list_prices_supports_custom_pagination(): void
    {
        $token = $this->createAuthenticatedUser();
        
        $divisa = Divisa::factory()->create();
        $producto = Producto::factory()->create();
        
        // Create 15 prices
        PrecioProducto::factory()->count(15)->create([
            'product_id' => $producto->id,
            'currency_id' => $divisa->id,
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->get("/api/products/{$producto->id}/prices?per_page=5");
        
        $response->assertStatus(200);
        $responseData = $response->json('body.prices');
        $this->assertEquals(5, $responseData['per_page']);
        $this->assertEquals(15, $responseData['total']);
        $this->assertEquals(3, $responseData['last_page']);
        $this->assertCount(5, $responseData['data']);
    }

    /**
     * Test GET /products/{id}/prices supports custom sorting
     */
    public function test_list_prices_supports_custom_sorting(): void
    {
        $token = $this->createAuthenticatedUser();
        
        $divisa = Divisa::factory()->create();
        $producto = Producto::factory()->create();
        
        PrecioProducto::factory()->create([
            'product_id' => $producto->id,
            'currency_id' => $divisa->id,
            'price' => 100.00,
        ]);
        
        PrecioProducto::factory()->create([
            'product_id' => $producto->id,
            'currency_id' => $divisa->id,
            'price' => 50.00,
        ]);
        
        PrecioProducto::factory()->create([
            'product_id' => $producto->id,
            'currency_id' => $divisa->id,
            'price' => 200.00,
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->get("/api/products/{$producto->id}/prices?sort_by=price&sort_direction=asc");
        
        $response->assertStatus(200);
        $responseData = $response->json('body.prices.data');
        $this->assertEquals(50.00, $responseData[0]['price']);
        $this->assertEquals(100.00, $responseData[1]['price']);
        $this->assertEquals(200.00, $responseData[2]['price']);
    }

    /**
     * Test GET /products/{id}/prices validates sort parameters
     */
    public function test_list_prices_validates_sort_parameters(): void
    {
        $token = $this->createAuthenticatedUser();
        $producto = Producto::factory()->create();
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->get("/api/products/{$producto->id}/prices?sort_by=invalid_field&sort_direction=invalid&per_page=500");
        
        $json = $response->json();
        
        $response->assertStatus(400);
        $this->assertArrayHasKey('sort_by', $json['error']);
        $this->assertArrayHasKey('sort_direction', $json['error']);
        $this->assertArrayHasKey('per_page', $json['error']);
        $this->assertEmpty($json['body']);
    }
}
