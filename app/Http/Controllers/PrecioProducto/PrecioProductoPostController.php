<?php

declare(strict_types=1);

namespace App\Http\Controllers\PrecioProducto;

use App\Actions\PrecioProducto\CrearPrecio;
use App\Http\Requests\PrecioProducto\PrecioPostRequest;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para crear un precio de producto.
 */
class PrecioProductoPostController
{
    public function __construct(private CrearPrecio $crearPrecio) {}

    public function __invoke(int $productoId, PrecioPostRequest $request): JsonResponse
    {
        $precio = ($this->crearPrecio)($productoId, $request->validated());

        return response()->json([
            'error' => [],
            'body' => ['price' => $precio],
        ], JsonResponse::HTTP_CREATED);
    }
}
