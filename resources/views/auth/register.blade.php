@extends('layouts.app')

@section('title', 'Registro')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
        <h1 class="text-2xl font-bold mb-6 text-center">Crear cuenta</h1>

        <form method="POST" action="{{ route('registro') }}" novalidate>
            @csrf

            <x-form-field name="name" label="Nombre" />
            <x-form-field name="email" label="Correo electrónico" type="email" />
            <x-form-field name="password" label="Contraseña" type="password" />
            <x-form-field name="password_confirmation" label="Confirmar contraseña" type="password" />

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Registrarme
            </button>
        </form>

        <p class="text-sm text-center mt-4">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Inicia sesión</a>
        </p>
    </div>
</div>
@endsection