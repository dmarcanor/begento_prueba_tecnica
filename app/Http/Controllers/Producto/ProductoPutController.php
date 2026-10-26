<?php

declare(strict_types=1);

namespace App\Http\Controllers\Producto;

use App\Actions\Producto\ActualizarProducto;
use App\Exceptions\ProductoNoEncontradoException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductoPutController
{
    public function __construct(private ActualizarProducto $actualizarProducto) {}

    public function __invoke(Request $request, int $productoId): JsonResponse
    {
        try {
            $validatedRequest = $request->validate([
                'name' => ['string', 'max:255'],
                'description' => ['string'],
                'price' => ['decimal:0,2', 'min:0', 'max:9999999999999.99'],
                'currency_id' => ['exists:divisas,id'],
                'tax_cost' => ['decimal:0,2', 'min:0', 'max:9999999999999.99'],
                'manufacturing_cost' => ['decimal:0,2', 'min:0', 'max:9999999999999.99'],
            ]);

            $producto = ($this->actualizarProducto)($productoId, $validatedRequest);

            return response()->json([
                'error' => [],
                'body' => ['product' => $producto],
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
