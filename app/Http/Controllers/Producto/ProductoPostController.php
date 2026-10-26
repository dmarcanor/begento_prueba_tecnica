<?php

declare(strict_types=1);

namespace App\Http\Controllers\Producto;

use App\Actions\Producto\CrearProducto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductoPostController
{
    public function __construct(private CrearProducto $action) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'price' => ['required', 'decimal:0,2', 'min:0', 'max:9999999999999.99'],
                'currency_id' => ['required', 'exists:divisas,id'],
                'tax_cost' => ['required', 'decimal:0,2', 'min:0', 'max:9999999999999.99'],
                'manufacturing_cost' => ['required', 'decimal:0,2', 'min:0', 'max:9999999999999.99'],
            ]);

            $producto = ($this->action)($validated);

            return response()->json([
                'error' => [],
                'body' => ['product' => $producto],
            ], JsonResponse::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'body' => [],
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
