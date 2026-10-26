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
    public function __invoke(LoginPostRequest $request, LoginUsuario $action): JsonResponse
    {
        $token = $action(
            $request->input('email'),
            $request->input('password')
        );

        if (!$token) {
            return response()->json([
                'error' => ['Unauthorized'],
                'body' => [],
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

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
