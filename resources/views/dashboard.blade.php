@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="max-w-2xl mx-auto mt-16 bg-white p-8 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-2">Bienvenido, {{ auth()->user()->name }}</h1>
    <p class="mb-6 text-gray-600">Tu rol es: <strong>{{ auth()->user()->role }}</strong></p>

    <div class="flex flex-wrap gap-2">
        @if (in_array(auth()->user()->role, ['admin', 'empleado']))
            <a href="{{ route('usuarios.index') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Administrar usuarios</a>
            <a href="{{ route('productos.index') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Productos</a>
        @endif

        @if (auth()->user()->isAdmin())
            <a href="{{ route('equipos.index') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Equipos</a>
            <a href="{{ route('reservaciones.index') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Reservaciones</a>
        @endif

        @if (auth()->user()->isProfesor())
            <a href="{{ route('alumnos.index') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Administrar alumnos</a>
        @endif

        @if (auth()->user()->isAlumno())
            <a href="{{ route('mi-informacion') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Mi información académica</a>
        @endif

        @if (auth()->user()->isEstudiante())
            <a href="{{ route('equipos.index') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Equipos disponibles</a>
            <a href="{{ route('reservaciones.mias') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Mis reservaciones</a>
        @endif
    </div>
</div>
@endsection