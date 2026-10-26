<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials(): void
    {
        $usuario = \App\Models\Usuario::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response = $this->post('/api/login', [
            'email' => $usuario->email,
            'password' => 'password',
        ]);

        $json = $response->json();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'error',
            'body' => [
                'access_token',
                'token_type',
                'expires_at',
            ]
        ]);

        $this->assertEmpty($json['error']);
    }

    public function test_login_with_invalid_credentials(): void
    {
        $response = $this->post('/api/login', [
            'email' => 'invalid@example.com',
            'password' => 'invalidpassword',
        ]);

        $json = $response->json();

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'error',
            'body',
        ]);
        $this->assertEquals(['Unauthorized'], $json['error']);
        $this->assertEmpty($json['body']);
    }

    public function test_login_with_invalid_request(): void
    {
        $response = $this->post('/api/login', [
            'email' => 'non-email',
            'password' => '',
        ]);

        $json = $response->json();

        $response->assertStatus(400);
        $response->assertJsonStructure([
            'error' => [
                'email',
                'password',
            ],
            'body',
        ]);
        $this->assertEquals([
            'email' => ['The email field must be a valid email address.'],
            'password' => ['The password field is required.'],
        ], $json['error']);
        $this->assertEmpty($json['body']);
    }
}