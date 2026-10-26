<?php

declare(strict_types=1);

namespace App\Http\Requests\PrecioProducto;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

/**
 * Valida los datos para crear un precio de producto.
 */
class PrecioPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'price' => ['required', 'decimal:0,2', 'min:0', 'max:9999999999999.99'],
            'currency_id' => ['required', 'exists:divisas,id'],
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
