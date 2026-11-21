@extends('layouts.app')

@section('title', 'Verificación')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
        <h1 class="text-2xl font-bold mb-2 text-center">Verificación</h1>
        <p class="text-sm text-gray-600 mb-6 text-center">
            Te enviamos un código de 6 dígitos a tu correo. Vence en 5 minutos.
        </p>

        <form method="POST" action="{{ $verifyUrl }}">
            @csrf

            <div class="mb-6">
                <label for="code" class="block text-sm font-medium mb-1">Código</label>
                <input type="text" id="code" name="code" maxlength="6" inputmode="numeric"
                       class="w-full border rounded px-3 py-2 text-center tracking-widest" required autofocus>
                @error('code')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Verificar
            </button>
        </form>
    </div>
</div>
@endsection