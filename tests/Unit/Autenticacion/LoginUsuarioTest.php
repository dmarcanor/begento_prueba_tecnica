<?php

declare(strict_types=1);

namespace Tests\Unit\Autenticacion;

use App\Actions\Autenticacion\LoginUsuario;
use App\Exceptions\CredencialesInvalidasException;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginUsuarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_token_with_valid_credentials(): void
    {
        $usuario = Usuario::factory()->create(['password' => 'correct-password']);

        $action = new LoginUsuario();
        $token = $action($usuario->email, 'correct-password');

        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    public function test_throws_with_invalid_password(): void
    {
        $usuario = Usuario::factory()->create(['password' => 'correct-password']);

        $action = new LoginUsuario();

        $this->expectException(CredencialesInvalidasException::class);
        $this->expectExceptionMessage('Unauthorized');

        $action($usuario->email, 'wrong-password');
    }

    public function test_throws_with_non_existent_email(): void
    {
        $action = new LoginUsuario();

        $this->expectException(CredencialesInvalidasException::class);
        $this->expectExceptionMessage('Unauthorized');

        $action('nonexistent@example.com', 'any-password');
    }
}
