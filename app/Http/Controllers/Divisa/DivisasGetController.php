<?php

declare(strict_types=1);

namespace App\Http\Controllers\Divisa;

use App\Actions\Divisa\ObtenerDivisas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DivisasGetController
{
    public function __construct(private ObtenerDivisas $obtenerDivisas) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validatedRequest = $request->validate([
                'per_page' => ['integer', 'min:1', 'max:100'],
                'sort_by' => Rule::in(['id', 'name', 'symbol', 'exchange_rate', 'created_at', 'updated_at']),
                'sort_direction' => Rule::in(['asc', 'desc']),
            ]);

            $divisas = ($this->obtenerDivisas)([
                'per_page' => $validatedRequest['per_page'] ?? 10,
                'sort_by' => $validatedRequest['sort_by'] ?? 'id',
                'sort_direction' => $validatedRequest['sort_direction'] ?? 'desc',
            ]);

            return response()->json([
                'error' => [],
                'body' => ['divisas' => $divisas],
            ], JsonResponse::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'body' => [],
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
