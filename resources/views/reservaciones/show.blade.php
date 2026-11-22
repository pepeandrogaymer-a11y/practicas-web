@extends('layouts.app')

@section('title', 'Detalle de reservación')

@section('content')
<div class="max-w-lg mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-6">Detalle de la reservación</h1>

    <dl class="space-y-3 mb-6">
        <div><dt class="text-sm text-gray-500">ID</dt><dd>{{ $reservation->id }}</dd></div>
        <div><dt class="text-sm text-gray-500">Equipo</dt><dd>{{ $reservation->computer->nombre }}</dd></div>
        <div><dt class="text-sm text-gray-500">Usuario</dt><dd>{{ $reservation->user?->name ?? 'Usuario deshabilitado' }}</dd></div>
        <div><dt class="text-sm text-gray-500">Estado</dt><dd>{{ ucfirst($reservation->estado) }}</dd></div>
        <div><dt class="text-sm text-gray-500">Reservada el</dt><dd>{{ $reservation->created_at->format('d/m/Y H:i') }}</dd></div>
        @if ($reservation->cancelled_at)
            <div><dt class="text-sm text-gray-500">Cancelada el</dt><dd>{{ $reservation->cancelled_at->format('d/m/Y H:i') }}</dd></div>
        @endif
    </dl>

    <a href="{{ url()->previous() }}" class="px-4 py-2 border rounded hover:bg-gray-50">Volver</a>
</div>
@endsection