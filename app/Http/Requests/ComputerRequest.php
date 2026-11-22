<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ComputerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el acceso por rol lo controla el middleware
    }

    public function rules(): array
    {
        return [
            'nombre' => [
                'required', 'string', 'max:255',
                Rule::unique('computers', 'nombre')->ignore($this->route('computer')),
            ],
            'descripcion' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
