@extends('layouts.app')

@section('title', 'Mi historial')

@section('content')
<div class="max-w-4xl mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Mi historial de partidas</h1>
        <div class="flex gap-2">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Inicio</a>
            <a href="{{ route('juego.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Sala de juego</a>
        </div>
    </div>

    <table class="w-full text-left">
        <thead>
            <tr class="border-b">
                <th class="py-2">Partida</th>
                <th class="py-2">Oponente</th>
                <th class="py-2">Mi símbolo</th>
                <th class="py-2">Resultado</th>
                <th class="py-2">Fecha</th>
                <th class="py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($games as $game)
                <tr class="border-b">
                    <td class="py-2">#{{ $game->id }}</td>
                    <td class="py-2">{{ $game->opponentFor(auth()->user())?->name ?? '—' }}</td>
                    <td class="py-2">{{ $game->symbolFor(auth()->user()) }}</td>
                    <td class="py-2">{{ $game->resultLabelFor(auth()->user()) }}</td>
                    <td class="py-2">{{ $game->finished_at?->format('d/m/Y H:i') }}</td>
                    <td class="py-2">
                        <a href="{{ route('juego.show', $game) }}" class="text-blue-600 hover:underline">Ver</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-4 text-gray-500">Todavía no has terminado ninguna partida.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $games->links() }}</div>
</div>
@endsection