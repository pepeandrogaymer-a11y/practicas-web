@extends('layouts.app')

@section('title', 'Nuevo equipo')

@section('content')
<div class="max-w-lg mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-6">Nuevo equipo</h1>

    <form method="POST" action="{{ route('equipos.store') }}" novalidate>
        @csrf
        @include('equipos._form')

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Guardar</button>
            <a href="{{ route('equipos.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Cancelar</a>
        </div>
    </form>
</div>
@endsection