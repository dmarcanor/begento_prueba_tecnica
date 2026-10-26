<?php

declare(strict_types=1);

namespace App\Http\Controllers\Producto;

use App\Actions\Producto\ObtenerProductos;
use App\Http\Requests\Producto\ProductosGetRequest;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para listar productos.
 */
class ProductosGetController
{
    public function __invoke(ProductosGetRequest $request, ObtenerProductos $action): JsonResponse
    {
        $productos = $action([
            'per_page' => $request->validated('per_page', 10),
            'sort_by' => $request->validated('sort_by', 'id'),
            'sort_direction' => $request->validated('sort_direction', 'desc'),
        ]);

        return response()->json([
            'error' => [],
            'body' => ['products' => $productos],
        ], JsonResponse::HTTP_OK);
    }
}
