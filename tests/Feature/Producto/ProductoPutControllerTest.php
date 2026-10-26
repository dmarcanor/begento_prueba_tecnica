<?php

declare(strict_types=1);

namespace Tests\Feature\Producto;

use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoPutControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser(): string
    {
        $usuario = Usuario::factory()->create();
        return $usuario->createToken('test-token')->plainTextToken;
    }

    /**
     * Test PUT /products/{id} updates a product
     */
    public function test_update_should_update_a_producto(): void
    {
        $token = $this->createAuthenticatedUser();
        $product = Producto::factory()->create();

        $newData = [
            'name' => 'Updated Product Name',
            'description' => 'Updated description',
            'price' => 150.50,
            'tax_cost' => 15.00,
            'manufacturing_cost' => 80.00,
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")->put("/api/products/{$product->id}", $newData);

        $json = $response->json();

        $response->assertStatus(200);
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
            ],
        ]);

        $this->assertEmpty($json['error']);
        $this->assertEquals($newData['name'], $json['body']['product']['name']);
        $this->assertEquals($newData['description'], $json['body']['product']['description']);
        $this->assertEquals($newData['price'], $json['body']['product']['price']);
        $this->assertEquals($newData['tax_cost'], $json['body']['product']['tax_cost']);
        $this->assertEquals($newData['manufacturing_cost'], $json['body']['product']['manufacturing_cost']);
    }

    /**
     * Test PUT /products/{id} returns 400 with invalid data
     */
    public function test_update_with_invalid_request(): void
    {
        $token = $this->createAuthenticatedUser();
        $product = Producto::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer $token")->put("/api/products/{$product->id}", [
            'name' => '',
            'price' => -1,
            'currency_id' => 999999,
            'tax_cost' => -5,
            'manufacturing_cost' => -10,
        ]);

        $json = $response->json();

        $response->assertStatus(400);
        $this->assertEmpty($json['body']);
        $this->assertEquals(['The price field must be at least 0.'], $json['error']['price']);
        $this->assertEquals(['The selected currency id is invalid.'], $json['error']['currency_id']);
        $this->assertEquals(['The tax cost field must be at least 0.'], $json['error']['tax_cost']);
        $this->assertEquals(['The manufacturing cost field must be at least 0.'], $json['error']['manufacturing_cost']);
    }

    /**
     * Test PUT /products/{id} returns 404 for non-existent product
     */
    public function test_update_with_non_existent_producto(): void
    {
        $token = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer $token")->put('/api/products/99999', [
            'name' => 'Updated Name',
        ]);

        $json = $response->json();

        $response->assertStatus(404);
        $this->assertEquals(['Product not found'], $json['error']);
        $this->assertEmpty($json['body']);
    }

    /**
     * Test PUT /products/{id} returns 401 without authentication
     */
    public function test_update_with_invalid_token(): void
    {
        $product = Producto::factory()->create();

        $response = $this->withHeaders([
                'Authorization' => 'Bearer invalid-token',
                'Accept' => 'application/json',
            ])->put("/api/products/{$product->id}", [
                'name' => 'Updated Name',
            ]);

        $json = $response->json();

        $response->assertStatus(401);
        $this->assertEquals(['Unauthenticated'], $json['error']);
        $this->assertEmpty($json['body']);
    }
}
