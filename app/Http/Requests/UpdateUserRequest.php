<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            // En edición la contraseña es opcional
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ];

        if ($this->user()->isAdmin()) {
            $rules['role'] = ['required', Rule::in(['admin', 'empleado'])];
        }

        return $rules;
    }
}
