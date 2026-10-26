<?php

declare(strict_types=1);

namespace App\Http\Controllers\Producto;

use App\Actions\Producto\EliminarProducto;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para eliminar un producto.
 */
class ProductoDeleteController
{
    public function __construct(private EliminarProducto $eliminarProducto) {}

    public function __invoke(int $id): JsonResponse
    {
        ($this->eliminarProducto)($id);

        return response()->json([
            'error' => [],
            'body' => [],
        ], JsonResponse::HTTP_OK);
    }
}
