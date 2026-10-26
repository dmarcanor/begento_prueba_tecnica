<?php

declare(strict_types=1);

namespace App\Http\Controllers\Producto;

use App\Actions\Producto\ObtenerProducto;
use App\Actions\Producto\EliminarProducto;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para eliminar un producto.
 */
class ProductoDeleteController
{
    public function __invoke(int $id, ObtenerProducto $obtener, EliminarProducto $eliminar): JsonResponse
    {
        $producto = $obtener($id);

        if (!$producto) {
            return response()->json([
                'error' => ['Product not found'],
                'body' => [],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $eliminar($producto);

        return response()->json([
            'error' => [],
            'body' => [],
        ], JsonResponse::HTTP_OK);
    }
}
