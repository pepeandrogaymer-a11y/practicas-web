<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MoveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el acceso lo controlan el middleware y la lógica del controlador
    }

    public function rules(): array
    {
        return [
            'posicion' => ['required', 'integer', 'between:0,8'],
        ];
    }
}
