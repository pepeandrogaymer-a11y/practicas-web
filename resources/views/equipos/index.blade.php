@extends('layouts.app')

@section('title', 'Equipos')

@section('content')
@php $esAdmin = auth()->user()->isAdmin(); @endphp
<div class="max-w-5xl mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ $esAdmin ? 'Equipos' : 'Equipos disponibles' }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Inicio</a>
            @if ($esAdmin)
                <a href="{{ route('reservaciones.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Reservaciones</a>
                <a href="{{ route('equipos.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Nuevo equipo</a>
            @else
                <a href="{{ route('reservaciones.mias') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Mis reservaciones</a>
            @endif
        </div>
    </div>

    @include('users._status')

    <table class="w-full text-left">
        <thead>
            <tr class="border-b">
                <th class="py-2">Equipo</th>
                <th class="py-2">Descripción</th>
                <th class="py-2">Estado</th>
                @if ($esAdmin)
                    <th class="py-2">Reservado por</th>
                @endif
                <th class="py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($computers as $computer)
                @php $reserva = $computer->activeReservation; @endphp
                <tr class="border-b">
                    <td class="py-2">{{ $computer->nombre }}</td>
                    <td class="py-2">{{ $computer->descripcion ?? '—' }}</td>
                    <td class="py-2"><x-status-badge :estado="$reserva ? 'Reservado' : 'Disponible'" /></td>
                    @if ($esAdmin)
                        <td class="py-2">{{ $reserva ? ($reserva->user?->name ?? 'Usuario deshabilitado') : '—' }}</td>
                    @endif
                    <td class="py-2">
                        <div class="flex gap-2 items-center">
                            @if ($esAdmin)
                                <a href="{{ route('equipos.edit', $computer) }}" class="text-yellow-600 hover:underline">Editar</a>
                                <form method="POST" action="{{ route('equipos.destroy', $computer) }}"
                                      onsubmit="return confirm('¿Eliminar este equipo? Se borrará también su historial de reservaciones.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @elseif (! $reserva)
                                <form method="POST" action="{{ route('equipos.reservar', $computer) }}">
                                    @csrf
                                    <button class="text-blue-600 hover:underline">Reservar</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-4 text-gray-500">No hay equipos disponibles en este momento.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $computers->links() }}</div>
</div>
@endsection