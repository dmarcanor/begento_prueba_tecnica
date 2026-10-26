<?php

declare(strict_types=1);

namespace App\Http\Requests\Divisa;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

/**
 * Valida los datos para crear una divisa.
 */
class DivisaPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:255',
            'exchange_rate' => ['required', 'decimal:0,8', 'min:0', 'max:99999999.99999999'],
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
