<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Controlador para la autenticación de usuarios.
 * Todas las respuestas de la API se devuelven en inglés por convención de la API REST.
 */
class UsuarioController extends Controller
{
    public function inicioSesion(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'body' => [],
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $usuario = Usuario::where('email', $request->input('email'))->first();

        // Verificar credenciales usando Hash::check de forma explícita
        // para no depender del guard por defecto (que puede cambiar entre requests).
        if (!$usuario || !Hash::check($request->input('password'), $usuario->password)) {
            return response()->json([
                'error' => ['Unauthorized'],
                'body' => [],
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = $usuario->createToken('auth-token');

        return response()->json([
            'error' => [],
            'body' => [
                'access_token' => $token->plainTextToken,
                'token_type' => 'bearer',
                'expires_at' => now()->addMinutes((int) config('sanctum.expiration'))->toIso8601String(),
            ],
        ], JsonResponse::HTTP_OK);
    }
}
