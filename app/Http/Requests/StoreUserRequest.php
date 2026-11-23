<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el acceso por rol ya lo controla el middleware
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];

             // Solo el admin puede elegir el rol
        if ($this->user()->isAdmin()) {
            $rules['role'] = ['required', Rule::in(['admin', 'empleado', 'jugador'])];
        }

        return $rules;
    }
}
