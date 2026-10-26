<?php

declare(strict_types=1);

namespace App\Http\Controllers\Producto;

use App\Actions\Producto\ObtenerProducto;
use App\Actions\Producto\ActualizarProducto;
use App\Http\Requests\Producto\ProductoPutRequest;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para actualizar un producto.
 */
class ProductoPutController
{
    public function __invoke(int $id, ProductoPutRequest $request, ObtenerProducto $obtener, ActualizarProducto $actualizar): JsonResponse
    {
        $producto = $obtener($id);

        if (!$producto) {
            return response()->json([
                'error' => ['Product not found'],
                'body' => [],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $producto = $actualizar($producto, $request->validated());

        return response()->json([
            'error' => [],
            'body' => ['product' => $producto],
        ], JsonResponse::HTTP_OK);
    }
}
