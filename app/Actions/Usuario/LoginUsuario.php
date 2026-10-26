<?php

declare(strict_types=1);

namespace App\Actions\Usuario;

use App\Exceptions\CredencialesInvalidasException;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class LoginUsuario
{
    public function __invoke(string $email, string $password): string
    {
        $usuario = Usuario::where('email', $email)->first();

        if (!$usuario || !Hash::check($password, $usuario->password)) {
            throw new CredencialesInvalidasException();
        }

        return $usuario->createToken('auth-token')->plainTextToken;
    }
}
