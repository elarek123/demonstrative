<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string',
            'shipping_cost' => 'sometimes|numeric',
            'currency_name' => 'sometimes|string',
            'currency_value' => 'sometimes|numeric|gt:0',
            'product_ids' => 'sometimes|array',
            'product_ids.*' => 'exists:products,id',
        ];
    }
}
