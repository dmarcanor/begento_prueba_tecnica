<?php

declare(strict_types=1);

namespace App\Http\Controllers\Divisa;

use App\Actions\Divisa\CrearDivisa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DivisaPostController
{
    public function __construct(private CrearDivisa $crearDivisa) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validatedRequest = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'symbol' => ['required', 'string', 'max:255'],
                'exchange_rate' => ['required', 'decimal:0,8', 'min:0', 'max:99999999.99999999'],
            ]);

            $divisa = ($this->crearDivisa)($validatedRequest);

            return response()->json([
                'error' => [],
                'body' => ['divisa' => $divisa],
            ], JsonResponse::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'body' => [],
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
