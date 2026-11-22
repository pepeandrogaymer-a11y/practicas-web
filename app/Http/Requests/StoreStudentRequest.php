<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el acceso por rol lo controla el middleware
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'faltas' => ['required', 'integer', 'min:0', 'max:' . config('academico.max_faltas')],
            'calificacion' => [
                'required', 'numeric',
                'min:' . config('academico.min_calificacion'),
                'max:' . config('academico.max_calificacion'),
            ],
        ];
    }
}
