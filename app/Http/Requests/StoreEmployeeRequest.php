<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'id_number' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'role_title' => 'nullable|string|max:255',
            'outlet_id' => 'nullable|exists:outlets,id',
            'hire_date' => 'nullable|date',
            'basic_salary' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,terminated',
        ];
    }
}