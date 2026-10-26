<?php

declare(strict_types=1);

namespace App\Http\Controllers\PrecioProducto;

use App\Actions\PrecioProducto\ObtenerPrecios;
use App\Http\Requests\PrecioProducto\PreciosGetRequest;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para listar precios de un producto.
 */
class PrecioProductosGetController
{
    public function __construct(private ObtenerPrecios $obtenerPrecios) {}

    public function __invoke(int $productoId, PreciosGetRequest $request): JsonResponse
    {
        $precios = ($this->obtenerPrecios)($productoId, [
            'per_page' => $request->validated('per_page', 10),
            'sort_by' => $request->validated('sort_by', 'created_at'),
            'sort_direction' => $request->validated('sort_direction', 'desc'),
        ]);

        return response()->json([
            'error' => [],
            'body' => ['prices' => $precios],
        ], JsonResponse::HTTP_OK);
    }
}
