@extends('layouts.app')

@section('title', 'Juego del gato')

@section('content')
<div class="max-w-4xl mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Juego del gato</h1>
        <div class="flex gap-2">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Inicio</a>
            <a href="{{ route('juego.historial') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Mi historial</a>
        </div>
    </div>

    @include('users._status')

    @if ($myGame)
        <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-6">
            <p class="font-semibold mb-1">Tienes una partida activa (#{{ $myGame->id }})</p>
            <p class="text-sm text-gray-600 mb-3">
                X: {{ $myGame->playerX?->name ?? 'Jugador eliminado' }}
                · O: {{ $myGame->playerO?->name ?? 'esperando oponente' }}
            </p>
            <a href="{{ route('juego.show', $myGame) }}"
               class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Continuar partida</a>
        </div>
    @else
        <form method="POST" action="{{ route('juego.store') }}" class="mb-6">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Crear nueva partida
            </button>
        </form>
    @endif

    <h2 class="text-lg font-semibold mb-2">Partidas disponibles para unirse</h2>
    <table class="w-full text-left mb-8">
        <thead>
            <tr class="border-b">
                <th class="py-2">Partida</th>
                <th class="py-2">Creada por</th>
                <th class="py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($waiting as $game)
                <tr class="border-b">
                    <td class="py-2">#{{ $game->id }}</td>
                    <td class="py-2">{{ $game->playerX?->name ?? 'Jugador eliminado' }}</td>
                    <td class="py-2">
                        @if (! $myGame)
                            <form method="POST" action="{{ route('juego.join', $game) }}">
                                @csrf
                                <button class="text-blue-600 hover:underline">Unirse</button>
                            </form>
                        @else
                            <span class="text-gray-400 text-sm">Termina tu partida actual para unirte</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="py-4 text-gray-500">No hay partidas esperando oponente.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2 class="text-lg font-semibold mb-2">Jugadores disponibles</h2>
    @if ($available->isEmpty())
        <p class="text-gray-500">No hay otros jugadores disponibles en este momento.</p>
    @else
        <ul class="list-disc list-inside">
            @foreach ($available as $player)
                <li>{{ $player->name }}</li>
            @endforeach
        </ul>
    @endif
</div>

<script>
    // La sala se actualiza sola cada 5 segundos
    setTimeout(() => location.reload(), 5000);
</script>
@endsection