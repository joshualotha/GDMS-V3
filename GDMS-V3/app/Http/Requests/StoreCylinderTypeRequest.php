<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCylinderTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cylinderTypeId = $this->route('cylinder_type');
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cylinder_types')->ignore($cylinderTypeId?->id),
            ],
            'size_kg' => 'required|integer|min:1',
            'full_sale_cost' => 'required|numeric|min:0',
            'full_sale_price' => 'required|numeric|min:0',
            'refill_cost' => 'required|numeric|min:0',
            'refill_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }
}