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
    public function __construct(private ObtenerProducto $obtenerProducto) {}

    public function __invoke(int $productoId): JsonResponse
    {
        $producto = ($this->obtenerProducto)($productoId);

        return response()->json([
            'error' => [],
            'body' => ['product' => $producto],
        ], JsonResponse::HTTP_OK);
    }
}
