<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PrecioProducto;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Controlador para la gestión de precios de productos.
 * Todas las respuestas de la API se devuelven en inglés por convención de la API REST.
 */
class PrecioProductoController extends Controller
{
    public function index(Request $request, int $idProducto): JsonResponse
    {
        $producto = Producto::find($idProducto);

        if (!$producto) {
            return response()->json([
                'error' => ['Product not found'],
                'body' => [],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            $request->validate([
                'per_page' => 'integer|min:1|max:100',
                'sort_by' => Rule::in([
                    'id', 'product_id', 'currency_id', 'price', 'created_at', 'updated_at',
                ]),
                'sort_direction' => Rule::in(['asc', 'desc']),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'body' => [],
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $porPagina = $request->input('per_page', 10);
        $ordenarPor = $request->input('sort_by', 'created_at');
        $direccion = $request->input('sort_direction', 'desc');

        $precios = PrecioProducto::where('product_id', $idProducto)
            ->with('divisa')
            ->orderBy($ordenarPor, $direccion)
            ->paginate($porPagina);

        return response()->json([
            'error' => [],
            'body' => ['prices' => $precios],
        ], JsonResponse::HTTP_OK);
    }

    public function store(Request $request, int $idProducto): JsonResponse
    {
        $producto = Producto::find($idProducto);

        if (!$producto) {
            return response()->json([
                'error' => ['Product not found'],
                'body' => [],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            $validados = $request->validate([
                'price' => ['required', 'decimal:0,2', 'min:0', 'max:9999999999999.99'],
                'currency_id' => ['required', 'exists:divisas,id'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'body' => [],
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $precio = PrecioProducto::create([
            'product_id' => $idProducto,
            'price' => $validados['price'],
            'currency_id' => $validados['currency_id'],
        ]);

        $precio->load('divisa');

        return response()->json([
            'error' => [],
            'body' => ['price' => $precio],
        ], JsonResponse::HTTP_CREATED);
    }
}
