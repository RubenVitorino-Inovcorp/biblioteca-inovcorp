<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'street' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'regex:/^\d{4}-\d{3}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'street.required' => 'A rua é obrigatória.',
            'street.string' => 'A rua deve ser texto.',
            'street.max' => 'A rua não pode exceder 255 caracteres.',
            'city.required' => 'A cidade é obrigatória.',
            'city.string' => 'A cidade deve ser texto.',
            'city.max' => 'A cidade não pode exceder 100 caracteres.',
            'postal_code.required' => 'O código postal é obrigatório.',
            'postal_code.string' => 'O código postal deve ser texto.',
            'postal_code.regex' => 'O código postal deve ter o formato 0000-000.',
        ];
    }

    public function attributes(): array
    {
        return [
            'street' => 'Rua',
            'city' => 'Cidade',
            'postal_code' => 'Código Postal',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->postal_code) {
            $this->merge([
                'postal_code' => str_replace(' ', '', $this->postal_code),
            ]);
        }
    }
}
