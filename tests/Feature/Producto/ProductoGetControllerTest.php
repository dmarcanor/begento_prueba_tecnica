<?php

declare(strict_types=1);

namespace Tests\Feature\Producto;

use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoGetControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser(): string
    {
        $usuario = Usuario::factory()->create();
        return $usuario->createToken('test-token')->plainTextToken;
    }

    /**
     * Test GET /products/{id} returns a product
     */
    public function test_show_should_return_a_producto(): void
    {
        $token = $this->createAuthenticatedUser();
        $product = Producto::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer $token")->get("/api/products/{$product->id}");

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
            ]
        ]);

        $this->assertEmpty($json['error']);
        $this->assertEquals($product->id, $json['body']['product']['id']);
        $this->assertEquals($product->name, $json['body']['product']['name']);
        $this->assertEquals($product->description, $json['body']['product']['description']);
        $this->assertEquals($product->price, $json['body']['product']['price']);
        $this->assertEquals($product->currency_id, $json['body']['product']['currency_id']);
        $this->assertEquals($product->tax_cost, $json['body']['product']['tax_cost']);
        $this->assertEquals($product->manufacturing_cost, $json['body']['product']['manufacturing_cost']);
    }

    /**
     * Test GET /products/{id} returns 404 for non-existent product
     */
    public function test_show_with_non_existent_producto(): void
    {
        $token = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer $token")->get('/api/products/99999');

        $json = $response->json();

        $response->assertStatus(404);
        $this->assertEquals(['Product not found'], $json['error']);
        $this->assertEmpty($json['body']);
    }

    /**
     * Test GET /products/{id} returns 401 without authentication
     */
    public function test_show_with_invalid_token(): void
    {
        $product = Producto::factory()->create();

        $response = $this->withHeaders([
                'Authorization' => 'Bearer invalid-token',
                'Accept' => 'application/json',
            ])->get("/api/products/{$product->id}");

        $json = $response->json();

        $response->assertStatus(401);
        $this->assertEquals(['Unauthenticated'], $json['error']);
        $this->assertEmpty($json['body']);
    }
}
