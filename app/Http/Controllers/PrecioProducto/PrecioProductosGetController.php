<?php

declare(strict_types=1);

namespace App\Http\Controllers\PrecioProducto;

use App\Actions\PrecioProducto\ObtenerPrecios;
use App\Exceptions\ProductoNoEncontradoException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PrecioProductosGetController
{
    public function __construct(private ObtenerPrecios $obtenerPrecios) {}

    public function __invoke(Request $request, int $productoId): JsonResponse
    {
        try {
            $validatedRequest = $request->validate([
                'per_page' => ['integer', 'min:1', 'max:100'],
                'sort_by' => Rule::in([
                    'id', 'product_id', 'currency_id', 'price', 'created_at', 'updated_at',
                ]),
                'sort_direction' => Rule::in(['asc', 'desc']),
            ]);

            $precios = ($this->obtenerPrecios)($productoId, [
                'per_page' => $validatedRequest['per_page'] ?? 10,
                'sort_by' => $validatedRequest['sort_by'] ?? 'created_at',
                'sort_direction' => $validatedRequest['sort_direction'] ?? 'desc',
            ]);

            return response()->json([
                'error' => [],
                'body' => ['prices' => $precios],
            ], JsonResponse::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'body' => [],
            ], JsonResponse::HTTP_BAD_REQUEST);
        } catch (ProductoNoEncontradoException $e) {
            return response()->json([
                'error' => [$e->getMessage()],
                'body' => [],
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
