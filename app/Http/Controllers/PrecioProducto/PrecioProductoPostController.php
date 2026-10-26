<?php

declare(strict_types=1);

namespace App\Http\Controllers\PrecioProducto;

use App\Actions\Producto\ObtenerProducto;
use App\Actions\PrecioProducto\CrearPrecio;
use App\Http\Requests\PrecioProducto\PrecioPostRequest;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para crear un precio de producto.
 */
class PrecioProductoPostController
{
    public function __invoke(int $idProducto, PrecioPostRequest $request, ObtenerProducto $obtenerProducto, CrearPrecio $action): JsonResponse
    {
        $producto = $obtenerProducto($idProducto);

        if (!$producto) {
            return response()->json([
                'error' => ['Product not found'],
                'body' => [],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $precio = $action($idProducto, $request->validated());

        return response()->json([
            'error' => [],
            'body' => ['price' => $precio],
        ], JsonResponse::HTTP_CREATED);
    }
}
