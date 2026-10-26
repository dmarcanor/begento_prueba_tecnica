<?php

declare(strict_types=1);

namespace App\Http\Controllers\Divisa;

use App\Actions\Divisa\CrearDivisa;
use App\Http\Requests\Divisa\DivisaPostRequest;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para crear una divisa.
 */
class DivisaPostController
{
    public function __invoke(DivisaPostRequest $request, CrearDivisa $action): JsonResponse
    {
        $divisa = $action($request->validated());

        return response()->json([
            'error' => [],
            'body' => ['divisa' => $divisa],
        ], JsonResponse::HTTP_CREATED);
    }
}
