<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Divisa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class DivisaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'per_page' => 'integer|min:1|max:100',
                'sort_by' => Rule::in([
                    'id', 'name', 'symbol', 'exchange_rate', 'created_at', 'updated_at',
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

        $divisas = Divisa::orderBy($ordenarPor, $direccion)->paginate($porPagina);

        return response()->json([
            'error' => [],
            'body' => ['divisas' => $divisas],
        ], JsonResponse::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validados = $request->validate([
                'name' => 'required|string|max:255',
                'symbol' => 'required|string|max:255',
                'exchange_rate' => ['required', 'decimal:0,8', 'min:0', 'max:99999999.99999999'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'body' => [],
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $divisa = Divisa::create($validados);

        return response()->json([
            'error' => [],
            'body' => ['divisa' => $divisa],
        ], JsonResponse::HTTP_CREATED);
    }
}
