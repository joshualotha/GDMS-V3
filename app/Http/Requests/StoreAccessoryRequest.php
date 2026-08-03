<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccessoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $accessoryId = $this->route('accessory');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('accessories')->ignore($accessoryId?->id),
            ],
            'sku' => 'nullable|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }
}
