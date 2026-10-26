<?php

declare(strict_types=1);

namespace App\Http\Controllers\Producto;

use App\Actions\Producto\CrearProducto;
use App\Http\Requests\Producto\ProductoPostRequest;
use Illuminate\Http\JsonResponse;

class ProductoPostController
{
    public function __construct(private CrearProducto $action) {}

    public function __invoke(ProductoPostRequest $request): JsonResponse
    {
        $producto = ($this->action)($request->validated());

        return response()->json([
            'error' => [],
            'body' => ['product' => $producto],
        ], JsonResponse::HTTP_CREATED);
    }
}
