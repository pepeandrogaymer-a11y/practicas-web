@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="max-w-2xl mx-auto mt-16 bg-white p-8 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-2">Bienvenido, {{ auth()->user()->name }}</h1>
    <p class="mb-6 text-gray-600">Tu rol es: <strong>{{ auth()->user()->role }}</strong></p>

<a href="{{ route('usuarios.index') }}"
   class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4">
    Administrar usuarios
</a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
            Cerrar sesión
        </button>
    </form>
</div>
@endsection