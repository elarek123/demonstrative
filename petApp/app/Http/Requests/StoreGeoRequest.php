<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGeoRequest extends FormRequest
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
            'name' => 'required|string',
            'shipping_cost' =>'required|numeric',
            'currency_name' => 'required|string',
            'currency_value' => 'required|numeric|gt:0',
            'product_ids' => 'sometimes|array',
            'product_ids.*' => 'exists:products,id',
        ];
    }
}
