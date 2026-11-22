@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="max-w-5xl mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Usuarios</h1>
        <div class="flex gap-2">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Inicio</a>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('usuarios.trashed') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Papelera</a>
            @endif
            <a href="{{ route('usuarios.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Nuevo usuario</a>
        </div>
    </div>

    @include('users._status')

    <table class="w-full text-left">
        <thead>
            <tr class="border-b">
                <th class="py-2">Nombre</th>
                <th class="py-2">Correo</th>
                <th class="py-2">Rol</th>
                <th class="py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr class="border-b">
                    <td class="py-2">{{ $user->name }}</td>
                    <td class="py-2">{{ $user->email }}</td>
                    <td class="py-2">{{ $user->role }}</td>
                    <td class="py-2">
                        <div class="flex gap-2 items-center">
                            <a href="{{ route('usuarios.show', $user) }}" class="text-blue-600 hover:underline">Ver</a>

                            @if (auth()->user()->isAdmin() || ! $user->isAdmin())
                                <a href="{{ route('usuarios.edit', $user) }}" class="text-yellow-600 hover:underline">Editar</a>
                            @endif

                            {{-- Los botones de eliminar solo existen para el admin --}}
                            @if (auth()->user()->isAdmin() && $user->id !== auth()->id())
                                <form method="POST" action="{{ route('usuarios.destroy', $user) }}"
                                      onsubmit="return confirm('¿Deshabilitar a este usuario? Podrás recuperarlo después.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-orange-600 hover:underline">Deshabilitar</button>
                                </form>

                                <form method="POST" action="{{ route('usuarios.force-delete', $user) }}"
                                      onsubmit="return confirm('¿Eliminar PERMANENTEMENTE? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar permanente</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $users->links() }}</div>
</div>
@endsection