@extends('layouts.app')

@section('title', $todas ? 'Reservaciones' : 'Mis reservaciones')

@section('content')
<div class="max-w-5xl mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ $todas ? 'Todas las reservaciones' : 'Mis reservaciones' }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Inicio</a>
            <a href="{{ route('equipos.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Equipos</a>
        </div>
    </div>

    @include('users._status')

    <table class="w-full text-left">
        <thead>
            <tr class="border-b">
                <th class="py-2">ID</th>
                @if ($todas)
                    <th class="py-2">Usuario</th>
                @endif
                <th class="py-2">Equipo</th>
                <th class="py-2">Fecha</th>
                <th class="py-2">Estado</th>
                <th class="py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reservations as $reservation)
                <tr class="border-b">
                    <td class="py-2">{{ $reservation->id }}</td>
                    @if ($todas)
                        <td class="py-2">{{ $reservation->user?->name ?? 'Usuario deshabilitado' }}</td>
                    @endif
                    <td class="py-2">{{ $reservation->computer->nombre }}</td>
                    <td class="py-2">{{ $reservation->created_at->format('d/m/Y H:i') }}</td>
                    <td class="py-2">
                        <span class="px-2 py-1 rounded text-sm font-semibold
                            {{ $reservation->isActive() ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                            {{ ucfirst($reservation->estado) }}
                        </span>
                    </td>
                    <td class="py-2">
                        <div class="flex gap-2 items-center">
                            <a href="{{ route('reservaciones.show', $reservation) }}" class="text-blue-600 hover:underline">Ver</a>

                            {{-- El botón solo aparece si la política permite cancelar --}}
                            @can('cancel', $reservation)
                                <form method="POST" action="{{ route('reservaciones.cancel', $reservation) }}"
                                      onsubmit="return confirm('¿Cancelar esta reservación?')">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-red-600 hover:underline">Cancelar</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-4 text-gray-500">No hay reservaciones.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $reservations->links() }}</div>
</div>
@endsection