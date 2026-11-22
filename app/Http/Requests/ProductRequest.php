<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProductRequest extends FormRequest
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
                // Al editar, se ignora el propio producto
                Rule::unique('products', 'nombre')->ignore($this->route('product')),
            ],
            'descripcion' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'precio' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'existencias' => ['required', 'integer', 'min:0'],
            'estado' => ['required', Rule::in(Product::ESTADOS)],
        ];
    }

    // Regla de consistencia: sin existencias no puede estar "Disponible"
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->input('estado') === 'Disponible' && (int) $this->input('existencias') === 0) {
                    $validator->errors()->add('estado', 'Un producto con 0 existencias no puede estar Disponible.');
                }
            },
        ];
    }
}