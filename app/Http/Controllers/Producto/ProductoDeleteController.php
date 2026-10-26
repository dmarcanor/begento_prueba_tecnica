<?php

declare(strict_types=1);

namespace App\Http\Controllers\Producto;

use App\Actions\Producto\EliminarProducto;
use App\Exceptions\ProductoNoEncontradoException;
use Illuminate\Http\JsonResponse;

class ProductoDeleteController
{
    public function __construct(private EliminarProducto $action) {}

    public function __invoke(int $productoId): JsonResponse
    {
        try {
            ($this->action)($productoId);

            return response()->json([
                'error' => [],
                'body' => [],
            ], JsonResponse::HTTP_OK);
        } catch (ProductoNoEncontradoException $e) {
            return response()->json([
                'error' => [$e->getMessage()],
                'body' => [],
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
