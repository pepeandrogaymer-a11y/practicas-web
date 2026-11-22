<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($this->route('student')->user_id),
            ],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'faltas' => ['required', 'integer', 'min:0', 'max:' . config('academico.max_faltas')],
            'calificacion' => [
                'required', 'numeric',
                'min:' . config('academico.min_calificacion'),
                'max:' . config('academico.max_calificacion'),
            ],
        ];
    }
}
