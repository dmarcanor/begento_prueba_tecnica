<?php

declare(strict_types=1);

namespace App\Http\Controllers\Producto;

use App\Actions\Producto\ObtenerProducto;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para obtener un producto específico.
 */
class ProductoGetController
{
    public function __invoke(int $id, ObtenerProducto $action): JsonResponse
    {
        $producto = $action($id);

        if (!$producto) {
            return response()->json([
                'error' => ['Product not found'],
                'body' => [],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json([
            'error' => [],
            'body' => ['product' => $producto],
        ], JsonResponse::HTTP_OK);
    }
}
