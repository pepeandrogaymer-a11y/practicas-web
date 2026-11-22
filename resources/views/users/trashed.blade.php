@extends('layouts.app')

@section('title', 'Papelera')

@section('content')
<div class="max-w-5xl mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Papelera de usuarios</h1>
        <a href="{{ route('usuarios.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Volver a usuarios</a>
    </div>

    @include('users._status')

    <table class="w-full text-left">
        <thead>
            <tr class="border-b">
                <th class="py-2">Nombre</th>
                <th class="py-2">Correo</th>
                <th class="py-2">Deshabilitado el</th>
                <th class="py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr class="border-b">
                    <td class="py-2">{{ $user->name }}</td>
                    <td class="py-2">{{ $user->email }}</td>
                    <td class="py-2">{{ $user->deleted_at->format('d/m/Y H:i') }}</td>
                    <td class="py-2">
                        <div class="flex gap-2 items-center">
                            <form method="POST" action="{{ route('usuarios.restore', $user) }}">
                                @csrf
                                @method('PATCH')
                                <button class="text-green-600 hover:underline">Restaurar</button>
                            </form>

                            <form method="POST" action="{{ route('usuarios.force-delete', $user) }}"
                                  onsubmit="return confirm('¿Eliminar PERMANENTEMENTE? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">Eliminar permanente</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-4 text-gray-500">La papelera está vacía.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $users->links() }}</div>
</div>
@endsection