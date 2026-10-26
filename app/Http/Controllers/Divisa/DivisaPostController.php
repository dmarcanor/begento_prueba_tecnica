<?php

declare(strict_types=1);

namespace App\Http\Controllers\Divisa;

use App\Actions\Divisa\CrearDivisa;
use App\Http\Requests\Divisa\DivisaPostRequest;
use Illuminate\Http\JsonResponse;

class DivisaPostController
{
    public function __construct(private CrearDivisa $action) {}

    public function __invoke(DivisaPostRequest $request): JsonResponse
    {
        $divisa = ($this->action)($request->validated());

        return response()->json([
            'error' => [],
            'body' => ['divisa' => $divisa],
        ], JsonResponse::HTTP_CREATED);
    }
}
