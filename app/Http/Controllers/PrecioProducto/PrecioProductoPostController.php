<?php

declare(strict_types=1);

namespace App\Http\Controllers\PrecioProducto;

use App\Actions\PrecioProducto\CrearPrecio;
use App\Exceptions\ProductoNoEncontradoException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PrecioProductoPostController
{
    public function __construct(private CrearPrecio $crearPrecio) {}

    public function __invoke(Request $request, int $productoId): JsonResponse
    {
        try {
            $validatedRequest = $request->validate([
                'price' => ['required', 'decimal:0,2', 'min:0', 'max:9999999999999.99'],
                'currency_id' => ['required', 'exists:divisas,id'],
            ]);

            $precio = ($this->crearPrecio)($productoId, $validatedRequest);

            return response()->json([
                'error' => [],
                'body' => ['price' => $precio],
            ], JsonResponse::HTTP_CREATED);
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
