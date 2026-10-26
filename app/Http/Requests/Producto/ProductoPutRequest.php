<?php

declare(strict_types=1);

namespace App\Http\Requests\Producto;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

/**
 * Valida los datos para actualizar un producto.
 */
class ProductoPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'description' => ['string'],
            'price' => ['decimal:0,2', 'min:0', 'max:9999999999999.99'],
            'currency_id' => ['exists:divisas,id'],
            'tax_cost' => ['decimal:0,2', 'min:0', 'max:9999999999999.99'],
            'manufacturing_cost' => ['decimal:0,2', 'min:0', 'max:9999999999999.99'],
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
