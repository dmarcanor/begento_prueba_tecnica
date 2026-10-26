<?php

declare(strict_types=1);

namespace App\Actions\Usuario;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

/**
 * Autentica un usuario y genera un token.
 */
class LoginUsuario
{
    public function __invoke(string $email, string $password): ?string
    {
        $usuario = Usuario::where('email', $email)->first();

        if (!$usuario || !Hash::check($password, $usuario->password)) {
            return null;
        }

        return $usuario->createToken('auth-token')->plainTextToken;
    }
}
