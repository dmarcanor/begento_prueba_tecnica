<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Controlador para la gestión de productos.
 * Todas las respuestas de la API se devuelven en inglés por convención de la API REST.
 */
class ProductoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'per_page' => 'integer|min:1|max:100',
                'sort_by' => Rule::in([
                    'id', 'name', 'description', 'price', 'currency_id', 'tax_cost', 'manufacturing_cost', 'created_at', 'updated_at',
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
        $ordenarPor = $request->input('sort_by', 'id');
        $direccion = $request->input('sort_direction', 'desc');

        // Mapeo de campos de API (inglés) a nombres de columnas (español)
        $mapeo = [
            'nombre' => 'name',
            'descripcion' => 'description',
            'precio' => 'price',
            'divisa_id' => 'currency_id',
            'costo_impuesto' => 'tax_cost',
            'costo_fabricacion' => 'manufacturing_cost',
        ];

        $columna = $mapeo[$ordenarPor] ?? $ordenarPor;

        $productos = Producto::orderBy($columna, $direccion)->paginate($porPagina);

        return response()->json([
            'error' => [],
            'body' => ['products' => $productos],
        ], JsonResponse::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validados = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => ['required', 'decimal:0,2', 'min:0', 'max:9999999999999.99'],
                'currency_id' => ['required', 'exists:divisas,id'],
                'tax_cost' => ['required', 'decimal:0,2', 'min:0', 'max:9999999999999.99'],
                'manufacturing_cost' => ['required', 'decimal:0,2', 'min:0', 'max:9999999999999.99'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'body' => [],
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $producto = Producto::create($validados);

        return response()->json([
            'error' => [],
            'body' => ['product' => $producto],
        ], JsonResponse::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'error' => ['Product not found'],
                'body' => [],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json([
            'error' => [],
            'body' => ['product' => $producto],
        ], JsonResponse::HTTP_OK);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'error' => ['Product not found'],
                'body' => [],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            $validados = $request->validate([
                'name' => 'string|max:255',
                'description' => 'string',
                'price' => ['decimal:0,2', 'min:0', 'max:9999999999999.99'],
                'currency_id' => ['exists:divisas,id'],
                'tax_cost' => ['decimal:0,2', 'min:0', 'max:9999999999999.99'],
                'manufacturing_cost' => ['decimal:0,2', 'min:0', 'max:9999999999999.99'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'body' => [],
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $producto->update($validados);

        return response()->json([
            'error' => [],
            'body' => ['product' => $producto],
        ], JsonResponse::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'error' => ['Product not found'],
                'body' => [],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $producto->delete();

        return response()->json([
            'error' => [],
            'body' => [],
        ], JsonResponse::HTTP_OK);
    }
}
