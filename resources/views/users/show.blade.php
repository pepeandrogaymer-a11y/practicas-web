@extends('layouts.app')

@section('title', 'Detalle de usuario')

@section('content')
<div class="max-w-lg mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-6">Detalle del usuario</h1>

    <dl class="space-y-3 mb-6">
        <div><dt class="text-sm text-gray-500">ID</dt><dd>{{ $user->id }}</dd></div>
        <div><dt class="text-sm text-gray-500">Nombre</dt><dd>{{ $user->name }}</dd></div>
        <div><dt class="text-sm text-gray-500">Correo</dt><dd>{{ $user->email }}</dd></div>
        <div><dt class="text-sm text-gray-500">Rol</dt><dd>{{ $user->role }}</dd></div>
        <div><dt class="text-sm text-gray-500">Registrado</dt><dd>{{ $user->created_at->format('d/m/Y H:i') }}</dd></div>
    </dl>

    <a href="{{ route('usuarios.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Volver</a>
</div>
@endsection