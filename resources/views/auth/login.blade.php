@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
        <h1 class="text-2xl font-bold mb-6 text-center">Iniciar sesión</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium mb-1">Correo electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="w-full border rounded px-3 py-2" required autofocus>
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium mb-1">Contraseña</label>
                <input type="password" id="password" name="password"
                       class="w-full border rounded px-3 py-2" required>
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Entrar
            </button>
        </form>
    </div>
</div>
@endsection