<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Exceptions\ProductoNoEncontradoException;
use App\Exceptions\CredencialesInvalidasException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Todas las peticiones API devuelven JSON
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Excepción de autenticación (token inválido o ausente)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'error' => ['Unauthenticated'],
                    'body' => [],
                ], 401);
            }
        });

        // Validación fallida (422 → 400)
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'error' => $e->errors(),
                    'body' => [],
                ], 400);
            }
        });

        // Producto no encontrado (404)
        $exceptions->render(function (ProductoNoEncontradoException $e, Request $request) {
            return response()->json([
                'error' => [$e->getMessage()],
                'body' => [],
            ], 404);
        });

        // Credenciales inválidas en login (401)
        $exceptions->render(function (CredencialesInvalidasException $e, Request $request) {
            return response()->json([
                'error' => [$e->getMessage()],
                'body' => [],
            ], 401);
        });
    })->create();
