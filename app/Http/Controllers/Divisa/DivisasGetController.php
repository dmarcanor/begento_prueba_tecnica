<?php

declare(strict_types=1);

namespace App\Http\Controllers\Divisa;

use App\Actions\Divisa\ObtenerDivisas;
use App\Http\Requests\Divisa\DivisasGetRequest;
use Illuminate\Http\JsonResponse;

class DivisasGetController
{
    public function __invoke(DivisasGetRequest $request, ObtenerDivisas $action): JsonResponse
    {
        $divisas = $action([
            'per_page' => $request->validated('per_page', 10),
            'sort_by' => $request->validated('sort_by', 'id'),
            'sort_direction' => $request->validated('sort_direction', 'desc'),
        ]);

        return response()->json([
            'error' => [],
            'body' => ['divisas' => $divisas],
        ], JsonResponse::HTTP_OK);
    }
}
