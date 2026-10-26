<?php

declare(strict_types=1);

namespace App\Http\Controllers\Producto;

use App\Actions\Producto\ObtenerProductos;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductosGetController
{
    public function __construct(private ObtenerProductos $obtenerProductos) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validatedRequest = $request->validate([
                'per_page' => ['integer', 'min:1', 'max:100'],
                'sort_by' => Rule::in([
                    'id', 'name', 'description', 'price', 'currency_id', 'tax_cost', 'manufacturing_cost', 'created_at', 'updated_at',
                ]),
                'sort_direction' => Rule::in(['asc', 'desc']),
            ]);

            $productos = ($this->obtenerProductos)([
                'per_page' => $validatedRequest['per_page'] ?? 10,
                'sort_by' => $validatedRequest['sort_by'] ?? 'id',
                'sort_direction' => $validatedRequest['sort_direction'] ?? 'desc',
            ]);

            return response()->json([
                'error' => [],
                'body' => ['products' => $productos],
            ], JsonResponse::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'body' => [],
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
