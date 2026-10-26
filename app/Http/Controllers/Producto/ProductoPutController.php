<?php

declare(strict_types=1);

namespace App\Http\Controllers\Producto;

use App\Actions\Producto\ActualizarProducto;
use App\Http\Requests\Producto\ProductoPutRequest;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para actualizar un producto.
 */
class ProductoPutController
{
    public function __construct(private ActualizarProducto $actualizarProducto) {}

    public function __invoke(int $productoId, ProductoPutRequest $request): JsonResponse
    {
        $producto = ($this->actualizarProducto)($productoId, $request->validated());

        return response()->json([
            'error' => [],
            'body' => ['product' => $producto],
        ], JsonResponse::HTTP_OK);
    }
}
