<?php

declare(strict_types=1);

namespace Tests\Feature\PrecioProducto;

use App\Models\Divisa;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrecioProductoPostControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser(): string
    {
        $usuario = Usuario::factory()->create();
        return $usuario->createToken('test-token')->plainTextToken;
    }

    /**
     * Test POST /products/{id}/prices creates price successfully
     */
    public function test_create_price_for_product_success(): void
    {
        $token = $this->createAuthenticatedUser();
        
        $divisa = Divisa::factory()->create();
        $producto = Producto::factory()->create();
        
        $priceData = [
            'price' => 99.99,
            'currency_id' => $divisa->id,
        ];
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->postJson("/api/products/{$producto->id}/prices", $priceData);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'error',
                'body' => [
                    'price' => [
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
            ]);
        
        $this->assertEmpty($response->json('error'));
        $responseData = $response->json('body.price');
        $this->assertEquals($producto->id, $responseData['product_id']);
        $this->assertEquals($divisa->id, $responseData['currency_id']);
        $this->assertEquals(99.99, $responseData['price']);
    }

    /**
     * Test POST /products/{id}/prices returns 404 for non-existent product
     */
    public function test_create_price_for_non_existent_product(): void
    {
        $token = $this->createAuthenticatedUser();
        
        $divisa = Divisa::factory()->create();
        
        $priceData = [
            'price' => 99.99,
            'currency_id' => $divisa->id,
        ];
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->postJson('/api/products/999999/prices', $priceData);
        
        $json = $response->json();
        
        $response->assertStatus(404);
        $this->assertEquals(['Product not found'], $json['error']);
        $this->assertEmpty($json['body']);
    }

    /**
     * Test POST /products/{id}/prices returns 400 when required fields are missing
     */
    public function test_create_price_without_required_fields(): void
    {
        $token = $this->createAuthenticatedUser();
        $producto = Producto::factory()->create();
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->postJson("/api/products/{$producto->id}/prices", []);
        
        $json = $response->json();
        
        $response->assertStatus(400);
        $this->assertEquals(['The price field is required.'], $json['error']['price']);
        $this->assertEquals(['The currency id field is required.'], $json['error']['currency_id']);
        $this->assertEmpty($json['body']);
    }

    /**
     * Test POST /products/{id}/prices returns 400 when price is negative
     */
    public function test_create_price_with_negative_price(): void
    {
        $token = $this->createAuthenticatedUser();
        
        $divisa = Divisa::factory()->create();
        $producto = Producto::factory()->create();
        
        $priceData = [
            'price' => -10.00,
            'currency_id' => $divisa->id,
        ];
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->postJson("/api/products/{$producto->id}/prices", $priceData);
        
        $json = $response->json();
        
        $response->assertStatus(400);
        $this->assertEquals(['The price field must be at least 0.'], $json['error']['price']);
        $this->assertEmpty($json['body']);
    }

    /**
     * Test POST /products/{id}/prices returns 400 when currency_id doesn't exist
     */
    public function test_create_price_with_invalid_currency_id(): void
    {
        $token = $this->createAuthenticatedUser();
        
        $producto = Producto::factory()->create();
        
        $priceData = [
            'price' => 99.99,
            'currency_id' => 999999,
        ];
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->postJson("/api/products/{$producto->id}/prices", $priceData);
        
        $json = $response->json();
        
        $response->assertStatus(400);
        $this->assertEquals(['The selected currency id is invalid.'], $json['error']['currency_id']);
        $this->assertEmpty($json['body']);
    }

    /**
     * Test POST /products/{id}/prices returns 401 without authentication
     */
    public function test_create_price_without_authentication(): void
    {
        $producto = Producto::factory()->create();
        $divisa = Divisa::factory()->create();
        
        $priceData = [
            'price' => 99.99,
            'currency_id' => $divisa->id,
        ];
        
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson("/api/products/{$producto->id}/prices", $priceData);
        
        $json = $response->json();
        
        $response->assertStatus(401);
        $this->assertEquals(['Unauthenticated'], $json['error']);
        $this->assertEmpty($json['body']);
    }
}
