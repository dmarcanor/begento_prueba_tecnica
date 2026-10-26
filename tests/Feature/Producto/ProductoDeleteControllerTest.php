<?php

declare(strict_types=1);

namespace Tests\Feature\Producto;

use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoDeleteControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser(): string
    {
        $usuario = Usuario::factory()->create();
        return $usuario->createToken('test-token')->plainTextToken;
    }

    /**
     * Test DELETE /products/{id} deletes a product
     */
    public function test_destroy_should_delete_a_producto(): void
    {
        $token = $this->createAuthenticatedUser();
        $product = Producto::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer $token")->delete("/api/products/{$product->id}");

        $json = $response->json();

        $response->assertStatus(200);
        $this->assertEmpty($json['error']);
        $this->assertEmpty($json['body']);

        $this->assertSoftDeleted('productos', ['id' => $product->id]);
    }

    /**
     * Test DELETE /products/{id} returns 404 for non-existent product
     */
    public function test_destroy_with_non_existent_producto(): void
    {
        $token = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer $token")->delete('/api/products/99999');

        $json = $response->json();

        $response->assertStatus(404);
        $this->assertEquals(['Product not found'], $json['error']);
        $this->assertEmpty($json['body']);
    }

    /**
     * Test DELETE /products/{id} returns 401 without authentication
     */
    public function test_destroy_with_invalid_token(): void
    {
        $product = Producto::factory()->create();

        $response = $this->withHeaders([
                'Authorization' => 'Bearer invalid-token',
                'Accept' => 'application/json',
            ])->delete("/api/products/{$product->id}");

        $json = $response->json();

        $response->assertStatus(401);
        $this->assertEquals(['Unauthenticated'], $json['error']);
        $this->assertEmpty($json['body']);
    }
}
