@props(['campo', 'titulo'])

@php
    $activo = request('orden', 'nombre') === $campo;
    $dirActual = request('dir') === 'desc' ? 'desc' : 'asc';
    $siguiente = $activo && $dirActual === 'asc' ? 'desc' : 'asc';
@endphp

<a href="{{ route('alumnos.index', ['orden' => $campo, 'dir' => $siguiente]) }}" class="hover:underline">
    {{ $titulo }}
    @if ($activo) {{ $dirActual === 'asc' ? '▲' : '▼' }} @endif
</a>