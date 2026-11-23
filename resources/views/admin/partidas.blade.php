@extends('layouts.app')

@section('title', 'Partidas')

@section('content')
<div class="max-w-5xl mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Partidas del gato</h1>
        <div class="flex gap-2">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Inicio</a>
            <a href="{{ route('usuarios.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Usuarios</a>
        </div>
    </div>

    @include('users._status')

    <form method="GET" action="{{ route('admin.partidas.index') }}" class="flex items-end gap-2 mb-6">
        <div>
            <label for="estado" class="block text-sm font-medium mb-1">Estado</label>
            <select id="estado" name="estado" class="border rounded px-3 py-2">
                <option value="">Todas</option>
                @foreach (\App\Models\Game::ETIQUETAS as $valor => $texto)
                    <option value="{{ $valor }}" @selected($estado === $valor)>{{ $texto }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filtrar</button>
        <a href="{{ route('admin.partidas.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Limpiar</a>
    </form>

    <table class="w-full text-left">
        <thead>
            <tr class="border-b">
                <th class="py-2">ID</th>
                <th class="py-2">X</th>
                <th class="py-2">O</th>
                <th class="py-2">Estado</th>
                <th class="py-2">Resultado</th>
                <th class="py-2">Fecha</th>
                <th class="py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($games as $game)
                <tr class="border-b">
                    <td class="py-2">{{ $game->id }}</td>
                    <td class="py-2">{{ $game->playerX?->name ?? '—' }}</td>
                    <td class="py-2">{{ $game->playerO?->name ?? '—' }}</td>
                    <td class="py-2">{{ \App\Models\Game::ETIQUETAS[$game->status] }}</td>
                    <td class="py-2">
                        @if ($game->resultado === 'empate')
                            Empate
                        @elseif ($game->winner)
                            Ganó {{ $game->winner->name }}{{ $game->resultado === 'abandono' ? ' (abandono)' : '' }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="py-2">{{ $game->created_at->format('d/m/Y H:i') }}</td>
                    <td class="py-2">
                        <div class="flex gap-2 items-center">
                            <a href="{{ route('juego.show', $game) }}" class="text-blue-600 hover:underline">Ver</a>

                            @if (in_array($game->status, ['esperando', 'en_curso'], true))
                                <form method="POST" action="{{ route('admin.partidas.cancel', $game) }}"
                                      onsubmit="return confirm('¿Cancelar esta partida?')">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-red-600 hover:underline">Cancelar</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-4 text-gray-500">No hay partidas.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $games->links() }}</div>
</div>
@endsection