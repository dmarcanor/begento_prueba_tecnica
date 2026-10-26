<?php

declare(strict_types=1);

namespace Tests\Feature\Product;

use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser(): string
    {
        $usuario = Usuario::factory()->create();
        return $usuario->createToken('test-token')->plainTextToken;
    }
    
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

    public function test_show_with_non_existent_producto(): void
    {
        $token = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer $token")->get('/api/products/99999');

        $json = $response->json();

        $response->assertStatus(404);
        $this->assertEquals(['Product not found'], $json['error']);
        $this->assertEmpty($json['body']);
    }

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

    public function test_destroy_with_non_existent_producto(): void
    {
        $token = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer $token")->delete('/api/products/99999');

        $json = $response->json();

        $response->assertStatus(404);
        $this->assertEquals(['Product not found'], $json['error']);
        $this->assertEmpty($json['body']);
    }

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
