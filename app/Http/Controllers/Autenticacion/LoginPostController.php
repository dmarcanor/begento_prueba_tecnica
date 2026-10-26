<?php

declare(strict_types=1);

namespace App\Http\Controllers\Autenticacion;

use App\Actions\Usuario\LoginUsuario;
use App\Http\Requests\Usuario\LoginPostRequest;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para iniciar sesión.
 */
class LoginPostController
{
    public function __construct(private LoginUsuario $loginUsuario) {}

    public function __invoke(LoginPostRequest $request): JsonResponse
    {
        $token = ($this->loginUsuario)(
            $request->input('email'),
            $request->input('password')
        );

        return response()->json([
            'error' => [],
            'body' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_at' => now()->addMinutes((int) config('sanctum.expiration'))->toIso8601String(),
            ],
        ], JsonResponse::HTTP_OK);
    }
}
