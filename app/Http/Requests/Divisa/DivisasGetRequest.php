<?php

declare(strict_types=1);

namespace App\Http\Requests\Divisa;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

/**
 * Valida los parámetros de consulta para listar divisas.
 */
class DivisasGetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => 'integer|min:1|max:100',
            'sort_by' => Rule::in([
                'id', 'name', 'symbol', 'exchange_rate', 'created_at', 'updated_at',
            ]),
            'sort_direction' => Rule::in(['asc', 'desc']),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'error' => $validator->errors(),
                'body' => [],
            ], JsonResponse::HTTP_BAD_REQUEST)
        );
    }
}
