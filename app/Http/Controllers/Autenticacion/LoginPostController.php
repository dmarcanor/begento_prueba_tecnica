<?php

declare(strict_types=1);

namespace App\Http\Controllers\Autenticacion;

use App\Actions\Usuario\LoginUsuario;
use App\Exceptions\CredencialesInvalidasException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginPostController
{
    public function __construct(private LoginUsuario $loginUsuario) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ]);

            $token = ($this->loginUsuario)(
                $validated['email'],
                $validated['password']
            );

            return response()->json([
                'error' => [],
                'body' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_at' => now()->addMinutes((int) config('sanctum.expiration'))->toIso8601String(),
                ],
            ], JsonResponse::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'body' => [],
            ], JsonResponse::HTTP_BAD_REQUEST);
        } catch (CredencialesInvalidasException $e) {
            return response()->json([
                'error' => [$e->getMessage()],
                'body' => [],
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }
    }
}
