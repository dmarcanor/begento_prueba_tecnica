<?php

declare(strict_types=1);

namespace Tests\Feature\Producto;

use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoPostControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser(): string
    {
        $usuario = Usuario::factory()->create();
        return $usuario->createToken('test-token')->plainTextToken;
    }

    /**
     * Test POST /products creates a new product
     */
    public function test_store_should_create_a_new_producto(): void
    {
        $token = $this->createAuthenticatedUser();
        $product = Producto::factory()->make();

        $response = $this->withHeader('Authorization', "Bearer $token")->post('/api/products', [
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'currency_id' => $product->currency_id,
            'tax_cost' => $product->tax_cost,
            'manufacturing_cost' => $product->manufacturing_cost,
        ]);

        $json = $response->json();

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'error',
            'body' => [
                'product' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'currency_id',
                    'tax_cost',
                    'manufacturing_cost',
                    'created_at',
                    'updated_at',
                ],
            ]
        ]);

        $this->assertEmpty($json['error']);
        $this->assertEquals($product->name, $json['body']['product']['name']);
        $this->assertEquals($product->description, $json['body']['product']['description']);
        $this->assertEquals($product->price, $json['body']['product']['price']);
        $this->assertEquals($product->currency_id, $json['body']['product']['currency_id']);
        $this->assertEquals($product->tax_cost, $json['body']['product']['tax_cost']);
        $this->assertEquals($product->manufacturing_cost, $json['body']['product']['manufacturing_cost']);
    }

    /**
     * Test POST /products returns 400 with invalid data
     */
    public function test_store_with_invalid_request(): void
    {
        $token = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer $token")->post('/api/products', [
            'name' => '',
            'description' => '',
            'price' => -1,
            'currency_id' => 999999,
            'tax_cost' => -5,
            'manufacturing_cost' => -10,
        ]);

        $json = $response->json();

        $response->assertStatus(400);
        $response->assertJsonStructure([
            'error' => [
                'name',
                'price',
                'currency_id',
                'tax_cost',
                'manufacturing_cost',
            ],
            'body'
        ]);

        $this->assertEmpty($json['body']);
        $this->assertEquals(['The name field is required.'], $json['error']['name']);
        $this->assertEquals(['The price field must be at least 0.'], $json['error']['price']);
        $this->assertEquals(['The selected currency id is invalid.'], $json['error']['currency_id']);
        $this->assertEquals(['The tax cost field must be at least 0.'], $json['error']['tax_cost']);
        $this->assertEquals(['The manufacturing cost field must be at least 0.'], $json['error']['manufacturing_cost']);
    }

    /**
     * Test POST /products returns 401 without authentication
     */
    public function test_store_with_invalid_token(): void
    {
        $response = $this->withHeaders([
                'Authorization' => 'Bearer invalid-token',
                'Accept' => 'application/json',
            ])->post('/api/products', [
                'name' => 'Test Product',
                'description' => 'Test Description',
                'price' => 100,
                'currency_id' => 1,
                'tax_cost' => 10,
                'manufacturing_cost' => 50,
            ]);

        $json = $response->json();

        $response->assertStatus(401);
        $this->assertEquals(['Unauthenticated'], $json['error']);
        $this->assertEmpty($json['body']);
    }
}
